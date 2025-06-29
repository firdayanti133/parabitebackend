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
        Schema::table('merchant_menu_list', function (Blueprint $table) {
            $table->enum('type', [1, 2, 3]); // 1 = food, 2 = drink, 3 = snack
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('merchant_menu_list', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
