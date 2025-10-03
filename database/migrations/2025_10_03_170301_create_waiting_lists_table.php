<?php

// database/migrations/xxxx_xx_xx_create_waiting_lists_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('waiting_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('availability_id')->nullable();
            $table->integer('party_size')->default(1);
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->text('notes')->nullable();
            $table->enum('source', ['widget', 'staff'])->default('widget');
            $table->enum('status', ['open','notified','converted','cancelled'])->default('open');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('waiting_lists');
    }
};

