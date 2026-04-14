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
        Schema::create('booking_guest_drink_preferences', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('booking_guest_id');

            $table->boolean('drinks_alcohol')->default(false);

            $table->string('wine_preference')->nullable();
            $table->string('spirits_preference')->nullable();
            $table->string('cocktail_preference')->nullable();
            $table->string('beer_preference')->nullable();

            $table->string('coffee_preference')->nullable();
            $table->string('tea_preference')->nullable();
            $table->string('soft_drink_preference')->nullable();
            $table->string('water_preference')->nullable();

            $table->text('drink_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_guest_drink_preferences');
    }
};