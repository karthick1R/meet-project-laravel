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
        Schema::table('product_keys', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('id');
            $table->string('registration_token')->nullable()->unique()->after('product_key');
            $table->timestamp('used_at')->nullable()->after('registration_token');
            $table->string('razorpay_order_id')->nullable()->after('used_at');
            $table->string('razorpay_payment_id')->nullable()->after('razorpay_order_id');
            $table->enum('payment_status', ['pending', 'completed', 'failed'])->default('pending')->after('razorpay_payment_id');
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_keys', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'registration_token', 'used_at', 'razorpay_order_id', 'razorpay_payment_id', 'payment_status']);
        });
    }
};
