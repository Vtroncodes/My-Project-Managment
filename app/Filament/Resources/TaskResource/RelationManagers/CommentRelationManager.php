<?php

namespace App\Filament\Resources\TaskResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Forms;
use Filament\Tables;

class CommentsRelationManager extends RelationManager
{
    protected static string $relationship = 'comments';

    public function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\RichEditor::make('content')
                ->required()
                ->label('Comment')
                ->placeholder('Enter your comment here')
                ->columns(4),  // No comma after columns
            Forms\Components\Hidden::make('user_id')
                ->default(fn() => auth()->id()),
        ]);
    }

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->label('Author'),
                Tables\Columns\TextColumn::make('content')->label('Comment'),
                Tables\Columns\TextColumn::make('created_at')->label('Date')->dateTime(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(), // Ensure the Create Action is here
            ])->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
    protected function canCreate(): bool
    {
        return true;
    }
}
