<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Content;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ContentController extends Controller
{
    /**
     * Get all contents with filters
     */
    public function index(Request $request): JsonResponse
    {
        $query = Content::query()
            ->with(['category', 'author', 'tags'])
            ->published();

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by access level based on user
        $user = $request->user();
        if (!$user || $user->user_type === 'free') {
            $query->free();
        }

        // Search
        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('excerpt', 'like', "%{$request->search}%");
            });
        }

        // Sort
        $sortBy = $request->get('sort_by', 'published_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $contents = $query->paginate($request->get('per_page', 20));

        return response()->json($contents);
    }

    /**
     * Get featured contents
     */
    public function featured(): JsonResponse
    {
        $contents = Content::query()
            ->with(['category', 'author'])
            ->published()
            ->featured()
            ->limit(10)
            ->get();

        return response()->json($contents);
    }

    /**
     * Get trending contents
     */
    public function trending(): JsonResponse
    {
        $contents = Content::query()
            ->with(['category', 'author'])
            ->published()
            ->trending()
            ->limit(10)
            ->get();

        return response()->json($contents);
    }

    /**
     * Get single content by slug
     */
    public function show(Request $request, string $slug): JsonResponse
    {
        $content = Content::query()
            ->with(['category', 'author', 'tags'])
            ->where('slug', $slug)
            ->firstOrFail();

        // Check access permission
        $user = $request->user();
        if (!$content->canBeAccessedBy($user)) {
            return response()->json([
                'message' => 'Premium subscription required to access this content',
                'required_level' => $content->access_level,
            ], 403);
        }

        // Increment views
        $content->incrementViews();

        return response()->json($content);
    }

    /**
     * Like content
     */
    public function like(Request $request, int $id): JsonResponse
    {
        $content = Content::findOrFail($id);

        // Check if already liked (would need a likes table in real app)
        $content->increment('likes_count');

        return response()->json([
            'message' => 'Content liked',
            'likes_count' => $content->likes_count,
        ]);
    }

    /**
     * Unlike content
     */
    public function unlike(Request $request, int $id): JsonResponse
    {
        $content = Content::findOrFail($id);

        $content->decrement('likes_count');

        return response()->json([
            'message' => 'Content unliked',
            'likes_count' => $content->likes_count,
        ]);
    }

    /**
     * Share content
     */
    public function share(Request $request, int $id): JsonResponse
    {
        $content = Content::findOrFail($id);

        $content->increment('shares_count');

        return response()->json([
            'message' => 'Content shared',
            'shares_count' => $content->shares_count,
        ]);
    }

    /**
     * Stream video
     */
    public function streamVideo(Request $request, int $id): JsonResponse
    {
        $content = Content::findOrFail($id);

        if ($content->type !== 'video') {
            return response()->json([
                'message' => 'This content is not a video',
            ], 400);
        }

        // Check access
        $user = $request->user();
        if (!$content->canBeAccessedBy($user)) {
            return response()->json([
                'message' => 'Premium subscription required',
            ], 403);
        }

        // Return streaming URL (Cloudflare Stream)
        return response()->json([
            'stream_url' => $content->video_url,
            'video_id' => $content->video_id,
            'duration' => $content->video_duration,
        ]);
    }

    // ========================================
    // ADMIN METHODS
    // ========================================

    public function adminStore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|unique:contents,slug',
            'type' => 'required|in:article,video,gallery,poll',
            'category_id' => 'required|exists:content_categories,id',
            'excerpt' => 'nullable|string',
            'body' => 'required|string',
            'featured_image' => 'nullable|string',
            'video_url' => 'nullable|string',
            'video_id' => 'nullable|string',
            'video_duration' => 'nullable|integer',
            'access_level' => 'required|in:free,premium,socios',
            'is_featured' => 'boolean',
            'published_at' => 'nullable|date',
            'tags' => 'nullable|array',
            'metadata' => 'nullable|array',
        ]);

        $validated['author_id'] = $request->user()->id;
        $content = Content::create($validated);

        if (isset($validated['tags'])) {
            $content->tags()->sync($validated['tags']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Contenu créé avec succès',
            'data' => $content->load(['category', 'author', 'tags']),
        ], 201);
    }

    public function adminUpdate(Request $request, int $id): JsonResponse
    {
        $content = Content::findOrFail($id);

        $validated = $request->validate([
            'title' => 'string|max:255',
            'slug' => 'string|unique:contents,slug,' . $id,
            'type' => 'in:article,video,gallery,poll',
            'category_id' => 'exists:content_categories,id',
            'excerpt' => 'nullable|string',
            'body' => 'string',
            'featured_image' => 'nullable|string',
            'video_url' => 'nullable|string',
            'video_id' => 'nullable|string',
            'video_duration' => 'nullable|integer',
            'access_level' => 'in:free,premium,socios',
            'is_featured' => 'boolean',
            'published_at' => 'nullable|date',
            'tags' => 'nullable|array',
            'metadata' => 'nullable|array',
        ]);

        $content->update($validated);

        if (isset($validated['tags'])) {
            $content->tags()->sync($validated['tags']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Contenu mis à jour avec succès',
            'data' => $content->fresh(['category', 'author', 'tags']),
        ]);
    }

    public function adminDestroy(int $id): JsonResponse
    {
        $content = Content::findOrFail($id);
        $content->delete();

        return response()->json([
            'success' => true,
            'message' => 'Contenu supprimé avec succès',
        ]);
    }
}
