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
        Schema::create('product_places', function (Blueprint $table) {
            $table->id();
            $table->float('count');
            $table->foreignId('place_id')
                ->constrained('places')
                ->onDelete('cascade');

            $table->foreignId('batches_id')
                ->nullable()
                ->constrained('batches')
                ->onDelete('cascade');

            $table->foreignId('product_id')
                ->constrained('products')
                ->onDelete('cascade');


            $table->foreignId('unit_id')
                ->nullable()
                ->constrained('products_units')
                ->onDelete('cascade');
                    $table->timestamps();
                });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_places');
    }
};
