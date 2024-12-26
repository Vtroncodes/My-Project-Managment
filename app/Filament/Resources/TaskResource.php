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
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DateTimePicker;
use App\Filament\Resources\TaskResource\Pages;
use App\Filament\Resources\TaskResource\RelationManagers\CommentsRelationManager;
use App\Filament\Resources\TaskResource\RelationManagers\WorkLogsRelationManager;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static ?string $navigationIcon = 'heroicon-s-briefcase';

    // Define the form for creating and editing tasks
    public static function form(Form $form): Form
    {
        $inputClass = 'bg-blue-100 border-2 border-blue-500 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-blue-500';
        $fileClass = 'bg-gray-100 border-2 border-gray-500 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-blue-500';

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
                            ->columnSpan(6)
                            ->extraAttributes(['class' => $inputClass]),

                        Forms\Components\Select::make('category_id')
                            ->label('Category')
                            ->options(self::getHierarchicalCategories())
                            ->searchable()
                            ->required()
                            ->columnSpan(6)
                            ->extraAttributes(['class' => $inputClass]),
                    ]),

                // Second Row
                Forms\Components\Grid::make(12)
                    ->schema([
                        Forms\Components\TextInput::make('description')
                            ->label('Task Description')
                            ->required()
                            ->columnSpan(6)
                            ->extraAttributes(['class' => $inputClass]),

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
                            ->columnSpan(6)
                            ->extraAttributes(['class' => $inputClass]),
                    ]),

                // Third Row
                Forms\Components\Grid::make(12)
                    ->schema([
                        Forms\Components\Select::make('assignee_id')
                            ->label('Assign To')
                            ->options(User::all()->pluck('name', 'id')->toArray())
                            ->searchable()
                            ->required()
                            ->columnSpan(6)
                            ->extraAttributes(['class' => $inputClass]),

                        Forms\Components\DatePicker::make('due_date')
                            ->label('Due Date')
                            ->default(now()->toDateString())
                            ->format('Y-m-d')
                            ->columnSpan(6)
                            ->extraAttributes(['class' => $inputClass]),
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
                            ->extraAttributes(['class' => $fileClass]),
                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [

            WorklogsRelationManager::class,
            CommentsRelationManager::class,
        ];
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
}
