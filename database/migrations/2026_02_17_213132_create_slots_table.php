<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('slots', function (Blueprint $table) {
            $table->id();

            $table->string('slot_type'); // e.g., Standard, Maintenance, Docking
            $table->string('status')->default('active');

            // Foreign keys
            $table->foreignId('boat_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('region_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('departure_port_id')->nullable()->constrained('ports')->nullOnDelete();
            $table->foreignId('arrival_port_id')->nullable()->constrained('ports')->nullOnDelete();
            $table->foreignId('created_from_template_id')->nullable()->constrained('templates')->nullOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();

            $table->date('start_date');
            $table->date('end_date');

            $table->unsignedInteger('duration_nights')->default(0);
            $table->json('available_rooms')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            // Indexes for quick lookups
            $table->index(['boat_id', 'start_date', 'end_date']);
            $table->index(['company_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('slots');
    }
};
