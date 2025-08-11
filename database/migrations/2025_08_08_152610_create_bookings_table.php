<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained()->onDelete('cascade');
            $table->string('customer_name');
            $table->integer('guests')->default(1);
            $table->string('source');
            $table->string('email')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('nationality')->nullable();
            $table->string('passport_number')->nullable();
            $table->enum('booking_status', ['pending', 'confirmed', 'cancelled'])->nullable();
            $table->string('pickup_location_time')->nullable();
            $table->string('addons')->nullable();
            $table->enum('room_preference', ['single', 'double', 'suite'])->nullable();
            $table->foreignId('agent_id')->nullable()->constrained('agents')->nullOnDelete();
            $table->text('comments')->nullable();
            $table->text('notes')->nullable();
            $table->string('token', 64)->unique(); // Booking link token
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
