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
        Schema::create('trial_balance', function (Blueprint $table) {
            $table->id();
            $table->float('opening');
            $table->float('current');
            $table->float('closing');

            $table->foreignId('account_id')
                ->constrained('accounts')
                ->onDelete('cascade');


            $table->foreignId('creator_id')
                ->nullable()
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
        Schema::dropIfExists('trial_balance');
    }
};
