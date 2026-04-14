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
        Schema::create('booking_guest_divings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_guest_id');

            $table->boolean('is_diver')->default(false);

            $table->string('certification_agency')->nullable();
            $table->string('certification_level')->nullable();
            $table->string('certification_number')->nullable();

            $table->unsignedInteger('logged_dives')->nullable();
            $table->date('last_dive_date')->nullable();

            $table->boolean('dive_insurance')->default(false);
            $table->string('insurance_provider')->nullable();

            $table->boolean('equipment_rental_required')->default(false);

            $table->string('wetsuit_size')->nullable();
            $table->string('fin_size')->nullable();
            $table->string('bcd_size')->nullable();

            $table->text('diving_medical_notes')->nullable();
            $table->text('diving_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_guest_divings');
    }
};