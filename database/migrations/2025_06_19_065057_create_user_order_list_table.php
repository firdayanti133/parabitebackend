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
        Schema::create('user_order_list', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->references('id')->on('user_orders'); 
            $table->unsignedBigInteger('menu_id')->references('id')->on('merchant_menu_list');
            $table->integer('price');
            $table->integer('quantity');
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_order_list');
    }
};
