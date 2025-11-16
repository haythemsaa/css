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
        Schema::create('contents', function (Blueprint $table) {
            $table->id();

            // Contenu
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('body')->nullable();
            $table->text('excerpt')->nullable();

            // Type
            $table->enum('type', ['article', 'video', 'gallery', 'podcast'])->default('article');

            // Relations
            $table->foreignId('category_id')->nullable()->constrained('partner_categories')->onDelete('set null');
            $table->foreignId('author_id')->nullable()->constrained('users')->onDelete('set null');

            // Options
            $table->boolean('is_premium')->default(false);
            $table->boolean('is_featured')->default(false);

            // Statistiques
            $table->unsignedBigInteger('views_count')->default(0);
            $table->unsignedBigInteger('likes_count')->default(0);

            // Publication
            $table->timestamp('published_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('slug');
            $table->index('type');
            $table->index('category_id');
            $table->index('author_id');
            $table->index('is_premium');
            $table->index('is_featured');
            $table->index('published_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contents');
    }
};
