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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->references('id')->on('users');
            $table->unsignedBigInteger('merchant_id')->references('id')->on('merchants');
            $table->unsignedBigInteger('location_id')->references('id')->on('locations');
            $table->integer('total_price');
            $table->string('order_type')->enum(['delivery', 'takeaway', 'dine-in']);
            $table->string('payment_method')->enum(['qris', 'cash']);
            $table->string('status')->enum(['waiting', 'confirmed', 'processing', 'done', 'cancelled'])->default('waiting');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
