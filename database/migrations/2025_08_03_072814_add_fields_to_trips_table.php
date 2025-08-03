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
        Schema::table('trips', function (Blueprint $table) {
            $table->string('title')->nullable();
            $table->string('region')->nullable();
            $table->enum('status', ['Available', 'On Hold', 'Booked'])->default('Available');
            $table->enum('trip_type', ['private', 'open'])->default('private');
            $table->unsignedBigInteger('leading_guest_id')->nullable(); // no FK for now
            $table->text('notes')->nullable();
                $table->string('guest_form_token')->nullable();
    $table->string('guest_form_url')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->dropColumn([
                'title',
                'region',
                'status',
                'trip_type',
                'leading_guest_id',
                'notes',
            ]);
        });
    }
};
