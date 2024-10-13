<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   /**
     * docker-compose run --rm artisan migrate:refresh --path=/database/migrations/2024_09_27_032417_create_transactions_table.php
     */

    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('invoice_id');
            $table->decimal('total_amount', 10, 2);
            $table->string('status')->default('pending'); // Can be 'pending', 'completed', 'failed', etc.
            $table->string('type')->default('pending'); 
            $table->decimal('wallet_balance', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
