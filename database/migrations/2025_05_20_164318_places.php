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
        Schema::create('places', function (Blueprint $table) {
            $table->id();

            $table->string('name')->nullable();

            $table->string('floor_number')->nullable();
            $table->string('room_number')->nullable();
            $table->string('shelves_alphabet')->nullable();
            $table->boolean('blocked')->default(false);
            $table->foreignId('building_id')
                ->constrained('buildings')
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
        Schema::dropIfExists('	places');
    }
};
