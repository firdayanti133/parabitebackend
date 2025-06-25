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
            $table->unsignedBigInteger('merchant_id')->references('id')->on('users');
            $table->string('name');
            $table->string('description');
            $table->string('image')->nullable();
            $table->enum('type', [1, 2]); // 1 = food, 2 = drink
            $table->string('nutrition_facts')->nullable();
            $table->integer('price');
            $table->enum('status', [1, 2, 3])->default(3); // 1 = available, 2 = unavailable, 3 = coming soon
            $table->enum('is_favorite', [0, 1])->default(0); // 0 = not favorite, 1 = favorite
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
