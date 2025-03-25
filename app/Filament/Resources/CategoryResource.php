<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Category;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Filament\Clusters\Settings;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\CategoryResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CategoryResource\RelationManagers;



use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
// use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    // protected static ?string $cluster = Settings::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('category_name')
                    ->required(),

                FileUpload::make('image_url')
                    ->avatar()
                    ->label('image_url'),
            
                // Forms\Components\TextInput::make('image_url')
                //     ->label('image_url'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_url')
                    ->circular(),
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('image_url'),
                Tables\Columns\TextColumn::make('products_count') // Use products_count
                    ->label('Product Quantity')
                    ->getStateUsing(fn (Category $record) => $record->productCount()) // Call the function
                    ->sortable(),


                // Split::make([
                //     ImageColumn::make('image_url')
                //         ->circular(),
                //     TextColumn::make('name')
                //         ->weight(FontWeight::Bold)
                //         ->searchable()
                //         ->sortable(),
                //     Stack::make([
                //         TextColumn::make('phone')
                //             ->icon('heroicon-m-phone'),
                //         TextColumn::make('email')
                //             ->icon('heroicon-m-envelope'),
                //     ]),
                // ])
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
            RelationManagers\ProductsRelationManager::class,

        ];
    }

    

    public static function query(Builder $query): Builder
    {
        return $query->withCount('products');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'view' => Pages\ViewCategory::route('/{record}'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
