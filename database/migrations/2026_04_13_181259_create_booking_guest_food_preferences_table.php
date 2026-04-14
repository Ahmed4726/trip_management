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
        Schema::create('booking_guest_food_preferences', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('booking_guest_id')->nullable();

            $table->string('dietary_type')->nullable();

            $table->boolean('allergy_flag')->default(false);
            $table->text('allergy_details')->nullable();

            $table->text('dislikes')->nullable();
            $table->text('favorite_foods')->nullable();

            $table->string('breakfast_preference')->nullable();
            $table->string('snack_preference')->nullable();

            $table->text('food_notes')->nullable();

            $table->boolean('lactose_intolerant')->default(false);
            $table->boolean('gluten_free')->default(false);
            $table->boolean('halal')->default(false);
            $table->boolean('vegetarian')->default(false);
            $table->boolean('vegan')->default(false);
            $table->boolean('pescatarian')->default(false);
            $table->boolean('kosher')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_guest_food_preferences');
    }
};