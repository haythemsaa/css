<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use App\Models\UserBadge;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BadgeController extends Controller
{
    /**
     * Get all available badges with user progress
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        $category = $request->input('category');
        $rarity = $request->input('rarity');
        $showUnlockedOnly = $request->boolean('unlocked_only');
        $showLockedOnly = $request->boolean('locked_only');

        $query = Badge::active()->with('statistics');

        // Apply filters
        if ($category) {
            $query->byCategory($category);
        }

        if ($rarity) {
            $query->byRarity($rarity);
        }

        // If user is not authenticated, only show public badges
        if (!$user) {
            $query->public();
        }

        $badges = $query->ordered()->get()->map(function ($badge) use ($user) {
            $badgeData = $badge->toArray();

            if ($user) {
                $userBadge = $badge->getUserProgress($user);

                $badgeData['user_progress'] = [
                    'progress' => $userBadge?->progress ?? 0,
                    'progress_max' => $userBadge?->progress_max ?? $badge->required_count,
                    'progress_percentage' => $userBadge?->progress_percentage ?? 0,
                    'is_unlocked' => $userBadge?->is_unlocked ?? false,
                    'unlocked_at' => $userBadge?->unlocked_at,
                ];

                // Hide details of secret badges if not unlocked
                if ($badge->is_secret && !($userBadge?->is_unlocked ?? false)) {
                    $badgeData['name'] = '???';
                    $badgeData['description'] = 'Badge secret - À découvrir';
                    $badgeData['icon'] = '🔒';
                }
            }

            return $badgeData;
        });

        // Apply progress filters
        if ($showUnlockedOnly && $user) {
            $badges = $badges->filter(fn($b) => $b['user_progress']['is_unlocked']);
        }

        if ($showLockedOnly && $user) {
            $badges = $badges->filter(fn($b) => !$b['user_progress']['is_unlocked']);
        }

        return response()->json([
            'success' => true,
            'data' => $badges->values(),
        ]);
    }

    /**
     * Get a specific badge details
     */
    public function show(Request $request, int $badgeId): JsonResponse
    {
        $user = Auth::user();

        $badge = Badge::with('statistics')->find($badgeId);

        if (!$badge) {
            return response()->json([
                'success' => false,
                'message' => 'Badge non trouvé',
            ], 404);
        }

        $badgeData = $badge->toArray();

        if ($user) {
            $userBadge = $badge->getUserProgress($user);

            $badgeData['user_progress'] = [
                'progress' => $userBadge?->progress ?? 0,
                'progress_max' => $userBadge?->progress_max ?? $badge->required_count,
                'progress_percentage' => $userBadge?->progress_percentage ?? 0,
                'is_unlocked' => $userBadge?->is_unlocked ?? false,
                'unlocked_at' => $userBadge?->unlocked_at,
                'tokens_earned' => $userBadge?->tokens_earned ?? 0,
                'xp_earned' => $userBadge?->xp_earned ?? 0,
            ];

            // Hide details of secret badges if not unlocked
            if ($badge->is_secret && !($userBadge?->is_unlocked ?? false)) {
                $badgeData['name'] = '???';
                $badgeData['description'] = 'Badge secret - Continuez à explorer pour le découvrir!';
                $badgeData['unlock_criteria'] = null;
            }
        }

        return response()->json([
            'success' => true,
            'badge' => $badgeData,
        ]);
    }

    /**
     * Get user's badge summary
     */
    public function getUserSummary(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Authentification requise',
            ], 401);
        }

        $summary = $user->badgeSummary;

        if (!$summary) {
            // Create initial summary
            $user->updateBadgeSummary();
            $summary = $user->badgeSummary()->first();
        }

        // Get recent unlocks
        $recentUnlocks = $user->userBadges()
            ->where('is_unlocked', true)
            ->with('badge')
            ->latest('unlocked_at')
            ->take(5)
            ->get();

        // Get in-progress badges
        $inProgress = $user->userBadges()
            ->inProgress()
            ->with('badge')
            ->orderByDesc('progress_percentage')
            ->take(5)
            ->get();

        return response()->json([
            'success' => true,
            'summary' => $summary,
            'recent_unlocks' => $recentUnlocks,
            'in_progress' => $inProgress,
        ]);
    }

    /**
     * Get badges by category
     */
    public function getByCategory(Request $request): JsonResponse
    {
        $categories = Badge::active()
            ->select('category')
            ->groupBy('category')
            ->get()
            ->pluck('category');

        $data = [];

        foreach ($categories as $category) {
            $badges = Badge::active()->byCategory($category)->ordered()->get();
            $data[$category] = [
                'name' => $category,
                'display' => Badge::find($badges->first()->id)->category_display ?? ucfirst($category),
                'count' => $badges->count(),
                'badges' => $badges,
            ];
        }

        return response()->json([
            'success' => true,
            'categories' => $data,
        ]);
    }

    /**
     * Get leaderboard for badge collectors
     */
    public function getLeaderboard(Request $request): JsonResponse
    {
        $limit = $request->input('limit', 50);
        $user = Auth::user();

        $leaderboard = DB::table('user_badge_summaries')
            ->join('users', 'user_badge_summaries.user_id', '=', 'users.id')
            ->select(
                'users.id',
                'users.first_name',
                'users.last_name',
                'users.profile_photo',
                'user_badge_summaries.total_badges',
                'user_badge_summaries.legendary_badges',
                'user_badge_summaries.epic_badges',
                'user_badge_summaries.rare_badges',
                'user_badge_summaries.common_badges',
                'user_badge_summaries.completion_percentage',
                DB::raw('ROW_NUMBER() OVER (ORDER BY total_badges DESC, legendary_badges DESC, epic_badges DESC) as rank')
            )
            ->orderByDesc('total_badges')
            ->orderByDesc('legendary_badges')
            ->orderByDesc('epic_badges')
            ->limit($limit)
            ->get();

        // Get current user's rank if authenticated
        $userRank = null;
        if ($user) {
            $userSummary = $user->badgeSummary;
            if ($userSummary) {
                $userRank = DB::table('user_badge_summaries')
                    ->where('total_badges', '>', $userSummary->total_badges)
                    ->orWhere(function ($q) use ($userSummary) {
                        $q->where('total_badges', '=', $userSummary->total_badges)
                          ->where('legendary_badges', '>', $userSummary->legendary_badges);
                    })
                    ->count() + 1;
            }
        }

        return response()->json([
            'success' => true,
            'leaderboard' => $leaderboard,
            'user_rank' => $userRank,
        ]);
    }

    /**
     * Manually unlock a badge (admin only or for testing)
     */
    public function unlockBadge(Request $request, int $badgeId): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Authentification requise',
            ], 401);
        }

        $badge = Badge::find($badgeId);

        if (!$badge) {
            return response()->json([
                'success' => false,
                'message' => 'Badge non trouvé',
            ], 404);
        }

        // Check if already unlocked
        $userBadge = $badge->getUserProgress($user);

        if ($userBadge && $userBadge->is_unlocked) {
            return response()->json([
                'success' => false,
                'message' => 'Badge déjà débloqué',
            ], 400);
        }

        // Create or update user badge
        if (!$userBadge) {
            $userBadge = UserBadge::create([
                'user_id' => $user->id,
                'badge_id' => $badge->id,
                'progress' => $badge->required_count,
                'progress_max' => $badge->required_count,
            ]);
        }

        // Unlock the badge
        $unlocked = $userBadge->unlock();

        if ($unlocked) {
            return response()->json([
                'success' => true,
                'message' => 'Badge débloqué avec succès!',
                'badge' => $badge,
                'tokens_earned' => $userBadge->tokens_earned,
                'xp_earned' => $userBadge->xp_earned,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Erreur lors du déverrouillage du badge',
        ], 500);
    }

    /**
     * Update badge progress (for auto-unlock badges)
     */
    public function updateProgress(Request $request, int $badgeId): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Authentification requise',
            ], 401);
        }

        $request->validate([
            'progress' => 'required|integer|min:0',
        ]);

        $badge = Badge::find($badgeId);

        if (!$badge) {
            return response()->json([
                'success' => false,
                'message' => 'Badge non trouvé',
            ], 404);
        }

        // Get or create user badge
        $userBadge = $badge->getUserProgress($user);

        if (!$userBadge) {
            $userBadge = UserBadge::create([
                'user_id' => $user->id,
                'badge_id' => $badge->id,
                'progress' => 0,
                'progress_max' => $badge->required_count,
            ]);
        }

        // Update progress
        $unlocked = $userBadge->incrementProgress($request->input('progress'));

        return response()->json([
            'success' => true,
            'progress' => $userBadge->progress,
            'progress_percentage' => $userBadge->progress_percentage,
            'unlocked' => $unlocked,
            'message' => $unlocked ? 'Badge débloqué!' : 'Progression mise à jour',
        ]);
    }

    // ========================================
    // ADMIN METHODS
    // ========================================

    /**
     * Admin: Get all badges
     */
    public function adminIndex(Request $request): JsonResponse
    {
        $query = Badge::query()->with('statistics');

        // Filter by category
        if ($request->has('category')) {
            $query->byCategory($request->category);
        }

        // Filter by rarity
        if ($request->has('rarity')) {
            $query->byRarity($request->rarity);
        }

        // Filter by active status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Filter by secret
        if ($request->has('is_secret')) {
            $query->where('is_secret', $request->boolean('is_secret'));
        }

        // Search
        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('description', 'like', "%{$request->search}%");
            });
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $badges = $query->paginate($request->get('per_page', 20));

        return response()->json($badges);
    }

    /**
     * Admin: Create a new badge
     */
    public function adminStore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'icon' => 'required|string|max:255',
            'category' => 'required|in:engagement,social,donation,content,match,collection,achievement,special',
            'rarity' => 'required|in:common,rare,epic,legendary',
            'required_count' => 'required|integer|min:1',
            'unlock_type' => 'required|in:auto,manual,purchase',
            'unlock_criteria' => 'nullable|array',
            'token_reward' => 'nullable|integer|min:0',
            'xp_reward' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'is_secret' => 'boolean',
            'display_order' => 'nullable|integer',
        ]);

        $badge = Badge::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Badge créé avec succès',
            'badge' => $badge,
        ], 201);
    }

    /**
     * Admin: Update a badge
     */
    public function adminUpdate(Request $request, int $id): JsonResponse
    {
        $badge = Badge::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'icon' => 'sometimes|string|max:255',
            'category' => 'sometimes|in:engagement,social,donation,content,match,collection,achievement,special',
            'rarity' => 'sometimes|in:common,rare,epic,legendary',
            'required_count' => 'sometimes|integer|min:1',
            'unlock_type' => 'sometimes|in:auto,manual,purchase',
            'unlock_criteria' => 'nullable|array',
            'token_reward' => 'nullable|integer|min:0',
            'xp_reward' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'is_secret' => 'boolean',
            'display_order' => 'nullable|integer',
        ]);

        $badge->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Badge mis à jour avec succès',
            'badge' => $badge->fresh(),
        ]);
    }

    /**
     * Admin: Delete a badge
     */
    public function adminDestroy(int $id): JsonResponse
    {
        $badge = Badge::findOrFail($id);

        // Delete user badges
        UserBadge::where('badge_id', $badge->id)->delete();

        // Delete badge
        $badge->delete();

        return response()->json([
            'success' => true,
            'message' => 'Badge supprimé avec succès',
        ]);
    }
}
