<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('venues', function (Blueprint $table) {
            $table->decimal('price_morning', 10, 2)->nullable()->after('price_per_day');
            $table->decimal('price_afternoon', 10, 2)->nullable()->after('price_morning');
            $table->decimal('price_evening', 10, 2)->nullable()->after('price_afternoon');
        });
    }

    public function down(): void
    {
        Schema::table('venues', function (Blueprint $table) {
            $table->dropColumn(['price_morning', 'price_afternoon', 'price_evening']);
        });
    }
};
