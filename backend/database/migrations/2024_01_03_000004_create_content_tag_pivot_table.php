<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_tag', function (Blueprint $table) {
            $table->foreignId('content_id')->constrained()->onDelete('cascade');
            $table->foreignId('content_tag_id')->constrained()->onDelete('cascade');
            $table->primary(['content_id', 'content_tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_tag');
    }
};
