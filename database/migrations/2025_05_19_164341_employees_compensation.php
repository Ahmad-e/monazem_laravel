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
        Schema::create('employees_compensations', function (Blueprint $table) {
                    $table->id();
                    $table->float('value',10,3);
                    $table->string('description')->nullable();
                    $table->date('pay_time')->nullable();

                    $table->foreignId('employee_id')
                        ->constrained('employees')
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
        Schema::dropIfExists('employees_compensation');
    }
};
