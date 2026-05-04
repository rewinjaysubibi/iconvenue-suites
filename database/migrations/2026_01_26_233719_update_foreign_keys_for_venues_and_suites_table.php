<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Update foreign key constraints to reference the new table name
        
        // Drop existing foreign key constraints
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['venue_id']);
        });
        
        Schema::table('venue_packages', function (Blueprint $table) {
            $table->dropForeign(['venue_id']);
        });
        
        // Add new foreign key constraints referencing venues_and_suites
        Schema::table('bookings', function (Blueprint $table) {
            $table->foreign('venue_id')->references('id')->on('venues_and_suites')->onDelete('cascade');
        });
        
        Schema::table('venue_packages', function (Blueprint $table) {
            $table->foreign('venue_id')->references('id')->on('venues_and_suites')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        // Rollback: restore original foreign key constraints
        
        // Drop new foreign key constraints
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['venue_id']);
        });
        
        Schema::table('venue_packages', function (Blueprint $table) {
            $table->dropForeign(['venue_id']);
        });
        
        // Add back original foreign key constraints referencing venues
        Schema::table('bookings', function (Blueprint $table) {
            $table->foreign('venue_id')->references('id')->on('venues')->onDelete('cascade');
        });
        
        Schema::table('venue_packages', function (Blueprint $table) {
            $table->foreign('venue_id')->references('id')->on('venues')->onDelete('cascade');
        });
    }
};