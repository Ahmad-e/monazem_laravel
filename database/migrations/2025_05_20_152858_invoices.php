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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('number')->nullable();
            $table->enum('type', ['sell', 'buy', 'buyRefund', 'sellRefund']);
            $table->enum('payment_status', ['unpaid', 'partial', 'paid']);
            $table->string('note')->nullable();
            $table->float('unDiscounted_amount',10,3);
            $table->float('discounted_amount',10,3);
            $table->float('tax_amount',10,3);
            $table->float('shipping_cost',10,3);
            $table->float('refunded_amount',8,3);
            $table->float('affect_refund',8,3);
            $table->float('paid_amount',10,3);
            $table->float('amount_in_base',8,3)->nullable();
            $table->float('shipping_cost_in_base',8,3)->nullable();
            $table->boolean('blocked')->default(false);

            $table->date('date')->nullable();

            $table->foreignId('business_id')
                ->constrained('businesses')
                ->onDelete('cascade');

            $table->foreignId('branch_id')
                ->nullable()
                ->constrained('branches')
                ->onDelete('cascade');

            $table->foreignId('original_invoice_id')
                ->nullable()
                ->constrained('invoices')
                ->onDelete('cascade');

            $table->foreignId('partner_id')
                ->nullable()
                ->constrained('partners')
                ->onDelete('cascade');

            $table->foreignId('client_id')
                ->nullable()
                ->constrained('clients')
                ->onDelete('cascade');

            $table->foreignId('creator_id')
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
        Schema::dropIfExists('invoices');
    }
};
