<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agent_slot', function (Blueprint $table) {
            $table->id();

            $table->foreignId('agent_id')->constrained()->cascadeOnDelete();
            $table->foreignId('slot_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();

            $table->timestamps();

            $table->unique(['agent_id', 'slot_id'], 'agent_slot_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_slot');
    }
};
