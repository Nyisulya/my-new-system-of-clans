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
            $table->string('birth_place')->nullable()->after('date_of_birth');
            $table->decimal('birth_lat', 10, 8)->nullable()->after('birth_place');
            $table->decimal('birth_lng', 11, 8)->nullable()->after('birth_lat');
            
            $table->string('current_location')->nullable()->after('status');
            $table->decimal('current_lat', 10, 8)->nullable()->after('current_location');
            $table->decimal('current_lng', 11, 8)->nullable()->after('current_lat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            //
        });
    }
};
