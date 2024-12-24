<?php

namespace App\Filament\Resources;

use Illuminate\Support\Facades\DB;
use App\Filament\Resources\TaskResource\Pages;
use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;

use App\Filament\Resources\TaskResource\RelationManagers\WorkLogsRelationManager;

use Filament\Tables\Columns\TextColumn;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static ?string $navigationIcon = 'heroicon-s-briefcase';

    // Define the form for creating and editing tasks
    public static function form(Form $form): Form
{
    return $form
        ->schema([
            // First Row
            Forms\Components\Grid::make(12)
                ->schema([
                    Forms\Components\Select::make('project_id')
                        ->label('Project')
                        ->options(Project::all()->pluck('project_name', 'id')->toArray())
                        ->searchable()
                        ->required()
                        ->columnSpan(6),

                    Forms\Components\Select::make('category_id')
                        ->label('Category')
                        ->options(self::getHierarchicalCategories())
                        ->searchable()
                        ->required()
                        ->columnSpan(6),
                ]),

            // Second Row
            Forms\Components\Grid::make(12)
                ->schema([
                    Forms\Components\TextInput::make('description')
                        ->label('Task Description')
                        ->required()
                        ->columnSpan(6),

                    Forms\Components\Select::make('priority')
                        ->label('Priority')
                        ->options(function () {
                            $column = DB::select("SHOW COLUMNS FROM tasks WHERE Field = 'priority'");
                            $type = $column[0]->Type ?? null;

                            if ($type) {
                                preg_match('/enum\((.*)\)/', $type, $matches);
                                $enumValues = isset($matches[1]) ? explode(',', $matches[1]) : [];

                                return array_combine(
                                    array_map(fn($value) => trim($value, "'"), $enumValues),
                                    array_map(fn($value) => trim($value, "'"), $enumValues)
                                );
                            }

                            return [];
                        })
                        ->searchable()
                        ->required()
                        ->columnSpan(6),
                ]),

            // Third Row
            Forms\Components\Grid::make(12)
                ->schema([
                    Forms\Components\Select::make('assignee_id')
                        ->label('Assign To')
                        ->options(User::all()->pluck('name', 'id')->toArray())
                        ->searchable()
                        ->required()
                        ->columnSpan(6),

                    Forms\Components\DatePicker::make('due_date')
                        ->label('Due Date')
                        ->default(now()->toDateString())
                        ->format('Y-m-d')
                        ->columnSpan(6),
                ]),

            // Fourth Row
            Forms\Components\Grid::make(12)
                ->schema([
                    Forms\Components\FileUpload::make('file_attachment')
                        ->label('File Attachment')
                        ->disk('task_uploads_dir')
                        ->visibility('public')
                        ->acceptedFileTypes([
                            'application/pdf',
                            'image/jpeg',
                            'image/png',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'application/msword',
                        ])
                        ->columnSpan(6)
                        ->extraAttributes(['class' => 'same-height']), // Add a custom class

                    Forms\Components\Repeater::make('comments')
                        ->label('Comments')
                        ->relationship('comments')
                        ->schema([
                            Forms\Components\Textarea::make('content')
                                ->required()
                                ->label('Comment Content'),
                            Forms\Components\Hidden::make('user_id')
                                ->default(fn() => auth()->id()),
                        ])
                        ->createItemButtonLabel('Add Comment')
                        ->columnSpan(6)
                        ->extraAttributes(['class' => 'same-height']), // Add a custom class
                ]),
        ]);
}


    // Define query for fetching tasks with project relation
    protected function getTableQuery(): Builder
    {
        return Task::query()->with('project')->orderBy('project_id');
    }

    // Define the table that displays tasks
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('project.project_name')
                    ->label('Project')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('description')
                    ->label('Task Description')
                    ->wrap()
                    ->sortable()
                    ->searchable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->sortable(),

                TextColumn::make('priority')
                    ->label('Priority')
                    ->sortable(),

                TextColumn::make('assignee.name')
                    ->label('Assigned To')
                    ->sortable(),

                TextColumn::make('comments')
                    ->label('Latest Comment')
                    ->sortable()
                    ->getStateUsing(function ($record) {
                        // Get the latest comment for the project (or task)
                        $latestComment = $record->comments()->latest()->first();
                        return $latestComment ? $latestComment->content : 'No comments';
                    }),
                TextColumn::make('due_date')
                    ->label('Due Date')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('project_id')
                    ->label('Filter by Project')
                    ->options(Project::all()->pluck('project_name', 'id')->toArray()),
            ])
            ->defaultSort('project_id');
        //->paginate(10);  // Added pagination if needed
    }

    // Define the relations that can be managed (if any)
    public static function getRelations(): array
    {
        return [
            // Add any relation managers if necessary, e.g., for related tasks under projects
            WorklogsRelationManager::class,
        ];
    }

    // Define the pages for creating, editing, and listing tasks
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTasks::route('/'),  // Custom ManageTasks page for listing tasks
            'create' => Pages\CreateTask::route('/create'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
        ];
    }
    public static function getHierarchicalCategories($parentId = null, $prefix = ''): array
    {
        $categories = Category::where('parent_id', $parentId)->get();
        $options = [];

        foreach ($categories as $category) {
            $options[$category->id] = $prefix . $category->name;
            $options += self::getHierarchicalCategories($category->id, $prefix . '- ->');
        }

        return $options;
    }
}
