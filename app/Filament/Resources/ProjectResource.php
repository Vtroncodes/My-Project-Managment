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
use Filament\Forms\Components\FileUpload;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
  
    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('project_name')
                ->label('Project Name')
                ->required(),

            Forms\Components\Select::make('owner_id')
                ->label('Author')
                ->options(fn() => User::whereIn('role', ['manager', 'admin', 'client'])
                    ->pluck('name', 'id'))
                ->searchable()
                ->required(),


            Forms\Components\TextInput::make('description')
                ->label('Project Description')
                ->required(),

            Forms\Components\DatePicker::make('start_date')
                ->label('Start date')
                ->default(now()->toDateString())  // Optionally set the default value to today's date
                ->format('Y-m-d') // Format for date (adjust to your needs)
                ->columnSpan('sm'), // Adjust column layout as needed

            Forms\Components\DatePicker::make('end_date')
                ->label('End date')
                ->format('Y-m-d') // Format for date (adjust to your needs)
                ->columnSpan('sm'), // Adjust column layout as needed

            Forms\Components\Select::make('status')
                ->label('Status')
                ->options(function () {
                    // Fetch the column details for the 'status' field
                    $column = DB::select("SHOW COLUMNS FROM projects WHERE Field = 'status'");
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

            Forms\Components\TextInput::make('email_url')
                ->label('Email url'),     
    
            Forms\Components\FileUpload::make('file_url')
                ->label('File Attachment')
                ->disk('project_uploads_dir')             
                ->visibility('public')
                ->acceptedFileTypes(
                    array_map(function ($fileType) {
                        switch ($fileType) {
                            case 'pdf':
                                return 'application/pdf';
                            case 'jpg':
                            case 'jpeg':
                                return 'image/jpeg';
                            case 'png':
                                return 'image/png';
                            case 'xlsx':
                                return 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
                            case 'docs':
                                return 'application/msword';
                            default:
                                return null;
                        }
                    }, Attachment::getEnumValues('file_type'))
                )
                ->columnSpan('sm'), // Adjust column layout if required

        ]);
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
