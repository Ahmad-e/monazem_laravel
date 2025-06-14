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
        Schema::create('stock', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->integer('count');
            $table->date('date');

            $table->foreignId('building_id')
                ->constrained('buildings')
                ->onDelete('cascade');

            $table->foreignId('place_id')
                ->nullable()
                ->constrained('places')
                ->onDelete('cascade');

            $table->foreignId('manager_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('cascade');

            $table->foreignId('product_id')
                ->constrained('products')
                ->onDelete('cascade');

            $table->foreignId('products_price_id')
                ->nullable()
                ->constrained('products_prices')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock');
    }
};
