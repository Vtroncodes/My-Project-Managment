<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WorkLogResource\Pages;
use App\Filament\Resources\WorkLogResource\RelationManagers;
use App\Models\WorkLog;
use Illuminate\Support\Facades\DB;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Task;
use App\Models\User;
use App\Models\Project;
use App\Models\Category;

class WorkLogResource extends Resource
{
    protected static ?string $model = WorkLog::class;

    protected static ?string $navigationIcon = 'heroicon-s-clock';

    public static function form(Form $form): Form
    {
        $inputClass = 'bg-blue-100 border-2 border-blue-500 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-blue-500';
        $fileClass = 'bg-gray-100 border-2 border-gray-500 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-blue-500';
        return $form
            ->schema([
                // Dropdown for selecting a project
                Forms\Components\Select::make('project_id')
                    ->label('Project')
                    ->options(Project::all()->pluck('project_name', 'id'))  // All projects
                    ->searchable()
                    ->required()
                    ->reactive()  // Make this field reactive to update tasks
                    ->afterStateUpdated(function (callable $set) {
                        $set('task_id', null);  // Clear the task_id when the project is changed

                    })
                    ->extraAttributes(['class' => $inputClass]),


                // Dropdown for tasks, which is reactive based on selected project
                Forms\Components\Select::make('task_id')
                    ->label('Task')
                    ->options(function (callable $get) {
                        $projectId = $get('project_id');  // Get selected project ID
                        if (!$projectId) {
                            return [];
                        }
                        return Task::where('project_id', $projectId)

                            ->pluck('description', 'id');
                    })
                    ->searchable()
                    ->required()
                    ->extraAttributes(['class' => $inputClass]),

                // Hidden field for assignee_id, defaulting to the current user's ID
                Forms\Components\Hidden::make('assignee_id')
                    ->default(fn() => auth()->id())
                    ->required(),

                Forms\Components\TextInput::make('assignee_name')
                    ->label('Assignee')
                    ->default(fn() => auth()->user()->name)
                    ->disabled(), // Make the field read-only

                // Textarea for entering hours logged
                Forms\Components\Textarea::make('hours')
                    ->label('Hours')
                    ->required()
                    ->extraAttributes(['class' => $inputClass]),

                // Textarea for entering a description
                Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->nullable()
                    ->extraAttributes(['class' => $fileClass]),

                // Select field for status
                Forms\Components\Select::make('status')
                    ->label('Task Status')
                    ->options([
                        'Pending' => 'Pending',
                        'In Progress' => 'In Progress',
                        'Completed' => 'Completed',
                    ])
                    ->searchable()
                    ->required()
                    ->extraAttributes(['class' => $inputClass]),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('task.project.project_name')
                    ->label('Project')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('task.description')->label('Task')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('hours')->label('Hours')->wrap(),
                Tables\Columns\TextColumn::make('description')->label('Description')->wrap(),
                Tables\Columns\TextColumn::make('task.status')->label('Status')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->label('Logged At')->dateTime(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options([
                    'Pending' => 'Pending',
                    'In Progress' => 'In Progress',
                    'Completed' => 'Completed',
                ]),
            ]);
    }


    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWorkLogs::route('/'),
            'create' => Pages\CreateWorkLog::route('/create'),
            'edit' => Pages\EditWorkLog::route('/{record}/edit'),
        ];
    }
}
