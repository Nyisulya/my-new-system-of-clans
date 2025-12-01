<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify the ENUM column to include 'member'
        // Note: DB::statement is needed for modifying ENUMs in some MySQL versions/drivers
        // properly without doctrine/dbal issues
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'editor', 'viewer', 'member') NOT NULL DEFAULT 'viewer'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original ENUM (warning: this will fail if 'member' roles exist)
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'editor', 'viewer') NOT NULL DEFAULT 'viewer'");
    }
};
