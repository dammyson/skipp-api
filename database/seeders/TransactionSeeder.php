<?php

namespace Database\Seeders;

use App\Models\Store;
use App\Models\Invoice;
use App\Models\Transaction;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       $stores = Store::get();
      

       foreach($stores as $store) {
           $invoice = Invoice::create([
                'store_id' => $store['id'],
                'user_id' => 1,
                'total_amount' => 20.00,
                'fulfilment_method' => 'In-store pickup'
            ]);

            Transaction::create([
                'user_id' => 1,
                'invoice_id' => $invoice['id'],
                'total_amount' => 20.00,
                'wallet_balance' => 10.00
            ]);
       }
    }
}
