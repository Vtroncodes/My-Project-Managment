<?php

namespace App\Filament\Resources\TaskResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\FileUpload;
use App\Models\Attachment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AttachmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'attachments'; // Matches the relationship in the Project model

    public function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                FileUpload::make('file_url')
                    ->label('Attachment')
                    ->directory('uploads/task_uploads') // Specify directory for uploads
                    ->acceptedFileTypes(['application/pdf', 'image/*', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/msword'])
                    ->preserveFilenames()
                    ->required()
                    ->columnSpan(12),
            ]);
    }

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('file_url')
                    ->label('File URL')
                    ->url(fn($record) => asset('storage/' . $record->file_url)), // Link to the file
                Tables\Columns\TextColumn::make('file_type')
                    ->label('File Type'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Uploaded On')
                    ->dateTime(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(), // Allow adding new attachments
            ])
            ->actions([
                Tables\Actions\EditAction::make(), // Allow editing attachments
                Tables\Actions\DeleteAction::make(), // Allow deleting attachments
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(), // Allow bulk deletion
            ]);
    }
}
