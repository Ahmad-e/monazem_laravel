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
        Schema::create('partners', function (Blueprint $table) {
            $table->id();

            $table->float('total_capital', 15,6); //إجمالي رأس المال
            $table->float('ownership_percentage', 15,6); //نسبة الملكية
            $table->string('role')->nullable();
            $table->string('note')->nullable();
            $table->date('join_date')->nullable();
            $table->boolean('blocked_partner')->default(false);

            $table->foreignId('business_id')
                ->constrained('businesses')
                ->onDelete('cascade');

            $table->foreignId('user_id')
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
        Schema::dropIfExists('partners');
    }
};
