<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all store IDs
        $storeIds = DB::table('stores')->pluck('id');

        foreach ($storeIds as $storeId) {
            // Seed 10 products for each store
            for ($i = 1; $i <= 10; $i++) {
                DB::table('products')->insert([
                    'id' => Str::uuid(),
                    'store_id' => $storeId,
                    'code' => 'P' . $i . Str::random(5),
                    'barcode_number' => '1234567890' . $i,
                    'barcode_formats' => 'UPC-A, EAN-13',
                    'mpn' => 'MPN' . Str::random(5),
                    'model' => 'Model' . Str::random(3),
                    'asin' => 'ASIN' . Str::random(10),
                    'title' => 'Product ' . $i . ' of Store ' . $storeId,
                    'category' => 'Category ' . $i,
                    'manufacturer' => 'Manufacturer ' . $i,
                    'serial_number' => 'SN' . Str::random(10),
                    'weight' => rand(1, 10) . ' kg',
                    'dimension' => rand(10, 100) . 'x' . rand(10, 100) . 'x' . rand(10, 100) . ' cm',
                    'warranty_length' => rand(1, 3) . ' years',
                    'brand' => 'Brand ' . $i,
                    'ingredients' => 'Ingredient ' . $i . ', Ingredient ' . ($i + 1),
                    'nutrition_facts' => 'Nutrition fact ' . $i,
                    'size' => rand(1, 100) . ' cm',
                    'description' => 'This is the description for Product ' . $i . ' of Store ' . $storeId,
                    'quantity' => 50,
                    'price' => rand(100, 1000),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
