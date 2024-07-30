<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('stores')->insert([
            [
                'id' => Str::uuid(),
                'name' => 'Store 1',
                'type' => 'Retail',
                'address' => '123 Main St',
                'company_rc' => 'RC123456',
                'email' => 'store1@example.com',
                'phone_number' => '123-456-7890',
                'website' => 'http://www.store1.com',
                'city' => 'City1',
                'state' => 'State1',
                'logo' => 'http://www.store1.com/logo.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Store 2',
                'type' => 'Wholesale',
                'address' => '456 Another St',
                'company_rc' => 'RC654321',
                'email' => 'store2@example.com',
                'phone_number' => '987-654-3210',
                'website' => 'http://www.store2.com',
                'city' => 'City2',
                'state' => 'State2',
                'logo' => 'http://www.store2.com/logo.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Store 3',
                'type' => 'Online',
                'address' => '789 Market St',
                'company_rc' => 'RC987654',
                'email' => 'store3@example.com',
                'phone_number' => '555-555-5555',
                'website' => 'http://www.store3.com',
                'city' => 'City3',
                'state' => 'State3',
                'logo' => 'http://www.store3.com/logo.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
