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
        Schema::create('booking_guest_medicals', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('booking_guest_id');
                 

            $table->text('medical_conditions')->nullable();
            $table->text('medications')->nullable();

            $table->boolean('food_allergy_flag')->default(false);
            $table->text('food_allergy_details')->nullable();
            $table->text('other_allergies')->nullable();

            $table->boolean('motion_sickness')->default(false);

            $table->text('physical_limitations')->nullable();
            $table->text('mobility_notes')->nullable();

            $table->boolean('special_assistance_required')->default(false);
            $table->text('assistance_notes')->nullable();

            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_relationship')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->string('emergency_contact_email')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_guest_medicals');
    }
};