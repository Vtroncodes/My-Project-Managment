<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Models\Project;
use App\Models\User;
use App\Models\Attachment;
use Filament\Forms;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\DB;
use Filament\Tables;
use Forms\Components\Text;
use Filament\Forms\Components\FileUpload;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\Comment;
use Parallax\FilamentComments\Forms\Components\Comments;
use Forms\Components\Repeater;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-s-sparkles';

    public static function form(Forms\Form $form): Forms\Form
{
    return $form->schema([
        // First Row
        Forms\Components\TextInput::make('project_name')
            ->label('Project Name')
            ->required()
            ->placeholder('Enter the project name here')
            ->prefixIcon('heroicon-o-clipboard')
            ->columnSpan(4), // Takes 4/12 columns

        Forms\Components\Select::make('owner_id')
            ->label('Author')
            ->options(fn() => User::whereIn('role', ['manager', 'admin', 'client'])->pluck('name', 'id'))
            ->searchable()
            ->required()
            ->columnSpan(4), // Takes 4/12 columns

        Forms\Components\TextInput::make('description')
            ->label('Project Description')
            ->required()
            ->placeholder('Enter the project description here')
            ->columnSpan(4), // Takes 4/12 columns

        // Second Row
        Forms\Components\DatePicker::make('start_date')
            ->label('Start Date')
            ->default(now()->toDateString())
            ->format('Y-m-d')
            ->columnSpan(3), // Takes 3/12 columns

        Forms\Components\DatePicker::make('end_date')
            ->label('End Date')
            ->format('Y-m-d')
            ->columnSpan(3), // Takes 3/12 columns

        Forms\Components\Select::make('status')
            ->label('Status')
            ->options(function () {
                $column = DB::select("SHOW COLUMNS FROM projects WHERE Field = 'status'");
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
            ->columnSpan(3), // Takes 3/12 columns

        Forms\Components\TextInput::make('email_url')
            ->label('Email URL')
            ->placeholder('Enter the email URL here')
            ->columnSpan(3), // Takes 3/12 columns

        // Third Row
        Forms\Components\FileUpload::make('file_attachment_id')
            ->label('File Attachment')
            ->disk('project_uploads_dir')
            ->visibility('public')
            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/msword'])
            ->columnSpan(12), // Takes 6/12 columns

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
            ->columnSpan(12), // Takes 6/12 columns
    ])->columns(12); // Define the grid system with 12 columns
}


    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID')->sortable(),
                Tables\Columns\TextColumn::make('project_name')->label('Project Name')->searchable(),
                Tables\Columns\TextColumn::make('description')->label('Description')->limit(50),
                Tables\Columns\TextColumn::make('status')->label('Status')->sortable(),
                Tables\Columns\TextColumn::make('owner.name')->label('Owner')->sortable(),

                Tables\Columns\TextColumn::make('comments')
                    ->label('Latest Comment')
                    ->sortable()
                    ->getStateUsing(function ($record) {
                        // Get the latest comment for the project (or task)
                        $latestComment = $record->comments()->latest()->first();
                        return $latestComment ? $latestComment->content : 'No comments';
                    }),
                Tables\Columns\TextColumn::make('created_at')->label('Created At')->dateTime(),
                Tables\Columns\TextColumn::make('updated_at')->label('Updated At')->dateTime(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}
