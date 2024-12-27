<?php

namespace App\Filament\Resources\ProjectResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Forms;
use Filament\Tables;


class CommentRelationManager extends RelationManager
{
    protected static string $relationship = 'comments';

    public function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\Grid::make(12)
                ->schema([
                    // RichEditor for the comment
                    Forms\Components\RichEditor::make('comment_description')
                        ->required()
                        ->label('Comment')
                        ->placeholder('Enter your comment here')
                        ->columnSpan(12),  // Adjust this to how many columns you want the editor to span
                       
                    // Hidden field for user_id
                    Forms\Components\Hidden::make('user_id')
                        ->default(fn() => auth()->id()),
                ])
        ]);
    }
    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->label('Author'),
                Tables\Columns\TextColumn::make('comment_description')->label('Comment')->html(),
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
