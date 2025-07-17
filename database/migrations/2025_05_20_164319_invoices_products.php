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
        Schema::create('invoices_products', function (Blueprint $table) {
            $table->id();
            $table->integer('products_count');
            $table->float('total_product_price',15,6);
            $table->float('tax_amount',15,6);

            $table->foreignId('product_id')
                ->constrained('products')
                ->onDelete('cascade');

            $table->foreignId('products_price_id')
                ->constrained('products_prices')
                ->onDelete('cascade');

            $table->foreignId('place_id')
                ->nullable()
                ->constrained('places')
                ->onDelete('cascade');

            $table->foreignId('invoice_id')
                ->constrained('invoices')
                ->onDelete('cascade');

            $table->foreignId('currency_id')
                ->constrained('currencies')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices_products');
    }
};
