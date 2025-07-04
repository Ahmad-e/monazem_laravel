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
        Schema::create('employees_salaries_payments', function (Blueprint $table) {
            $table->id();
            $table->float('value',10,3);
            $table->float('allowances',10,3);
            $table->float('deductions',10,3);
            $table->string('description')->nullable();
            $table->date('date');
            $table->date('work_from')->nullable();
            $table->date('work_to')->nullable();

            $table->foreignId('salary_id')
                ->constrained('employees_salaries')
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
        Schema::dropIfExists('employees_salaries_payments');
    }
};
