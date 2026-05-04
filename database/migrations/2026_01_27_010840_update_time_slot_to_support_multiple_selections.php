<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Change time_slot from string to JSON to support multiple selections
            $table->json('time_slots')->nullable()->after('end_date');
        });
        
        // Migrate existing data
        DB::statement("UPDATE bookings SET time_slots = JSON_ARRAY(time_slot) WHERE time_slot IS NOT NULL");
        
        // Drop the old column
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('time_slot');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Add back the old column
            $table->string('time_slot')->nullable()->after('end_date');
        });
        
        // Migrate data back (take first time slot if multiple)
        DB::statement("UPDATE bookings SET time_slot = JSON_UNQUOTE(JSON_EXTRACT(time_slots, '$[0]')) WHERE time_slots IS NOT NULL");
        
        // Drop the new column
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('time_slots');
        });
    }
};