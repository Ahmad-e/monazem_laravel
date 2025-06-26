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
        Schema::create('rent_prepaid_expenses', function (Blueprint $table) {
            $table->id();
            $table->float('amount');
            $table->float('book_value');
            $table->float('amount_in_base')->nullable();
            $table->integer('month_count');
            $table->string('name');
            $table->longText('note')->nullable();

            $table->date('start_date');
            $table->date('end_date');


            $table->foreignId('account_id')
                ->constrained('accounts')
                ->onDelete('cascade');

            $table->foreignId('business_id')
                ->constrained('businesses')
                ->onDelete('cascade');

            $table->foreignId('branch_id')
                ->nullable()
                ->constrained('branches')
                ->onDelete('cascade') ;

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
        Schema::dropIfExists('	rent_prepaid_expenses');
    }
};
