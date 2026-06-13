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
        Schema::table('members', function (Blueprint $table) {
            // Make required fields nullable for simple registration
            $table->unsignedBigInteger('clan_id')->nullable()->change();
            $table->unsignedBigInteger('family_id')->nullable()->change();
            $table->date('date_of_birth')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            // Revert back to required (careful if nulls exist)
            // We generally don't revert this in production if data exists, 
            // but for completeness:
            $table->unsignedBigInteger('clan_id')->nullable(false)->change();
            $table->unsignedBigInteger('family_id')->nullable(false)->change();
            $table->date('date_of_birth')->nullable(false)->change();
        });
    }
};
