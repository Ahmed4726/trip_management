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
        Schema::create('booking_guest_service_notes', function (Blueprint $table) {
            $table->id();
             $table->foreignId('booking_guest_id');

            $table->string('vip_level')->nullable();

            $table->string('celebration_type')->nullable();
            $table->text('celebration_notes')->nullable();

            $table->boolean('beach_dining_requested')->default(false);

            $table->text('excursion_requests')->nullable();
            $table->text('activity_preferences')->nullable();

            $table->text('guest_handling_notes')->nullable();
            $table->text('internal_service_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_guest_service_notes');
    }
};