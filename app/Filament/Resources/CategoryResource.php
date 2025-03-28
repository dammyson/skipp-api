<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Category;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Filament\Clusters\Settings;
use App\Filament\Clusters\Inventory;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Storage;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\Layout\Split;



use Filament\Tables\Columns\Layout\Stack;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\CategoryResource\Pages;
// use Filament\Tables\Columns\ImageColumn;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use App\Filament\Resources\CategoryResource\RelationManagers;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    // protected static ?string $cluster = Inventory::class;

    // protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationGroup = 'Inventory';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('category_name')
                    ->required(),

                FileUpload::make('image_url')
                    ->avatar()
                    ->label('image_url')
                    ->disk('cloudinary') // Ensure you have the correct disk configured in `config/filesystems.php`
                    ->directory('uploads') // Optional: define a folder in Cloudinary
                    ->saveUploadedFileUsing(function ($file) {
                        $path = Storage::disk('cloudinary')->putFile('uploads', $file);
                        return Storage::disk('cloudinary')->url($path);
                    })
                    ->getUploadedFileNameForStorageUsing(fn ($file) => $file->hashName()),

                // $cloudinaryImage = $request->file('image')->storeOnCloudinary('products');
                // $url = $cloudinaryImage->getSecurePath();
                // $public_id = $cloudinaryImage->getPublicId();
            
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
                // Tables\Columns\TextColumn::make('image_url'),
                Tables\Columns\TextColumn::make('products_count') // Use products_count
                    ->label('Product Quantity')
                    ->getStateUsing(fn (Category $record) => $record->productCount()) // Call the function
                    ->sortable(),

            ])
            ->emptyStateHeading('No categories created yet')
            ->emptyStateDescription("Click 'Create Category' to start organizing your inventory.")
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
