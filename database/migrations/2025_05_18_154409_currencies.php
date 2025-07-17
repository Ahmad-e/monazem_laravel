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
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('code_en');
            $table->string('code_ar');
            $table->string('symbol')->nullable();
            $table->string('name_en')->nullable();
            $table->string('name_ar')->nullable();
            $table->float('exchange_rate_to_dollar',12,10)->nullable();
            $table->boolean('blocked_currency')->default(false);
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
