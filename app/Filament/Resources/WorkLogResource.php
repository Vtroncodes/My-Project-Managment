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

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Select::make('project_id')
                    ->label('Project')
                    ->options(Project::all()->pluck('project_name', 'id'))
                    ->searchable()
                    ->required()
                    ->reactive() // Mark this field as reactive
                    ->afterStateUpdated(function (callable $set) {
                        // Clear the task_id field when a new project is selected
                        $set('task_id', null);
                    }),
                Forms\Components\Select::make('task_id')
                    ->label('Task')
                    ->options(function (callable $get) {
                        // Get the logged-in user
                        $user = auth()->user();
                        $projectId = $get('project_id'); // Get the selected project_id

                        // Only fetch tasks if a project is selected and the task is assigned to the current user
                        if (!$projectId) {
                            return [];
                        }

                        // Filter tasks by project_id and assignee_id
                        return Task::where('project_id', $projectId)
                            ->where('assignee_id', $user->id)  // Filter by the logged-in user
                            ->pluck('description', 'id');
                    })
                    ->searchable()
                    ->required(),

                Forms\Components\Hidden::make('assignee_id')
                    ->default(auth()->user()->id),  
   
                Forms\Components\TextInput::make('assignee_name')
                    ->label('Assignee Name')
                    ->default(function () {
                        // Get the authenticated user's name based on their assignee_id
                        return auth()->user()->name ?? 'No Assignee';  // Use the authenticated user's name
                    })
                    ->readonly(),  // Make the name field read-only
                Forms\Components\Textarea::make('hours')
                    ->label('Hour Log')
                    ->required(),

                Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->nullable(),

                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options(function () {
                        // Fetch the column details for the 'status' field
                        $column = DB::select("SHOW COLUMNS FROM tasks WHERE Field = 'status'");
                        $type = $column[0]->Type ?? null;

                        if ($type) {
                            // Extract enum values from the column definition
                            preg_match('/enum\((.*)\)/', $type, $matches);
                            $enumValues = isset($matches[1]) ? explode(',', $matches[1]) : [];

                            // Clean enum values and return as options
                            return array_combine(
                                array_map(fn($value) => trim($value, "'"), $enumValues),
                                array_map(fn($value) => trim($value, "'"), $enumValues)
                            );
                        }

                        return [];
                    })
                    ->searchable()
                    ->required(),

            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('project.project_name')->label('Project')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('task.description')->label('Task')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('work_log')->label('Work Log')->wrap(),
                Tables\Columns\TextColumn::make('comment')->label('Comment')->wrap(),
                Tables\Columns\TextColumn::make('status')->label('Status')->sortable(),
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
