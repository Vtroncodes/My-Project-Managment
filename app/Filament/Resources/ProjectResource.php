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
use Filament\Forms\Components\FileUpload;
use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\ProjectResource\RelationManagers\CommentRelationManager;
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
                ->helperText(new HtmlString('<strong class="text-red-600" style="color:orange;"><sup> * </sup>in 250 Words only...</strong>'))
                ->maxLength(1250),


            FileUpload::make('file_attachment')
                ->label('Attachment')
                ->directory('uploads/project_uploads_dir')
                ->disk('public')
                ->acceptedFileTypes(['application/pdf', 'image/*'])
                ->preserveFilenames()

                ->saveUploadedFileUsing(function ($file, $state, $set, $record) {
                    if (!$file) {
                        throw new \Exception("No file uploaded.");
                    }

                    // Store the file
                    $path = $file->store('uploads/project_uploads_dir', 'public');
                    if (!$path) {
                        throw new \Exception("File could not be saved.");
                    }

                    // Save attachment entry
                    $attachment = Attachment::create([
                        'attachmentable_type' => Project::class,
                        'attachmentable_id' => $record->id ?? null,
                        'file_url' => $path,

                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // Associate attachment with project
                    if ($record) {
                        $record->file_attachment_id = $attachment->id;
                        $record->save();
                    }

                    return $path;
                })
                ->columnSpan(12),


        ])->columns(12);
        // Log::info(Storage::url('uploads/project_uploads_dir/sample.pdf'));

    }

    public static function getRelations(): array
    {
        return [
            CommentRelationManager::class,
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
                ImageColumn::make('file_url') // Assuming 'file_url' stores the image path
                    ->label('File')
                    ->disk('public') // Make sure the file is accessible via the 'public' disk
                    ->width(100) // Optional: Set the width of the displayed image
                    ->height(100), // Optional: Set the height of the displayed image
                TextColumn::make('file_url') // Assuming 'file_url' stores the file path
                    ->label('Attached File')
                    ->formatStateUsing(fn($state) => '<a href="' . asset('storage/' . $state) . '" target="_blank">Download</a>')
                    ->html(), // This ensures the link is rendered as HTML
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
