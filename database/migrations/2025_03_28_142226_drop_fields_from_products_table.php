<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('code');
            $table->dropColumn('barcode_number');
            $table->dropColumn('barcode_formats');
            $table->dropColumn('mpn');
            $table->dropColumn('model');
            $table->dropColumn('asin');
            $table->dropColumn('title');
            $table->dropColumn('category');
            $table->dropColumn('manufacturer');
            $table->dropColumn('serial_number');
            $table->dropColumn('weight');
            $table->dropColumn('dimension');
            $table->dropColumn('warranty_length');
            $table->dropColumn('brand');
            $table->dropColumn('ingredients');
            $table->dropColumn('nutrition_facts');
            $table->dropColumn('size');
            $table->dropColumn('logo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
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
            $table->string('logo')->nullable();
        });
    }
};
