<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaskResource\Pages;
use App\Models\Task;
use App\Models\Project;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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
                    ->searchable()  // Added searchable to improve UX with many options
                    ->required(),

                Forms\Components\TextInput::make('description')
                    ->label('Task Description')
                    ->required(),

                Forms\Components\Select::make('priority')
                    ->label('Priority')
                    ->options([
                        'low' => 'Low',
                        'medium' => 'Medium',
                        'high' => 'High',
                    ])
                    ->required(),

                Forms\Components\Select::make('category_id')
                    ->label('Category')
                    ->options(self::getHierarchicalCategories())
                    ->searchable()
                    ->required(),
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
                Tables\Columns\TextColumn::make('project.project_name')
                    ->label('Project')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('description')
                    ->label('Task Description')
                    ->wrap()
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->sortable(),

                Tables\Columns\TextColumn::make('priority')
                    ->label('Priority')
                    ->sortable(),

                Tables\Columns\TextColumn::make('due_date')
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
