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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('boat_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->string('room_name'); // Cabin name / number
            $table->integer('capacity')->default(1);
            $table->decimal('price_per_night', 10, 2)->nullable();
            $table->enum('status', ['available', 'maintenance', 'inactive'])
                  ->default('available');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
