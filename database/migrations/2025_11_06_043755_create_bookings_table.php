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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Organizer
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->enum('recurrence', ['none', 'daily', 'weekly', 'monthly'])->default('none');
            $table->date('recurrence_end_date')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('confirmed');
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['room_id', 'date', 'start_time', 'end_time']);
            $table->index(['user_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
