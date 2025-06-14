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
        Schema::create('employees_salaries_advances_payments', function (Blueprint $table) {
                    $table->id();


                    $table->float('value',10,3);
                    $table->date('date')->nullable();


                    $table->foreignId('salaries_advance_id')
                        ->constrained('employees_salaries_advances')
                        ->onDelete('cascade');

                    $table->timestamps();
                });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees_salaries_advance_payments');
    }
};
