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
        Schema::table('users', function (Blueprint $table) {
            // Make email nullable (for local users who won't use email)
            $table->string('email')->nullable()->change();
            
            // Make name unique (since it's used as username)
            $table->unique('name');
            
            // Add foreign key to link user to their member profile
            $table->foreignId('member_id')->nullable()->constrained('members')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Remove unique constraint from name
            $table->dropUnique(['name']);
            
            // Drop foreign key
            $table->dropForeign(['member_id']);
            $table->dropColumn('member_id');
            
            // Make email required again
            $table->string('email')->nullable(false)->change();
        });
    }
};
