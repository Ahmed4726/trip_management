<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('templates', function (Blueprint $table) {
            $table->id();

            $table->string('product_name');
            $table->string('product_type')->nullable();

            $table->foreignId('region_id')->nullable()->constrained()->nullOnDelete();

            $table->json('vessels_allowed')->nullable(); // IDs of allowed boats
            $table->unsignedInteger('duration_days')->default(0);
            $table->unsignedInteger('duration_nights')->default(0);

            $table->json('departure_ports')->nullable();
            $table->json('arrival_ports')->nullable();

            $table->unsignedInteger('min_bookings')->default(1);

            $table->text('inclusions')->nullable();
            $table->text('exclusions')->nullable();
            $table->text('obligatory_surcharges')->nullable();

            $table->string('experience_level')->nullable();
            $table->text('requirements_description')->nullable();

            $table->text('public_comment')->nullable();
            $table->text('internal_comment')->nullable();

            $table->timestamps();

            // Indexes for filtering
            $table->index(['region_id', 'product_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('templates');
    }
};
