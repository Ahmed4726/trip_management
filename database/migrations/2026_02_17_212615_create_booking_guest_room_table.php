<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_guest_room', function (Blueprint $table) {
            $table->id();

            $table->foreignId('booking_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('guest_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('room_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->timestamps();

            // Prevent duplicate same guest in same booking & room
            $table->unique(['booking_id', 'guest_id', 'room_id'], 'booking_guest_room_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_guest_room');
    }
};
