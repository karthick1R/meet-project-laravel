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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('capacity')->default(10);
            $table->json('amenities')->nullable(); // e.g., ['projector', 'whiteboard', 'wifi']
            $table->string('location')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('color')->default('#3b82f6'); // For calendar display
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
