<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * docker-compose run --rm artisan migrate:refresh --path=/database/migrations/2024_07_16_032349_create_products_table.php
     
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('store_id');
            //$table->foreignId('store_id')->constrained()->onDelete('cascade');
            $table->string('code');
            $table->string('barcode_number');
            $table->text('barcode_formats')->nullable();
            $table->string('mpn')->nullable();
            $table->string('model')->nullable();
            $table->string('asin')->nullable();
            $table->string('title');
            $table->string('category');
            $table->string('manufacturer');
            $table->string('serial_number')->nullable();
            $table->string('weight')->nullable();
            $table->string('dimension')->nullable();
            $table->string('warranty_length')->nullable();
            $table->string('brand');
            $table->text('ingredients')->nullable();
            $table->text('nutrition_facts')->nullable();
            $table->string('size')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
