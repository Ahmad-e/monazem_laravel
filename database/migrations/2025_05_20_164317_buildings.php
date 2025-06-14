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
        Schema::create('buildings', function (Blueprint $table) {
            $table->id();

            $table->string('name')->nullable();
            $table->enum('type', ['shop', 'warehouse']);
            $table->float('latitude',4,12)->nullable();
            $table->float('longitude',4,12)->nullable();
            $table->boolean('blocked')->default(false);
            $table->foreignId('branch_id')
                ->constrained('branches')
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
        Schema::dropIfExists('buildings');
    }
};
