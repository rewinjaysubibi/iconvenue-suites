<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->decimal('discount_amount', 10, 2)->default(0)->after('total_amount');
            $table->decimal('discount_percentage', 5, 2)->nullable()->after('discount_amount');
            $table->string('discount_reason')->nullable()->after('discount_percentage');
            $table->decimal('original_amount', 10, 2)->nullable()->after('discount_reason');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'discount_amount',
                'discount_percentage', 
                'discount_reason',
                'original_amount'
            ]);
        });
    }
};