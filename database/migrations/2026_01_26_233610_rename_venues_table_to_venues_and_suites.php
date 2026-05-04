<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Rename the venues table to venues_and_suites
        Schema::rename('venues', 'venues_and_suites');
    }

    public function down(): void
    {
        // Rollback: rename back to venues
        Schema::rename('venues_and_suites', 'venues');
    }
};