<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ForumCategory;
use App\Models\ForumTopic;
use App\Models\ForumReply;
use Illuminate\Http\Request;

class ForumController extends Controller
{
    /**
     * Get all forum categories
     */
    public function categories(Request $request)
    {
        $user = $request->user();

        $categories = ForumCategory::active()
            ->withCount('topics')
            ->with('latestTopic')
            ->get()
            ->filter(function ($category) use ($user) {
                return $category->canBeAccessedBy($user);
            });

        return response()->json($categories);
    }

    /**
     * Get topics (optionally filtered by category)
     */
    public function topics(Request $request)
    {
        $query = ForumTopic::with(['user', 'category', 'latestReply'])
            ->active()
            ->withCount('replies');

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Sort pinned first, then by latest activity
        $query->orderBy('is_pinned', 'desc')
            ->latest();

        $topics = $query->paginate(20);

        return response()->json($topics);
    }

    /**
     * Get a single topic with replies
     */
    public function showTopic($id)
    {
        $topic = ForumTopic::with(['user', 'category'])
            ->withCount('replies')
            ->findOrFail($id);

        // Increment views
        $topic->incrementViews();

        $replies = ForumReply::with('user')
            ->forTopic($topic->id)
            ->topLevel()
            ->latest()
            ->paginate(20);

        return response()->json([
            'topic' => $topic,
            'replies' => $replies,
        ]);
    }

    /**
     * Create a new topic
     */
    public function createTopic(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:forum_categories,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $user = $request->user();

        $topic = ForumTopic::create([
            'category_id' => $request->category_id,
            'user_id' => $user->id,
            'title' => $request->title,
            'content' => $request->content,
        ]);

        return response()->json([
            'message' => 'Topic created successfully',
            'topic' => $topic->load(['user', 'category']),
        ], 201);
    }

    /**
     * Update a topic
     */
    public function updateTopic(Request $request, $id)
    {
        $request->validate([
            'title' => 'sometimes|string|max:255',
            'content' => 'sometimes|string',
        ]);

        $user = $request->user();
        $topic = ForumTopic::findOrFail($id);

        if ($topic->user_id !== $user->id && $user->user_type !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $topic->update($request->only(['title', 'content']));

        return response()->json([
            'message' => 'Topic updated successfully',
            'topic' => $topic->fresh(),
        ]);
    }

    /**
     * Delete a topic
     */
    public function deleteTopic($id)
    {
        $user = request()->user();
        $topic = ForumTopic::findOrFail($id);

        if ($topic->user_id !== $user->id && $user->user_type !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $topic->delete();

        return response()->json(['message' => 'Topic deleted successfully']);
    }

    /**
     * Reply to a topic
     */
    public function reply(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string',
            'parent_id' => 'nullable|exists:forum_replies,id',
        ]);

        $user = $request->user();
        $topic = ForumTopic::findOrFail($id);

        if ($topic->is_locked) {
            return response()->json(['message' => 'Topic is locked'], 403);
        }

        $reply = ForumReply::create([
            'topic_id' => $topic->id,
            'user_id' => $user->id,
            'content' => $request->content,
            'parent_id' => $request->parent_id,
        ]);

        return response()->json([
            'message' => 'Reply posted successfully',
            'reply' => $reply->load('user'),
        ], 201);
    }

    /**
     * Update a reply
     */
    public function updateReply(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $user = $request->user();
        $reply = ForumReply::findOrFail($id);

        if (!$reply->canBeEditedBy($user)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $reply->content = $request->content;
        $reply->markAsEdited();

        return response()->json([
            'message' => 'Reply updated successfully',
            'reply' => $reply->fresh(),
        ]);
    }

    /**
     * Delete a reply
     */
    public function deleteReply($id)
    {
        $user = request()->user();
        $reply = ForumReply::findOrFail($id);

        if (!$reply->canBeDeletedBy($user)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $reply->delete();

        return response()->json(['message' => 'Reply deleted successfully']);
    }

    /**
     * Like a topic
     */
    public function likeTopic(Request $request, $id)
    {
        $user = $request->user();
        $topic = ForumTopic::findOrFail($id);

        $topic->toggleLike($user);

        return response()->json([
            'message' => 'Topic liked',
            'likes_count' => $topic->fresh()->likes_count,
        ]);
    }

    /**
     * Like a reply
     */
    public function likeReply(Request $request, $id)
    {
        $user = $request->user();
        $reply = ForumReply::findOrFail($id);

        $reply->toggleLike($user);

        return response()->json([
            'message' => 'Reply liked',
            'likes_count' => $reply->fresh()->likes_count,
        ]);
    }

    // ========================================
    // ADMIN MODERATION METHODS
    // ========================================

    /**
     * Admin: Get all topics (for moderation)
     */
    public function adminIndex(Request $request)
    {
        $query = ForumTopic::query()->with(['user', 'category', 'latestReply'])->withCount('replies');

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by locked/pinned
        if ($request->has('is_locked')) {
            $query->where('is_locked', $request->boolean('is_locked'));
        }

        if ($request->has('is_pinned')) {
            $query->where('is_pinned', $request->boolean('is_pinned'));
        }

        // Search
        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('content', 'like', "%{$request->search}%");
            });
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $topics = $query->paginate($request->get('per_page', 20));

        return response()->json($topics);
    }

    /**
     * Admin: Pin/unpin or lock/unlock a topic
     */
    public function adminUpdate(Request $request, $id)
    {
        $topic = ForumTopic::findOrFail($id);

        $validated = $request->validate([
            'is_pinned' => 'nullable|boolean',
            'is_locked' => 'nullable|boolean',
        ]);

        $topic->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Topic modéré avec succès',
            'topic' => $topic->fresh(),
        ]);
    }

    /**
     * Admin: Delete a topic (moderation)
     */
    public function adminDestroy($id)
    {
        $topic = ForumTopic::findOrFail($id);

        // Delete replies
        ForumReply::where('topic_id', $topic->id)->delete();

        // Delete topic
        $topic->delete();

        return response()->json([
            'success' => true,
            'message' => 'Topic supprimé avec succès',
        ]);
    }
}
