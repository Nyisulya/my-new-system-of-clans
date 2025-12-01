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
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->morphs('mediable'); // mediable_id and mediable_type for polymorphic relation
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type'); // image, document, video, etc.
            $table->string('mime_type');
            $table->unsignedBigInteger('file_size'); // in bytes
            $table->string('disk')->default('public');
            $table->text('description')->nullable();
            $table->json('metadata')->nullable(); // For storing dimensions, duration, etc.
            $table->integer('order')->default(0); // For ordering multiple media items
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['mediable_id', 'mediable_type']);
            $table->index('file_type');
            $table->index('order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
