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
        Schema::create('booking_guest_surfings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_guest_id');

            $table->boolean('is_surfer')->default(false);

            $table->enum('surf_level', [
                'beginner',
                'intermediate',
                'advanced',
                'professional'
            ])->nullable();

            $table->boolean('bringing_own_board')->default(false);
            $table->unsignedInteger('board_count')->nullable();

            $table->string('board_type')->nullable();
            $table->string('board_length')->nullable();
            $table->string('board_width')->nullable();
            $table->string('board_volume')->nullable();

            $table->boolean('rental_required')->default(false);
            $table->boolean('coaching_required')->default(false);
            $table->boolean('photo_video_interest')->default(false);

            $table->text('surfing_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_guest_surfings');
    }
};