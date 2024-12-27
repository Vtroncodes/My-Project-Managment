<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Models\Project;
use App\Models\Attachment;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\TextInput;
use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\ProjectResource\RelationManagers\CommentRelationManager;
use App\Filament\Resources\ProjectResource\RelationManagers\AttachmentsRelationManager;
use App\Filament\Resources\Log;
use Filament\Forms\Components\DatePicker;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\HtmlString;
use Forms\Components\RichEditor;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-s-sparkles';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('project_name')
                ->label('Project Name')
                ->required()
                ->placeholder('Enter the project name...')
                ->prefixIcon('heroicon-o-squares-2x2')
                ->columnSpan(3),


            Forms\Components\Select::make('owner_id')
                ->label('Author')
                ->options(fn() => User::whereIn('role', ['manager', 'admin', 'client'])->pluck('name', 'id'))
                ->searchable()
                ->required()
                ->columnSpan(3),

            DatePicker::make('start_date')
                ->label('Start Date')
                ->required()
                ->columnSpan(3),

            DatePicker::make('end_date')
                ->label('End Date')
                ->required()
                ->columnSpan(3),

            Forms\Components\RichEditor::make('description')
                ->label('Description')
                ->required()
                ->columnSpan(12)


        ])->columns(12);
    }

    public static function getRelations(): array
    {
        return [
            CommentRelationManager::class,
            AttachmentsRelationManager::class,
        ];
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
