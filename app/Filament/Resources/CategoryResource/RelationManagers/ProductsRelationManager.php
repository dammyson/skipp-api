<?php

namespace App\Filament\Resources\CategoryResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'products';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                    Forms\Components\TextInput::make('code')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('barcode_number')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('barcode_formats')
                    ->maxLength(255),
                Forms\Components\TextInput::make('mpn')
                    ->maxLength(255),                
                Forms\Components\TextInput::make('model')
                    ->maxLength(255),
                Forms\Components\TextInput::make('asin')
                    ->maxLength(255),
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('category')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('manufacturer')
                    ->required()
                    ->maxLength(255),                
                Forms\Components\TextInput::make('serial_number')
                    ->maxLength(255),
                Forms\Components\TextInput::make('weight')
                    ->maxLength(255),
                Forms\Components\TextInput::make('dimension')
                    ->maxLength(255),
                Forms\Components\TextInput::make('warranty_length')
                    ->maxLength(255),
                Forms\Components\TextInput::make('brand')
                    ->required()
                    ->maxLength(255),   
                Forms\Components\TextInput::make('ingredients')
                    ->maxLength(255),   
                Forms\Components\TextInput::make('nutrition_facts')
                    ->maxLength(255),                  
                Forms\Components\TextInput::make('size')
                    ->maxLength(255),   
                Forms\Components\TextInput::make('quantity') // integer
                    ->required()
                    ->maxLength(255),   
                Forms\Components\TextInput::make('price') // decimal
                    ->required()
                    ->maxLength(255),   
                Forms\Components\TextInput::make('description')
                    ->maxLength(255)
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('store.name')
                        ->searchable(),
                    Tables\Columns\TextColumn::make('category.name')
                        ->default('null')
                        ->searchable(),
                    Tables\Columns\TextColumn::make('code')
                        ->searchable(),
                    Tables\Columns\TextColumn::make('barcode_number'),
                    Tables\Columns\TextColumn::make('mpn'),
                    Tables\Columns\TextColumn::make('model'),
                    Tables\Columns\TextColumn::make('asin'),
                    Tables\Columns\TextColumn::make('title')
                        ->searchable(),
                    Tables\Columns\TextColumn::make('category'),
                    Tables\Columns\TextColumn::make('manufacturer'),
                    Tables\Columns\TextColumn::make('weight'),
                    Tables\Columns\TextColumn::make('dimension'),
                    Tables\Columns\TextColumn::make('warranty_length'),
                    Tables\Columns\TextColumn::make('brand'),
                    Tables\Columns\TextColumn::make('ingredients'),
                    Tables\Columns\TextColumn::make('nutrition_facts'),
                    Tables\Columns\TextColumn::make('size'),
                    Tables\Columns\TextColumn::make('description'),
                    Tables\Columns\TextColumn::make('quantity')
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
