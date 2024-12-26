<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Task;
use App\Models\User;
use Filament\Tables;
use App\Models\Project;
use App\Models\Category;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\DateFilter;
use App\Filament\Resources\TaskResource\Pages;
use Filament\Tables\Filters\SelectFilter; // Import for SelectFilter

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static ?string $navigationIcon = 'heroicon-s-briefcase';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // First Row: Project and Category (6x6)
                Forms\Components\Grid::make(12)
                    ->schema([
                        Select::make('project_id')
                            ->label('Project')
                            ->relationship('project', 'project_name')
                            ->preload() // Preload related data
                            ->searchable()
                            ->required()
                            ->columnSpan(6)
                            ->disabled(fn ($state) => !empty($state)) // Disable if the task already has a project (i.e., editing a task)
                            ->default(fn ($state) => $state ?? null), // Pre-fill with the existing project if editing

                        Select::make('category_id')
                            ->label('Category')
                            ->options(self::getHierarchicalCategories())
                            ->searchable()
                            ->required()
                            ->columnSpan(6),
                    ]),

                // Second Row: Task Description (12)
                Forms\Components\Grid::make(12)
                    ->schema([
                        TextInput::make('description')
                            ->label('Task Description')
                            ->required()
                            ->columnSpan(12),
                    ]),

                // Third Row: Priority, Assign To, and Due Date (4x4x4)
                Forms\Components\Grid::make(12)
                    ->schema([
                        Select::make('priority')
                            ->label('Priority')
                            ->options(self::getEnumValues('tasks', 'priority'))
                            ->searchable()
                            ->required()
                            ->columnSpan(4),

                        Select::make('assignee_id')
                            ->label('Assign To')
                            ->options(User::pluck('name', 'id'))
                            ->searchable()
                            ->required()
                            ->columnSpan(4),

                        DatePicker::make('due_date')
                            ->label('Due Date')
                            ->default(now()->toDateString())
                            ->format('Y-m-d')
                            ->columnSpan(4),
                    ]),

                // Fourth Row: File Attachment (12)
                Forms\Components\Grid::make(12)
                    ->schema([
                        FileUpload::make('file_attachment')
                            ->label('File Attachment')
                            ->disk('task_uploads')
                            ->visibility('public')
                            ->acceptedFileTypes([
                                'application/pdf',
                                'image/jpeg',
                                'image/png',
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                'application/msword',
                            ])
                            ->columnSpan(12),
                    ]),
            ]);
    }

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
                    ->sortable()
                    ->searchable()
                    ->wrap(),

                TextColumn::make('priority')
                    ->label('Priority')
                    ->sortable(),

                TextColumn::make('assignee.name')
                    ->label('Assigned To')
                    ->sortable(),

                TextColumn::make('due_date')
                    ->label('Due Date')
                    ->sortable()
                    ->date(),
            ])
            ->filters([
                // Filter by Project
                Tables\Filters\SelectFilter::make('project_id')
                    ->label('Filter by Project')
                    ->options(Project::pluck('project_name', 'id')),

                // Filter by Priority
                Tables\Filters\SelectFilter::make('priority')
                    ->label('Filter by Priority')
                    ->options(TaskResource::getEnumValues('tasks', 'priority')),

                // Custom Filter for Due Date
                Tables\Filters\Filter::make('due_date')
                    ->label('Filter by Due Date') // Label for the filter
                    ->form([
                        DatePicker::make('from')->label('From Date'), // Label for "From" date picker
                        DatePicker::make('to')->label('To Date'),     // Label for "To" date picker
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['from'], fn($q) => $q->where('due_date', '>=', $data['from']))
                            ->when($data['to'], fn($q) => $q->where('due_date', '<=', $data['to']));
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['from'] ?? null) {
                            $indicators['from'] = 'From: ' . $data['from'];
                        }

                        if ($data['to'] ?? null) {
                            $indicators['to'] = 'To: ' . $data['to'];
                        }

                        return $indicators;
                    }),
            ])
            ->defaultSort('project_id');
    }

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

    public static function getEnumValues(string $table, string $column): array
    {
        $type = DB::select("SHOW COLUMNS FROM {$table} WHERE Field = ?", [$column])[0]->Type ?? null;

        if ($type && preg_match('/enum\((.*)\)/', $type, $matches)) {
            return array_combine(
                $values = array_map(fn($value) => trim($value, "'"), explode(',', $matches[1])),
                $values
            );
        }

        return [];
    }
}
