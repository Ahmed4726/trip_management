<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('guests');
            $table->decimal('price', 10, 2);
            $table->string('boat'); // Added boat field
            $table->unsignedBigInteger('agent_id');
            $table->timestamps();

            $table->foreign('agent_id')->references('id')->on('agents')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};

