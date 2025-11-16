<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContentResource;
use App\Models\Content;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContentController extends Controller
{
    /**
     * Get all content (with filters)
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $userType = $user?->user_type ?? 'free';

        $query = Content::query()
            ->with(['author', 'video'])
            ->published();

        // Filter by content type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Filter premium content based on user type
        if ($userType === 'free') {
            $query->where('is_premium', false);
        }

        // Featured content only
        if ($request->boolean('featured')) {
            $query->where('is_featured', true);
        }

        // Search
        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('excerpt', 'like', '%' . $request->search . '%');
            });
        }

        // Order by
        $orderBy = $request->input('order_by', 'published_at');
        $orderDirection = $request->input('order_direction', 'desc');
        $query->orderBy($orderBy, $orderDirection);

        $contents = $query->paginate($request->input('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => ContentResource::collection($contents),
            'meta' => [
                'current_page' => $contents->currentPage(),
                'last_page' => $contents->lastPage(),
                'per_page' => $contents->perPage(),
                'total' => $contents->total(),
            ],
        ]);
    }

    /**
     * Get featured content
     */
    public function featured(Request $request): JsonResponse
    {
        $user = $request->user();
        $userType = $user?->user_type ?? 'free';

        $query = Content::query()
            ->with(['author', 'video'])
            ->published()
            ->where('is_featured', true);

        if ($userType === 'free') {
            $query->where('is_premium', false);
        }

        $contents = $query->orderByDesc('published_at')->limit(10)->get();

        return response()->json([
            'success' => true,
            'data' => ContentResource::collection($contents),
        ]);
    }

    /**
     * Get single content
     */
    public function show(Request $request, string $slug): JsonResponse
    {
        $user = $request->user();
        $userType = $user?->user_type ?? 'free';

        $content = Content::where('slug', $slug)
            ->with(['author', 'video'])
            ->published()
            ->firstOrFail();

        // Check if user can access premium content
        if ($content->is_premium && $userType === 'free') {
            return response()->json([
                'success' => false,
                'message' => 'Ce contenu est réservé aux membres Premium et Socios',
                'upgrade_required' => true,
            ], 403);
        }

        // Increment views
        $content->increment('views_count');

        return response()->json([
            'success' => true,
            'data' => new ContentResource($content),
        ]);
    }

    /**
     * Like/Unlike content
     */
    public function toggleLike(Request $request, string $slug): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Vous devez être connecté pour aimer un contenu',
            ], 401);
        }

        $content = Content::where('slug', $slug)->firstOrFail();

        // Check if user already liked
        $existingLike = $content->likes()->where('user_id', $user->id)->first();

        if ($existingLike) {
            // Unlike
            $existingLike->delete();
            $content->decrement('likes_count');
            $liked = false;
        } else {
            // Like
            $content->likes()->create(['user_id' => $user->id]);
            $content->increment('likes_count');
            $liked = true;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'liked' => $liked,
                'likes_count' => $content->fresh()->likes_count,
            ],
        ]);
    }
}
