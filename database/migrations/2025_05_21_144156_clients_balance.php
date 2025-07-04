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
        Schema::create('clients_balance', function (Blueprint $table) {
            $table->id();
            $table->float('opening');
            $table->float('current');
            $table->float('closing');

            $table->foreignId('trial_balance_id')
                ->nullable()
                ->constrained('trial_balance')
                ->onDelete('cascade') ;

            $table->foreignId('client_id')
                ->constrained('clients')
                ->onDelete('cascade');

            $table->foreignId('creator_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('cascade') ;

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
        Schema::dropIfExists('clients_balance');
    }
};
