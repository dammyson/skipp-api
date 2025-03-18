<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AnswerResource\Pages;
use App\Filament\Resources\AnswerResource\RelationManagers;
use App\Models\Answer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AnswerResource extends Resource
{
    protected static ?string $model = Answer::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationLabel(): string
    {   
        return 'FAQs';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('question_id')
                    ->relationship('question', 'title')
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('description')
                            ->label('Description')
                            ->maxLength(255)
                    
                    ])                
                    ->required(),
                Forms\Components\TextInput::make('question_text'),
                Forms\Components\Hidden::make('user_id') // Hide the field from the form
                    ->default(auth()->id()) // Automatically set the current user's ID
                    // ->default(1) // Automatically set the current user's ID
                
                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('question.title')
                ->searchable(),
                Tables\Columns\TextColumn::make('question_text')
                    ->label('Answer text')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('answer_by') // Use products_count
                    ->label('Answer by')
                    ->getStateUsing(fn (Answer $record) =>  $record->user?->first_name) // Call the function
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAnswers::route('/'),
            'create' => Pages\CreateAnswer::route('/create'),
            'view' => Pages\ViewAnswer::route('/{record}'),
            'edit' => Pages\EditAnswer::route('/{record}/edit'),
        ];
    }
}
