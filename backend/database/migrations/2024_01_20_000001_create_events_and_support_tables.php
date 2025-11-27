<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Events System
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('event_type'); // 'match', 'meet_greet', 'training', 'conference', 'party'
            $table->string('venue');
            $table->string('address')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamp('start_datetime');
            $table->timestamp('end_datetime')->nullable();
            $table->integer('max_attendees')->nullable();
            $table->integer('registered_count')->default(0);
            $table->boolean('requires_registration')->default(true);
            $table->boolean('is_free')->default(true);
            $table->decimal('price', 10, 2)->nullable();
            $table->json('images')->nullable();
            $table->string('status')->default('upcoming'); // 'upcoming', 'ongoing', 'completed', 'cancelled'
            $table->boolean('is_featured')->default(false);
            $table->text('registration_requirements')->nullable();
            $table->timestamps();

            $table->index(['event_type', 'status']);
            $table->index(['start_datetime']);
        });

        // Event Registrations
        Schema::create('event_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('status')->default('registered'); // 'registered', 'attended', 'cancelled', 'no_show'
            $table->json('registration_data')->nullable(); // Custom form data
            $table->string('qr_code')->unique()->nullable();
            $table->timestamp('registered_at');
            $table->timestamp('attended_at')->nullable();
            $table->timestamps();

            $table->unique(['event_id', 'user_id']);
            $table->index(['user_id', 'status']);
        });

        // Support Tickets
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('subject');
            $table->text('description');
            $table->string('category'); // 'technical', 'account', 'payment', 'content', 'other'
            $table->string('priority')->default('medium'); // 'low', 'medium', 'high', 'urgent'
            $table->string('status')->default('open'); // 'open', 'in_progress', 'waiting_user', 'resolved', 'closed'
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->json('attachments')->nullable();
            $table->timestamp('first_response_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->integer('satisfaction_rating')->nullable(); // 1-5
            $table->text('satisfaction_comment')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['status', 'priority']);
            $table->index(['ticket_number']);
        });

        // Support Ticket Messages
        Schema::create('support_ticket_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('support_tickets')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('message');
            $table->boolean('is_staff_reply')->default(false);
            $table->json('attachments')->nullable();
            $table->boolean('is_internal_note')->default(false); // Only visible to staff
            $table->timestamps();

            $table->index(['ticket_id', 'created_at']);
        });

        // Global Search Index (for advanced search)
        Schema::create('search_index', function (Blueprint $table) {
            $table->id();
            $table->string('searchable_type'); // Model class name
            $table->unsignedBigInteger('searchable_id');
            $table->string('title');
            $table->text('content')->nullable();
            $table->string('category')->nullable();
            $table->json('tags')->nullable();
            $table->json('metadata')->nullable();
            $table->integer('popularity_score')->default(0);
            $table->timestamps();

            $table->index(['searchable_type', 'searchable_id']);
            $table->fullText(['title', 'content']);
            $table->index(['category']);
            $table->index(['popularity_score']);
        });

        // User Recommendations Cache
        Schema::create('user_recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('recommendation_type'); // 'content', 'product', 'partner', 'event'
            $table->json('recommended_items'); // Array of IDs with scores
            $table->timestamp('generated_at');
            $table->timestamp('expires_at');
            $table->timestamps();

            $table->unique(['user_id', 'recommendation_type']);
            $table->index(['expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_recommendations');
        Schema::dropIfExists('search_index');
        Schema::dropIfExists('support_ticket_messages');
        Schema::dropIfExists('support_tickets');
        Schema::dropIfExists('event_registrations');
        Schema::dropIfExists('events');
    }
};
