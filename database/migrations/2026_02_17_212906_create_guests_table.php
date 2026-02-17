<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guests', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('first_name');
            $table->string('last_name')->nullable();

            $table->string('email')->nullable()->index();
            $table->string('phone')->nullable();
            $table->string('gender', 10)->nullable();

            $table->date('dob')->nullable();
            $table->string('passport')->nullable();

            $table->text('address')->nullable();

            $table->timestamps();

            // Optional but useful indexes
            $table->index(['company_id', 'email']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guests');
    }
};
