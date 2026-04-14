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
        Schema::create('booking_guest_housekeepings', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('booking_guest_id');

            $table->foreignId('room_id')
                  ->nullable();

            $table->enum('bed_setup_preference', ['double', 'twin', 'other'])->nullable();

            $table->string('pillow_preference')->nullable();
            $table->string('towel_change_preference')->nullable();

            $table->text('bathroom_assignment_notes')->nullable();
            $table->text('cleaning_notes')->nullable();

            $table->boolean('baby_cot_required')->default(false);
            $table->boolean('umbrella_required')->default(false);
            $table->boolean('beach_setup_required')->default(false);

            $table->text('room_comfort_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_guest_housekeepings');
    }
};