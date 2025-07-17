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
        Schema::create('expenses_payments', function (Blueprint $table) {
            $table->id();
            $table->string('note')->nullable();
            $table->float('value',15,6);
            $table->date('date')->nullable();

            $table->foreignId('expenses_id')
                ->constrained('expenses')
                ->onDelete('cascade');

            $table->foreignId('creator_id')
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
        Schema::dropIfExists('expenses_payments');
    }
};
