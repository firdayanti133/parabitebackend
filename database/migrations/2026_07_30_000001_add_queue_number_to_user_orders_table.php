<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_orders', function (Blueprint $table) {
            $table->unsignedInteger('queue_number')->nullable()->after('id');
            $table->index(['merchant_id', 'queue_number']);
        });
    }

    public function down(): void
    {
        Schema::table('user_orders', function (Blueprint $table) {
            $table->dropIndex(['merchant_id', 'queue_number']);
            $table->dropColumn('queue_number');
        });
    }
};
