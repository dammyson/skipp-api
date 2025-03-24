<?php

namespace App\Filament\Imports;

use App\Models\Store;
use App\Models\Product;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Models\Import;

class ProductImporter extends Importer
{
    protected static ?string $model = Product::class;

    public static function getColumns(): array
    {
         
        return [
            //
            ImportColumn::make('store')
                ->relationship(resolveUsing: function (string $state): ?Store {
                    return Store::query()
                        ->where('name', $state)
                        ->orWhere('id', $state)
                        ->first();
                })
                ->example("store id or store name")
                ->helperText('Enter Store name or Id'),
            // ImportColumn::make('store')
            //     ->relationship()
            //     ->example("c3c54b97-2df9-4b26-b2d0-b264a4821089"),
            ImportColumn::make('code')
                ->requiredMapping()
                ->rules(['required'])
                ->example('12345'),
            ImportColumn::make('barcode_number')
                ->requiredMapping()
                ->rules(['required'])
                ->example('222222'),
            ImportColumn::make('barcode_formats')
            ->example('unicode'),
            ImportColumn::make('mpn')
            ->example('default_data'),
            ImportColumn::make('model')
            ->example('default_data'),
            ImportColumn::make('asin')
            ->example('default_data'),
            ImportColumn::make('title')
                ->requiredMapping()
                ->rules(['required'])
                ->example('default_data'),
            ImportColumn::make('category')
                ->requiredMapping()
                ->rules(['required'])
                ->example('default_data'),
            ImportColumn::make('manufacturer')
                ->requiredMapping()
                ->rules(['required'])
                ->example('default_data'),
            ImportColumn::make('serial_number')
                ->example('default_data'),
            ImportColumn::make('weight')
                ->example('default_data'),
            ImportColumn::make('dimension')
                ->example('default_data'),
            ImportColumn::make('warranty_length')
                ->example('default_data'),
            ImportColumn::make('brand') 
                ->requiredMapping()
                ->rules(['required'])
                ->example('default_data'),
            ImportColumn::make('ingredients')
                ->example('default_data'),
            ImportColumn::make('nutrition_facts')
                ->example('default_data'),
            ImportColumn::make('size')
                ->example('default_data'),
            ImportColumn::make('quantity')
                ->numeric()
                ->requiredMapping()
                ->rules(['required'])
                ->example(2),
            ImportColumn::make('price')
                ->numeric(decimalPlaces: 2)
                ->example(5.00),
                // ->castStateUsing(function (string $state): ?float {
                //     if (blank($state)) {
                //         return null;
                //     }
                    
                //     $state = preg_replace('/[^0-9.]/', '', $state);
                //     $state = floatval($state);
                
                //     return round($state, precision: 2);
                // }),
            ImportColumn::make('description')
                ->example('default_data')

        
        ];
    }

    public function resolveRecord(): ?Product
    {
        // return Product::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);

        return new Product();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your product import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
