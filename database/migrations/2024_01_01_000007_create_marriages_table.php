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
        Schema::create('marriages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('husband_id')->constrained('members')->onDelete('cascade');
            $table->foreignId('wife_id')->constrained('members')->onDelete('cascade');
            $table->date('marriage_date')->nullable();
            $table->date('divorce_date')->nullable();
            $table->enum('status', ['active', 'divorced'])->default('active');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Ensure no duplicate active marriages
            $table->unique(['husband_id', 'wife_id', 'status']);
        });

        // Migrate existing spouse_id data to marriages table
        DB::statement("
            INSERT INTO marriages (husband_id, wife_id, status, created_at, updated_at)
            SELECT 
                CASE WHEN m1.gender = 'male' THEN m1.id ELSE m1.spouse_id END as husband_id,
                CASE WHEN m1.gender = 'female' THEN m1.id ELSE m1.spouse_id END as wife_id,
                'active' as status,
                CURRENT_TIMESTAMP as created_at,
                CURRENT_TIMESTAMP as updated_at
            FROM members m1
            WHERE m1.spouse_id IS NOT NULL
            AND m1.id < m1.spouse_id  -- Avoid duplicates
        ");

        // Drop the old spouse_id column and its constraints
        Schema::table('members', function (Blueprint $table) {
            $table->dropForeign(['spouse_id']); // Drop foreign key first
            $table->dropIndex(['spouse_id']); // Then drop index
            $table->dropColumn('spouse_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore spouse_id column
        Schema::table('members', function (Blueprint $table) {
            $table->foreignId('spouse_id')->nullable()->after('mother_id')->constrained('members')->onDelete('set null');
        });

        // Migrate first marriage back to spouse_id
        DB::statement("
            UPDATE members m
            SET spouse_id = (
                SELECT CASE 
                    WHEN m.gender = 'male' THEN mar.wife_id 
                    ELSE mar.husband_id 
                END
                FROM marriages mar
                WHERE (mar.husband_id = m.id OR mar.wife_id = m.id)
                AND mar.status = 'active'
                LIMIT 1
            )
        ");

        Schema::dropIfExists('marriages');
    }
};
