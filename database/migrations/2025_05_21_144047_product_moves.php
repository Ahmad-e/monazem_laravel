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
        Schema::create('products_moves', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->float('move_amount');
            $table->integer('count');
            $table->date('date');

            $table->foreignId('old_place_id')
                ->nullable()
                ->constrained('places')
                ->onDelete('cascade');

            $table->foreignId('new_place_id')
                ->nullable()
                ->constrained('places')
                ->onDelete('cascade');

            $table->foreignId('product_id')
                ->constrained('products')
                ->onDelete('cascade');

            $table->foreignId('creator_id')
                ->nullable()
                ->constrained('users')
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
        Schema::dropIfExists('products_moves');
    }
};
