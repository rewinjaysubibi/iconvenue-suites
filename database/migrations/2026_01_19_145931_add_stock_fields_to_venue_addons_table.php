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
        Schema::table('venue_addons', function (Blueprint $table) {
            $table->integer('stock_quantity')->nullable()->after('sort_order');
            $table->boolean('track_stock')->default(false)->after('stock_quantity');
            $table->integer('low_stock_threshold')->nullable()->after('track_stock');
            $table->text('notes')->nullable()->after('low_stock_threshold');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('venue_addons', function (Blueprint $table) {
            $table->dropColumn(['stock_quantity', 'track_stock', 'low_stock_threshold', 'notes']);
        });
    }
};
