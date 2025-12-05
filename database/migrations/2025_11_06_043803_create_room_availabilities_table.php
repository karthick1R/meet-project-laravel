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
        Schema::create('room_availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->enum('type', ['blocked', 'maintenance'])->default('blocked');
            $table->text('reason')->nullable();
            $table->timestamps();
            
            $table->index(['room_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_availabilities');
    }
};
