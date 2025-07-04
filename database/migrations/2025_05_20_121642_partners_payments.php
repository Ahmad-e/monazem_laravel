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
        Schema::create('partners_payments', function (Blueprint $table) {
            $table->id();

            $table->float('value',20,5);
            $table->date('date')->nullable();

            $table->foreignId('partner_id')
                ->nullable()
                ->constrained('partners')
                ->onDelete('cascade');

            $table->foreignId('currency_id')
                ->constrained('currencies')
                ->onDelete('cascade');

            $table->foreignId('creator_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->timestamps();
                });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partners_payments');
    }
};
