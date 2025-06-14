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
        Schema::create('cashes', function (Blueprint $table) {
            $table->id();

            $table->float('Balance',20,10);
            $table->string('note')->nullable();

            $table->foreignId('branch_id')
                ->constrained('branches')
                ->onDelete('cascade');

            $table->foreignId('manager_id')
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
         Schema::dropIfExists('cashes');
    }
};
