<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ChallengeResource;
use App\Models\Challenge;
use App\Models\UserChallenge;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ChallengeController extends Controller
{
    /**
     * Display a listing of challenges
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $request->validate([
            'type' => 'nullable|string|in:daily,weekly,monthly,special,seasonal',
            'category' => 'nullable|string|in:engagement,social,donation,content,match',
            'difficulty' => 'nullable|string|in:easy,medium,hard,expert',
            'status' => 'nullable|string|in:available,featured,all',
        ]);

        $query = Challenge::query();

        if ($request->input('status') !== 'all') {
            $query->active();
        }

        if ($request->has('type')) {
            $query->ofType($request->input('type'));
        }

        if ($request->has('category')) {
            $query->ofCategory($request->input('category'));
        }

        if ($request->has('difficulty')) {
            $query->ofDifficulty($request->input('difficulty'));
        }

        if ($request->input('status') === 'featured') {
            $query->featured();
        }

        $challenges = $query->orderBy('is_featured', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return ChallengeResource::collection($challenges);
    }

    /**
     * Display a single challenge
     */
    public function show(Challenge $challenge): ChallengeResource
    {
        return new ChallengeResource($challenge);
    }

    /**
     * Get user's challenges (enrolled)
     */
    public function userChallenges(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Authentification requise',
            ], 401);
        }

        $request->validate([
            'status' => 'nullable|string|in:completed,in_progress,not_started,all',
        ]);

        $query = UserChallenge::query()
            ->with('challenge')
            ->where('user_id', $user->id);

        switch ($request->input('status')) {
            case 'completed':
                $query->completed();
                break;
            case 'in_progress':
                $query->inProgress();
                break;
            case 'not_started':
                $query->notStarted();
                break;
        }

        $userChallenges = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'challenges' => $userChallenges->map(function ($uc) {
                return [
                    'id' => $uc->id,
                    'challenge' => new ChallengeResource($uc->challenge),
                    'current_progress' => $uc->current_progress,
                    'target_value' => $uc->target_value,
                    'progress_percentage' => $uc->progress_percentage,
                    'is_completed' => $uc->is_completed,
                    'reward_claimed' => $uc->reward_claimed,
                    'started_at' => $uc->started_at?->format('Y-m-d H:i:s'),
                    'completed_at' => $uc->completed_at?->format('Y-m-d H:i:s'),
                ];
            }),
        ]);
    }

    /**
     * Enroll in a challenge
     */
    public function enroll(Request $request, Challenge $challenge): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Authentification requise',
            ], 401);
        }

        if (!$challenge->is_available) {
            return response()->json([
                'message' => 'Ce défi n\'est pas disponible',
            ], 400);
        }

        // Check if already enrolled
        $existing = UserChallenge::where('user_id', $user->id)
            ->where('challenge_id', $challenge->id)
            ->where('is_completed', false)
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'Vous êtes déjà inscrit à ce défi',
            ], 400);
        }

        // Check completion count
        $completionCount = UserChallenge::where('user_id', $user->id)
            ->where('challenge_id', $challenge->id)
            ->where('is_completed', true)
            ->count();

        if ($completionCount >= $challenge->max_completions) {
            return response()->json([
                'message' => 'Vous avez déjà atteint le nombre maximum de complétion pour ce défi',
            ], 400);
        }

        $userChallenge = UserChallenge::create([
            'user_id' => $user->id,
            'challenge_id' => $challenge->id,
            'target_value' => $challenge->target_value,
            'started_at' => now(),
        ]);

        return response()->json([
            'message' => 'Inscription au défi réussie',
            'user_challenge' => [
                'id' => $userChallenge->id,
                'challenge' => new ChallengeResource($challenge),
                'current_progress' => 0,
                'target_value' => $challenge->target_value,
                'progress_percentage' => 0,
            ],
        ], 201);
    }

    /**
     * Update challenge progress
     */
    public function updateProgress(Request $request, int $userChallengeId): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Authentification requise',
            ], 401);
        }

        $userChallenge = UserChallenge::where('id', $userChallengeId)
            ->where('user_id', $user->id)
            ->first();

        if (!$userChallenge) {
            return response()->json([
                'message' => 'Défi non trouvé',
            ], 404);
        }

        $request->validate([
            'progress' => 'required|integer|min:0',
        ]);

        $userChallenge->updateProgress($request->input('progress'));

        return response()->json([
            'message' => 'Progression mise à jour',
            'user_challenge' => [
                'id' => $userChallenge->id,
                'current_progress' => $userChallenge->current_progress,
                'target_value' => $userChallenge->target_value,
                'progress_percentage' => $userChallenge->progress_percentage,
                'is_completed' => $userChallenge->is_completed,
                'completed_at' => $userChallenge->completed_at?->format('Y-m-d H:i:s'),
            ],
        ]);
    }

    /**
     * Claim challenge reward
     */
    public function claimReward(Request $request, int $userChallengeId): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Authentification requise',
            ], 401);
        }

        $userChallenge = UserChallenge::where('id', $userChallengeId)
            ->where('user_id', $user->id)
            ->with('challenge')
            ->first();

        if (!$userChallenge) {
            return response()->json([
                'message' => 'Défi non trouvé',
            ], 404);
        }

        if (!$userChallenge->is_completed) {
            return response()->json([
                'message' => 'Le défi n\'est pas encore terminé',
            ], 400);
        }

        if ($userChallenge->reward_claimed) {
            return response()->json([
                'message' => 'Récompense déjà réclamée',
            ], 400);
        }

        $success = $userChallenge->claimReward();

        if (!$success) {
            return response()->json([
                'message' => 'Impossible de réclamer la récompense',
            ], 500);
        }

        return response()->json([
            'message' => 'Récompense réclamée avec succès',
            'rewards' => [
                'points' => $userChallenge->challenge->points_reward,
                'additional' => $userChallenge->challenge->additional_rewards,
            ],
        ]);
    }

    // ========================================
    // ADMIN METHODS
    // ========================================

    /**
     * Admin: Get all challenges
     */
    public function adminIndex(Request $request): JsonResponse
    {
        $query = Challenge::query();

        // Filter by type
        if ($request->has('type')) {
            $query->ofType($request->type);
        }

        // Filter by category
        if ($request->has('category')) {
            $query->ofCategory($request->category);
        }

        // Filter by difficulty
        if ($request->has('difficulty')) {
            $query->ofDifficulty($request->difficulty);
        }

        // Filter by active status
        if ($request->has('is_available')) {
            $query->where('is_available', $request->boolean('is_available'));
        }

        // Filter by featured
        if ($request->has('is_featured')) {
            $query->where('is_featured', $request->boolean('is_featured'));
        }

        // Search
        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('description', 'like', "%{$request->search}%");
            });
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $challenges = $query->paginate($request->get('per_page', 20));

        return response()->json($challenges);
    }

    /**
     * Admin: Create a new challenge
     */
    public function adminStore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:daily,weekly,monthly,special,seasonal',
            'category' => 'required|in:engagement,social,donation,content,match',
            'difficulty' => 'required|in:easy,medium,hard,expert',
            'target_value' => 'required|integer|min:1',
            'points_reward' => 'required|integer|min:0',
            'token_reward' => 'nullable|integer|min:0',
            'additional_rewards' => 'nullable|array',
            'max_completions' => 'nullable|integer|min:1',
            'starts_at' => 'required|date',
            'ends_at' => 'nullable|date|after:starts_at',
            'is_available' => 'boolean',
            'is_featured' => 'boolean',
        ]);

        $challenge = Challenge::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Défi créé avec succès',
            'challenge' => $challenge,
        ], 201);
    }

    /**
     * Admin: Update a challenge
     */
    public function adminUpdate(Request $request, Challenge $challenge): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'type' => 'sometimes|in:daily,weekly,monthly,special,seasonal',
            'category' => 'sometimes|in:engagement,social,donation,content,match',
            'difficulty' => 'sometimes|in:easy,medium,hard,expert',
            'target_value' => 'sometimes|integer|min:1',
            'points_reward' => 'sometimes|integer|min:0',
            'token_reward' => 'nullable|integer|min:0',
            'additional_rewards' => 'nullable|array',
            'max_completions' => 'nullable|integer|min:1',
            'starts_at' => 'sometimes|date',
            'ends_at' => 'nullable|date|after:starts_at',
            'is_available' => 'boolean',
            'is_featured' => 'boolean',
        ]);

        $challenge->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Défi mis à jour avec succès',
            'challenge' => $challenge->fresh(),
        ]);
    }

    /**
     * Admin: Delete a challenge
     */
    public function adminDestroy(Challenge $challenge): JsonResponse
    {
        // Delete user challenges
        $challenge->userChallenges()->delete();

        // Delete challenge
        $challenge->delete();

        return response()->json([
            'success' => true,
            'message' => 'Défi supprimé avec succès',
        ]);
    }
}
