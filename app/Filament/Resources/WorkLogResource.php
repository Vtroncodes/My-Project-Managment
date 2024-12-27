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
use LaraZeus\Quantity\Components\Quantity;

class WorkLogResource extends Resource
{
    protected static ?string $model = WorkLog::class;

    protected static ?string $navigationIcon = 'heroicon-s-clock';

    public static function form(Form $form): Form
    {
       
        return $form
            ->schema([
                // Grid for project and task dropdown (2x2)
                Forms\Components\Grid::make(12)
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
                            ->columnSpan(6),  // Take up half the width
                           

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
                            ->columnSpan(6),
                    ]),

                // Grid for assignee and task status (3x3)
                Forms\Components\Grid::make(12)
                    ->schema([
                        // Hidden field for assignee_id, defaulting to the current user's ID
                        Forms\Components\Hidden::make('assignee_id')
                            ->default(fn() => auth()->id())
                            ->required(),

                        
                        Forms\Components\TextInput::make('assignee_name')
                            ->label('Assignee')
                            ->default(fn() => auth()->user()->name)
                            ->disabled() // Make the field read-only
                            ->columnSpan(4) ,

                        // Select field for task status
                        Forms\Components\Select::make('status')
                            ->label('Task Status')
                            ->options([
                                'Pending' => 'Pending',
                                'In Progress' => 'In Progress',
                                'Completed' => 'Completed',
                            ])
                            ->searchable()
                            ->required()
                            ->columnSpan(4),
                    ]),

                // Textarea for entering hours logged (stacked and custom styling)
                Quantity::make('hours')
                    ->label('Hours')
                    ->heading('Enter Hours...')
                    ->default(1)
                    ->minValue(0)
                    ->stacked()
                    ->steps(1)
                    ->extraAttributes(['class' => 'border-2 border-blue-500 p-4 rounded-md shadow-lg'])
                    ->columnSpan(4),  // Take up a third of the width

                // RichEditor for task description (full-width)
                Forms\Components\RichEditor::make('description')
                    ->label('Description')
                    ->nullable()
                    ->columnSpan(12),
            ]);;
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
