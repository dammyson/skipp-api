<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Product;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Grouping\Group;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Hidden;
use Illuminate\Support\Facades\Storage;

use Filament\Tables\Columns\ImageColumn;
use App\Filament\Imports\ProductImporter;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ProductResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ProductResource\RelationManagers;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationGroup = 'Inventory';

    public static function getEloquentQuery(): Builder
    {
        return Product::query()->when(
            Auth::user()->user_type !== "super-admin",
            fn($query) => $query->where('user_id', Auth::id())
        );
    }

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\Select::make('store_id')
                ->relationship('store', 'name')
                ->searchable()
                ->preload()
                ->createOptionForm([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('type')
                        ->label('Store type')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('address'),
                    Forms\Components\TextInput::make('company_rc'),
                    Forms\Components\TextInput::make('email')
                        ->label('Store email')
                        ->email()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('phone_number')
                        ->label('Phone number')
                        ->tel(),
                    Forms\Components\TextInput::make('website'),
                    Forms\Components\TextInput::make('city'),
                    Forms\Components\TextInput::make('state'),
                    Forms\Components\TextInput::make('logo'),
                    Forms\Components\TextInput::make('status'),
                ])
                ->required(),
            
            Forms\Components\Select::make('category_id')
                ->relationship('category', 'name')
                ->searchable()
                ->preload()
                ->createOptionForm([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255), 
                    
                    FileUpload::make('image_url')
                        ->label('category image')
                        ->disk('cloudinary') // Ensure you have the correct disk configured in `config/filesystems.php`
                        ->directory('uploads') // Optional: define a folder in Cloudinary
                        ->saveUploadedFileUsing(function ($file) {
                            $path = Storage::disk('cloudinary')->putFile('uploads', $file);
                            return Storage::disk('cloudinary')->url($path);
                        })
                        ->getUploadedFileNameForStorageUsing(fn ($file) => $file->hashName()),
                    
                ]),
            
            Hidden::make('user_id')
                ->default(Auth::id()) // Set to currently authenticated user
                ->dehydrated(true), // Ensures it gets saved to the database

              
            Forms\Components\TextInput::make('name') // integer
                ->required()
                ->maxLength(255),   
            Forms\Components\TextInput::make('description') // integer
                ->required()
                ->maxLength(255),   
            Forms\Components\TextInput::make('quantity') // integer
                ->required()
                ->maxLength(255),   
   
            Forms\Components\TextInput::make('price') // decimal
                ->required()
                ->hint('no comma or special characters')
                // ->descriptions('fill as integer or decimal without comma')
                ->maxLength(255),
              
            Forms\Components\TextInput::make('low_stock_threshold') // decimal
                ->required()
                ->maxLength(255), 
            Forms\Components\TextInput::make('code')
                ->unique(), 
            Forms\Components\TextInput::make('barcode_number'), 
            Forms\Components\TextInput::make('barcode_formats'), 
            Forms\Components\TextInput::make('mpn'), 
            Forms\Components\TextInput::make('model'), 
            Forms\Components\TextInput::make('asin'), 
            Forms\Components\TextInput::make('manufacturer'), 
            Forms\Components\TextInput::make('serial_number'), 
            Forms\Components\TextInput::make('weight'), 
            Forms\Components\TextInput::make('dimension'), 
            Forms\Components\TextInput::make('warranty_length'), 
            Forms\Components\TextInput::make('brand'), 
            Forms\Components\TextInput::make('ingredients'), 
            Forms\Components\TextInput::make('nutrition_facts'), 
            Forms\Components\TextInput::make('size'), 

            FileUpload::make('image_url')
                ->label('item image')
                ->disk('cloudinary') // Ensure you have the correct disk configured in `config/filesystems.php`
                ->directory('uploads') // Optional: define a folder in Cloudinary
                ->saveUploadedFileUsing(function ($file) {
                    $path = Storage::disk('cloudinary')->putFile('uploads', $file);
                    return Storage::disk('cloudinary')->url($path);
                })
                ->getUploadedFileNameForStorageUsing(fn ($file) => $file->hashName()),
           
                            
        ]);

    }

    public static function table(Table $table): Table
    {
        return $table
                ->columns([
                    ImageColumn::make('image_url')
                        ->label('item image')
                        ->circular(),
                    Tables\Columns\TextColumn::make('name')
                        ->label("item name")
                        ->searchable(),
                    Tables\Columns\TextColumn::make('store.name')
                        ->searchable(),
                    Tables\Columns\TextColumn::make('category.name')
                        ->default('null')
                        ->searchable(),
                    Tables\Columns\TextColumn::make('description'),
                    Tables\Columns\TextColumn::make('quantity'),
                    Tables\Columns\TextColumn::make('price')
                    ->sortable(),
                    Tables\Columns\TextColumn::make('low_stock_threshold'),
                    Tables\Columns\TextColumn::make('code'),
                    Tables\Columns\TextColumn::make('barcode_number'),
                    Tables\Columns\TextColumn::make('barcode_formats'),
                    Tables\Columns\TextColumn::make('mpn'),
                    Tables\Columns\TextColumn::make('model'),
                    Tables\Columns\TextColumn::make('asin'),
                    Tables\Columns\TextColumn::make('manufacturer'),
                    Tables\Columns\TextColumn::make('serial_number'),
                    Tables\Columns\TextColumn::make('weight'),
                    Tables\Columns\TextColumn::make('dimension'),
                    Tables\Columns\TextColumn::make('warranty_length'),
                    Tables\Columns\TextColumn::make('brand'),
                    Tables\Columns\TextColumn::make('ingredients'),
                    Tables\Columns\TextColumn::make('nutrition_facts'),
                    Tables\Columns\TextColumn::make('size'),
                    Tables\Columns\TextColumn::make('created_at')
                    ->label('sort_table_by_creation_time')
                    ->sortable()                   
               
            ])
            ->emptyStateHeading('No items added yet.')
            ->emptyStateDescription(" Click 'Add Item' to get started.")
            ->filters([
                SelectFilter::make('sort_by')
                    ->label('Sort By')
                    ->options([
                        // 'latest' => 'Lastest (last 2 days)',
                        // 'oldest' => 'Oldest(Before 2 Days)',
                        'lowest' => 'Lowest Stock',
                        // 'highest_price' => 'Higest Price',
                    ])                    
                    ->query(function (Builder $query, $data): Builder {
                        return $query
                            ->when(
                                $data['value'] == 'latest',
                                fn(Builder $query) => $query->where('created_at', '>=', now()->subDays(2))
                             
                            )
                            ->when(
                                $data['value'] == 'oldest',
                                fn(Builder $query) =>  $query->where('created_at', '<=', now()->subDays(2))
                              
                            )
                            ->when(
                                $data['value'] == 'lowest',
                                fn(Builder $query) =>  $query->whereColumn('quantity', '<=', 'products.low_stock_threshold')
                               
                            );
                            


                    //     // return match($value) {
                    //     //     'lastest' => $query->where('created_at', '>=', now()->subDays(2)),
                    //     //     'oldest' => $query->where('created_at', '<=', now()->subDays(2)),
                    //     //     'highest_price' => $query->orderBy('amount', 'desc'),
                    //     //     default => $query,
                    //     // };
                    }),



                
                Tables\Filters\SelectFilter::make('barcode_number'),
                
                

            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([                
                ImportAction::make()
                    ->importer(ProductImporter::class)
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
