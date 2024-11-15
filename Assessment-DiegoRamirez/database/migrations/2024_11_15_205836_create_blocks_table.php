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
        Schema::create('blocks', function (Blueprint $table) {
            $table->id();
            $table->time('hour_start');
            $table->time('hour_end');
            $table->enum('disponibility', ['available', 'unavailable']);
            $table->enum('status', ['active', 'inactive']);
            $table->foreignId('schedule_id')->constrained('schedules')->onDelete('cascade');
            $table->date('day');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blocks');
    }
};
