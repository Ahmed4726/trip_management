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
        Schema::create('booking_travel_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('booking_id');

            $table->bigInteger('booking_guest_id')
                  ->nullable();

            $table->enum('direction', ['arrival', 'departure']);
            $table->enum('travel_type', ['flight', 'hotel', 'other']);

            $table->date('date')->nullable();
            $table->time('time')->nullable();

            $table->string('airport')->nullable();
            $table->string('airline')->nullable();
            $table->string('flight_number')->nullable();

            $table->boolean('pickup_required')->default(false);
            $table->boolean('dropoff_required')->default(false);

            $table->string('location_name')->nullable();
            $table->text('location_address')->nullable();

            $table->text('notes')->nullable();

            $table->boolean('applies_to_all')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_travel_details');
    }
};