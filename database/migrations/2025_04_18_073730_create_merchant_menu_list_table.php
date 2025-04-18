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
        Schema::create('merchant_menu_list', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('merchant_id')->references('id')->on('merchants');
            $table->unsignedBigInteger('food_id')->references('id')->on('foods');
            $table->integer('stocks');
            $table->integer('price');
            $table->string('status')->enum(['available', 'unavailable', 'coming soon']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('merchant_menu_list');
    }
};
