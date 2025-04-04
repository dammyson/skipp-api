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
            $table->string('code')->nullable();
            $table->string('barcode_number')->nullable();
            $table->text('barcode_formats')->nullable();
            $table->string('mpn')->nullable();
            $table->string('model')->nullable();
            $table->string('asin')->nullable();
            $table->string('manufacturer')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('weight')->nullable();
            $table->string('dimension')->nullable();
            $table->string('warranty_length')->nullable();
            $table->string('brand')->nullable();
            $table->text('ingredients')->nullable();
            $table->text('nutrition_facts')->nullable();
            $table->string('size')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('code');
            $table->dropColumn('barcode_number');
            $table->dropColumn('barcode_formats');
            $table->dropColumn('mpn');
            $table->dropColumn('model');
            $table->dropColumn('asin');
            $table->dropColumn('manufacturer');
            $table->dropColumn('serial_number');
            $table->dropColumn('weight');
            $table->dropColumn('dimension');
            $table->dropColumn('warranty_length');
            $table->dropColumn('brand');
            $table->dropColumn('ingredients');
            $table->dropColumn('nutrition_facts');
            $table->dropColumn('size');
        });
    }
};
