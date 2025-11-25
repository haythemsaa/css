<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserFollow;
use App\Models\TimelinePost;
use App\Models\TimelinePostLike;
use App\Models\Wishlist;
use App\Models\SavedContent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SocialController extends Controller
{
    // ========== Follow System ==========

    public function follow(Request $request, int $userId): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Authentification requise'], 401);
        }

        if ($user->id === $userId) {
            return response()->json(['message' => 'Vous ne pouvez pas vous suivre vous-même'], 400);
        }

        $targetUser = User::findOrFail($userId);

        if (UserFollow::where('follower_id', $user->id)->where('following_id', $userId)->exists()) {
            return response()->json(['message' => 'Vous suivez déjà cet utilisateur'], 400);
        }

        UserFollow::create([
            'follower_id' => $user->id,
            'following_id' => $userId,
        ]);

        return response()->json(['message' => 'Utilisateur suivi avec succès']);
    }

    public function unfollow(Request $request, int $userId): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Authentification requise'], 401);
        }

        UserFollow::where('follower_id', $user->id)
            ->where('following_id', $userId)
            ->delete();

        return response()->json(['message' => 'Vous ne suivez plus cet utilisateur']);
    }

    public function followers(Request $request, int $userId): JsonResponse
    {
        $followers = UserFollow::with('follower')
            ->where('following_id', $userId)
            ->paginate(20);

        return response()->json([
            'followers' => $followers->map(fn($follow) => [
                'id' => $follow->follower->id,
                'name' => $follow->follower->name,
                'avatar' => $follow->follower->avatar,
                'followed_at' => $follow->created_at,
            ]),
        ]);
    }

    public function following(Request $request, int $userId): JsonResponse
    {
        $following = UserFollow::with('following')
            ->where('follower_id', $userId)
            ->paginate(20);

        return response()->json([
            'following' => $following->map(fn($follow) => [
                'id' => $follow->following->id,
                'name' => $follow->following->name,
                'avatar' => $follow->following->avatar,
                'followed_at' => $follow->created_at,
            ]),
        ]);
    }

    // ========== Timeline/Feed ==========

    public function timeline(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Authentification requise'], 401);
        }

        // Get posts from followed users
        $followingIds = UserFollow::where('follower_id', $user->id)->pluck('following_id');
        $followingIds->push($user->id); // Include own posts

        $posts = TimelinePost::with(['user', 'likes', 'comments'])
            ->whereIn('user_id', $followingIds)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json(['timeline' => $posts]);
    }

    public function createPost(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Authentification requise'], 401);
        }

        $request->validate([
            'content' => 'required|string|max:1000',
            'post_type' => 'nullable|string|in:text,image,video,achievement',
            'media' => 'nullable|array',
        ]);

        $post = TimelinePost::create([
            'user_id' => $user->id,
            'content' => $request->input('content'),
            'post_type' => $request->input('post_type', 'text'),
            'media' => $request->input('media'),
        ]);

        return response()->json([
            'message' => 'Post créé',
            'post' => $post,
        ], 201);
    }

    public function likePost(Request $request, int $postId): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Authentification requise'], 401);
        }

        $post = TimelinePost::findOrFail($postId);

        if (TimelinePostLike::where('post_id', $postId)->where('user_id', $user->id)->exists()) {
            return response()->json(['message' => 'Déjà liké'], 400);
        }

        TimelinePostLike::create([
            'post_id' => $postId,
            'user_id' => $user->id,
        ]);

        $post->increment('likes_count');

        return response()->json(['message' => 'Post liké']);
    }

    public function unlikePost(Request $request, int $postId): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Authentification requise'], 401);
        }

        $post = TimelinePost::findOrFail($postId);

        TimelinePostLike::where('post_id', $postId)
            ->where('user_id', $user->id)
            ->delete();

        $post->decrement('likes_count');

        return response()->json(['message' => 'Like retiré']);
    }

    // ========== Wishlist ==========

    public function addToWishlist(Request $request, int $productId): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Authentification requise'], 401);
        }

        if (Wishlist::where('user_id', $user->id)->where('product_id', $productId)->exists()) {
            return response()->json(['message' => 'Déjà dans la wishlist'], 400);
        }

        Wishlist::create([
            'user_id' => $user->id,
            'product_id' => $productId,
        ]);

        return response()->json(['message' => 'Ajouté à la wishlist']);
    }

    public function removeFromWishlist(Request $request, int $productId): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Authentification requise'], 401);
        }

        Wishlist::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->delete();

        return response()->json(['message' => 'Retiré de la wishlist']);
    }

    public function wishlist(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Authentification requise'], 401);
        }

        $wishlist = Wishlist::with('product')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json(['wishlist' => $wishlist]);
    }

    // ========== Saved Content ==========

    public function saveContent(Request $request, int $contentId): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Authentification requise'], 401);
        }

        if (SavedContent::where('user_id', $user->id)->where('content_id', $contentId)->exists()) {
            return response()->json(['message' => 'Déjà sauvegardé'], 400);
        }

        SavedContent::create([
            'user_id' => $user->id,
            'content_id' => $contentId,
            'collection' => $request->input('collection', 'default'),
        ]);

        return response()->json(['message' => 'Contenu sauvegardé']);
    }

    public function unsaveContent(Request $request, int $contentId): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Authentification requise'], 401);
        }

        SavedContent::where('user_id', $user->id)
            ->where('content_id', $contentId)
            ->delete();

        return response()->json(['message' => 'Contenu retiré']);
    }

    public function savedContent(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Authentification requise'], 401);
        }

        $saved = SavedContent::with('content')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json(['saved_content' => $saved]);
    }
}
