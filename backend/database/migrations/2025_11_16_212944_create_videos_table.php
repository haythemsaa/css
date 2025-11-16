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
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_id')->nullable()->constrained('contents')->onDelete('cascade');

            // Informations
            $table->string('title');
            $table->text('description')->nullable();

            // Vidéo
            $table->string('video_url');
            $table->string('thumbnail_url')->nullable();
            $table->integer('duration')->nullable(); // en secondes

            // Qualité
            $table->enum('quality', ['hd', 'fullhd', '4k'])->default('hd');

            // Statistiques
            $table->unsignedBigInteger('views_count')->default(0);

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('content_id');
            $table->index('quality');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('videos');
    }
};
