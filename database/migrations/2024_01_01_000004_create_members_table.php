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
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            
            // Hierarchy relationships
            $table->foreignId('clan_id')->constrained()->cascadeOnDelete();
            $table->foreignId('family_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            
            // Personal Information
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('maiden_name')->nullable(); // For married individuals
            $table->enum('gender', ['male', 'female', 'other']);
            $table->date('date_of_birth');
            $table->string('place_of_birth')->nullable();
            
            // Family Relationships (self-referencing)
            $table->foreignId('father_id')->nullable()->constrained('members')->nullOnDelete();
            $table->foreignId('mother_id')->nullable()->constrained('members')->nullOnDelete();
            $table->foreignId('spouse_id')->nullable()->constrained('members')->nullOnDelete();
            
            // Generation & Status
            $table->integer('generation_number')->default(1);
            $table->enum('status', ['alive', 'deceased'])->default('alive');
            $table->date('date_of_death')->nullable();
            $table->string('place_of_death')->nullable();
            
            // Contact Information
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            
            // Media
            $table->string('profile_photo')->nullable();
            
            // Additional Information
            $table->text('biography')->nullable();
            $table->string('occupation')->nullable();
            $table->text('notes')->nullable();
            
            // Metadata
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for performance
            $table->index('clan_id');
            $table->index('family_id');
            $table->index('branch_id');
            $table->index('father_id');
            $table->index('mother_id');
            $table->index('spouse_id');
            $table->index('generation_number');
            $table->index('status');
            $table->index(['first_name', 'last_name']);
            $table->index(['date_of_birth']);
            
            // Composite index for duplicate detection
            $table->index(['first_name', 'last_name', 'date_of_birth'], 'duplicate_check_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
