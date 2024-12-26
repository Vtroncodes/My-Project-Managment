<?php

namespace App\Filament\Resources\TaskResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Forms\Components\NumberInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\User;
use LaraZeus\Quantity\Components\Quantity;

class WorklogsRelationManager extends RelationManager
{
    protected static string $relationship = 'workLogs';
    protected static ?string $recordTitleAttribute = 'id';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('assignee_id')
                    ->default(fn() => auth()->id())
                    ->required(),

                Forms\Components\TextInput::make('assignee_name')
                    ->label('Assignee')
                    ->default(fn() => auth()->user()->name)
                    ->disabled(), // Make the field read-only

                // "Hours" field with validation and float formatting
                Quantity::make('hours')
                    ->label('Hours')            
                    ->heading('Enter Hours...')
                    ->default(1)                 
                    ->minValue(0)
                    ->stacked()
                    ->steps(1)
                    ->extraAttributes(['class' => ' border-2 border-blue-500 p-4 rounded-md shadow-lg'])   ,

                // "Description" field spanning 12 columns
                Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->required()
                    ->columnSpan(12),  // Make the description field span 12 columns
            ]);
    }


    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('task.assignee.name')
                    ->label('Assignee')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('hours')
                    ->label('Hours Worked')
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Description')
                    ->wrap()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Logged At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                // Define any table filters if necessary
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
