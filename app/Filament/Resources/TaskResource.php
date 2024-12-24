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

use Filament\Tables\Columns\TextColumn;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    // Define the form for creating and editing tasks
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('project_id')
                    ->label('Project')
                    ->options(Project::all()->pluck('project_name', 'id')->toArray())
                    ->searchable() // Added searchable to improve UX with many options
                    ->required(),

                Forms\Components\TextInput::make('description')
                    ->label('Task Description')
                    ->required(),

                Forms\Components\Select::make('priority')
                    ->label('Priority')
                    ->options(function () {
                        // Fetch the column details for the 'status' field
                        $column = DB::select("SHOW COLUMNS FROM tasks WHERE Field = 'priority'");
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
                Forms\Components\Select::make('category_id')
                    ->label('Category')
                    ->options(self::getHierarchicalCategories())
                    ->searchable()
                    ->required(),

                // New Select component for assigning tasks to users
                Forms\Components\Select::make('assignee_id')
                    ->label('Assign To')
                    ->options(User::all()->pluck('name', 'id')->toArray()) // Fetch all users
                    ->searchable()
                    ->required(), // Mark it required to ensure a user is always assigned

                Forms\Components\DatePicker::make('due_date')
                    ->label('Due date')
                    ->default(now()->toDateString())  // Optionally set the default value to today's date
                    ->format('Y-m-d'), // Format for date (adjust to your needs)

                Forms\Components\Repeater::make('comments')
                    ->label('Comments')
                    ->relationship('comments') // Use the relationship defined in the Project model
                    ->schema([
                        Forms\Components\Textarea::make('content')
                            ->required()
                            ->label('Comment Content'),
                        Forms\Components\Hidden::make('user_id')
                            ->default(fn() => auth()->id()), // Set the default value to the authenticated user's ID
                    ])
                    ->createItemButtonLabel('Add Comment'),

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
