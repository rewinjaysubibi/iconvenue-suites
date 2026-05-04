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
        Schema::table('venue_packages', function (Blueprint $table) {
            // Add time-based pricing fields
            $table->decimal('price_morning', 10, 2)->nullable()->after('price');
            $table->decimal('price_afternoon', 10, 2)->nullable()->after('price_morning');
            $table->decimal('price_evening', 10, 2)->nullable()->after('price_afternoon');
            
            // Add flag to enable/disable time-based pricing for this package
            $table->boolean('has_time_based_pricing')->default(false)->after('price_evening');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('venue_packages', function (Blueprint $table) {
            $table->dropColumn([
                'price_morning',
                'price_afternoon', 
                'price_evening',
                'has_time_based_pricing'
            ]);
        });
    }
};