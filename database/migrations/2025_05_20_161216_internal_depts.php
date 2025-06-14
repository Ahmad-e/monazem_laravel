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
        Schema::create('internal_dept', function (Blueprint $table) {

            $table->id();
            $table->string('note')->nullable();

            $table->float('total',10,3);
            $table->float('paid',10,3);
            $table->float('remaining',10,3);

            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            $table->enum('type', ['Debit', 'Credit']);
            $table->enum('state', ['paid','unpaid','partial','dead','forgiven']);

            $table->foreignId('invoice_id')
                ->constrained('invoices')
                ->onDelete('cascade');

            $table->foreignId('currency_id')
                ->constrained('currencies')
                ->onDelete('cascade');

            $table->foreignId('client_id')
                ->constrained('users')
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
        Schema::dropIfExists('internal_dept');
    }
};
