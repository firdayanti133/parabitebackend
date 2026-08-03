<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('menu_ratings');
        Schema::dropIfExists('user_wishlist');
        Schema::dropIfExists('user_favorite_menu');

        Schema::table('merchant_menu_list', function (Blueprint $table) {
            $table->dropColumn('is_favorite');
        });
    }

    public function down(): void
    {
        Schema::table('merchant_menu_list', function (Blueprint $table) {
            $table->enum('is_favorite', [0, 1])->default(0);
        });

        Schema::create('user_wishlist', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->references('id')->on('users');
            $table->unsignedBigInteger('menu_id')->references('id')->on('merchant_menu_list');
            $table->timestamps();
        });

        Schema::create('menu_ratings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->references('id')->on('users');
            $table->unsignedBigInteger('menu_id')->references('id')->on('merchant_menu_list');
            $table->integer('rating');
            $table->string('comment')->nullable();
            $table->timestamps();
        });

        Schema::create('user_favorite_menu', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->references('id')->on('users');
            $table->unsignedBigInteger('menu_id')->references('id')->on('merchant_menu_list');
            $table->timestamps();
        });
    }
};
