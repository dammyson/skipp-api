<?php

namespace Database\Seeders;

use App\Models\FulfilmentMethod;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FulfilmentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $fulfilmentMethods = [
            [
                'method_name' => 'Pick_up',
               
            ],
            [
                'method_name' => 'delivery',
                
            ],
            [
                'method_name' => 'in_store',
                
            ],
            [
                'method_name' => 'rider',
                
            ]
        ];

        foreach ($fulfilmentMethods as $fulfilmentMethod) {
            FulfilmentMethod::create($fulfilmentMethod);
        }
    }
}
