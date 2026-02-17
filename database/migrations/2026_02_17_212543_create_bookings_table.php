<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();

            $table->string('source')->nullable();
            $table->text('notes')->nullable();
            $table->string('guest_name');

            $table->string('status')->default('pending');

            $table->decimal('price', 12, 2)->nullable();
            $table->decimal('price_usd', 12, 2)->nullable();
            $table->string('currency', 3)->nullable();

            // Relationships
            $table->foreignId('agent_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('room_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('boat_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('slot_id')->nullable()->constrained()->nullOnDelete();

            $table->foreignId('rate_plan_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('payment_policy_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('cancellation_policy_id')->nullable()->constrained()->nullOnDelete();

            $table->foreignId('salesperson_id')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            // Optional indexes for performance
            $table->index(['company_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
