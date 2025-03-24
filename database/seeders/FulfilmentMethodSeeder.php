<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FulfilmentMethod;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class FulfilmentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fulfilmentMethods = [
            
            [
                'method_name' => 'In-store pickup',
                
            ],
            [
                'method_name' => 'Buy Online',
                
            ],
            [
                'method_name' => 'Pick Up In-Store [BOPIS]',
               
            ],
            [
                'method_name' => 'delivery',
                
            ]
        ];

        foreach ($fulfilmentMethods as $fulfilmentMethod) {
            FulfilmentMethod::create($fulfilmentMethod);
        }
    }
}
