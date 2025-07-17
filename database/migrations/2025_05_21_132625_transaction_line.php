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
        Schema::create('transaction_line', function (Blueprint $table) {
            $table->id();
            $table->string('description')->nullable();
            $table->enum('debit_credit', ['Debit', 'Credit']);
            $table->float('amount',18,6);

            $table->foreignId('transaction_id')
                ->constrained('transaction')
                ->onDelete('cascade');

            $table->foreignId('account_id')
                ->constrained('accounts')
                ->onDelete('cascade');

            $table->foreignId('partner_id')
                ->nullable()
                ->constrained('partners')
                ->onDelete('cascade');

            $table->foreignId('employee_id')
                ->nullable()
                ->constrained('employees')
                ->onDelete('cascade');

            $table->foreignId('client_id')
                ->nullable()
                ->constrained('clients')
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
        Schema::dropIfExists('transaction_line');
    }
};
