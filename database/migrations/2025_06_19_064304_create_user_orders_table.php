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
        Schema::create('user_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->references('id')->on('users');
            $table->unsignedBigInteger('merchant_id')->references('id')->on('users');
            $table->unsignedBigInteger('location_id')->references('id')->on('locations')->nullable();
            $table->integer('bill');
            $table->enum('type', [1, 2, 3]); // 1 = delivery, 2 = takeaway, 3 = dine-in
            $table->enum('payment_method', [1, 2]); // 1 = cash, 2 = qris
            $table->enum('status', [1, 2, 3, 4])->default(1); // 1 = waiting, 2 = processing, 3 = done, 4 = cancelled
            $table->string('schedule')->nullable();
            $table->boolean('is_preorder')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_orders');
    }
};
