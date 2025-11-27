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
}
