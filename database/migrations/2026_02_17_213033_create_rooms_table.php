<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();

            $table->foreignId('boat_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('room_name');
            $table->unsignedInteger('capacity')->default(1);
            $table->decimal('price_per_night', 10, 2)->default(0);
            $table->string('status')->default('active');
            $table->string('deck')->nullable();
            $table->string('bed_type')->nullable();
            $table->unsignedInteger('extra_beds')->default(0);

            $table->timestamps();

            $table->unique(['boat_id', 'room_name'], 'boat_room_unique'); // avoid duplicate room names per boat
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
