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

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                
                    
                ]),

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
            FileUpload::make('logo'), 
            Forms\Components\TextInput::make('description')
                ->maxLength(255)
                            
        ]);

        $table->uuid('store_id');
    }

    public static function table(Table $table): Table
    {
        return $table
                ->columns([
                    ImageColumn::make('logo')
                    ->circular(),
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
                    Tables\Columns\TextColumn::make('quantity'),
                    Tables\Columns\TextColumn::make('price')
                    ->sortable()
                    
               
            ])
            // ->groups([
            //     Group::make('price')
            //         ->orderQueryUsing(fn (Builder $query, string $direction) => $query->orderBy('price', $direction)),
            // ])
            ->emptyStateHeading('No items added yet.')
            ->emptyStateDescription(" Click 'Add Item' to get started.")
            ->filters([
                SelectFilter::make('sort_by')
                    ->label('Sort By')
                    ->options([
                        'latest' => 'Lastest (last 2 days)',
                        'oldest' => 'Oldest(Before 2 Days)',
                        'lowest' => 'Lowest Stock(stock less than 5 in quantity)',
                        'highest_price' => 'Higest Price',
                    ])                    
                    ->query(function (Builder $query, $data): Builder {
                        return $query
                            ->when(
                                $data['value'] == 'latest',
                                fn(Builder $query) => $query->where('created_at', '>=', now()->subDays(2))
                                // fn(Builder $query) => $query->where('created_at', '>=', now()->subMinutes(2))
                            )
                            ->when(
                                $data['value'] == 'oldest',
                                fn(Builder $query) =>  $query->where('created_at', '<=', now()->subDays(2))
                                // fn(Builder $query) =>  $query->where('created_at', '<=', now()->subMinutes(2))
                            )
                            ->when(
                                $data['value'] == 'lowest',
                                fn(Builder $query) =>  $query->where('quantity', '<=', 5)
                                // fn(Builder $query) =>  $query->where('created_at', '<=', now()->subMinutes(2))
                            )
                            ->when(
                                $data['value'] == 'highest_price',
                                fn(Builder $query) =>  $query->orderBy('price')

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
