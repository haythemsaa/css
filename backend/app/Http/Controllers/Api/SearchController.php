<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Content;
use App\Models\Product;
use App\Models\Player;
use App\Models\Match;
use App\Models\Partner;
use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'query' => 'required|string|min:2',
            'type' => 'nullable|string|in:all,content,products,players,matches,partners,events',
            'limit' => 'nullable|integer|min:1|max:50',
        ]);

        $query = $request->input('query');
        $type = $request->input('type', 'all');
        $limit = $request->input('limit', 10);

        $results = [];

        if ($type === 'all' || $type === 'content') {
            $results['content'] = Content::where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('excerpt', 'like', "%{$query}%");
            })
            ->where('status', 'published')
            ->limit($limit)
            ->get(['id', 'title', 'slug', 'type', 'thumbnail']);
        }

        if ($type === 'all' || $type === 'products') {
            $results['products'] = Product::where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            })
            ->where('is_available', true)
            ->limit($limit)
            ->get(['id', 'name', 'slug', 'price', 'images']);
        }

        if ($type === 'all' || $type === 'players') {
            $results['players'] = Player::where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('position', 'like', "%{$query}%");
            })
            ->limit($limit)
            ->get(['id', 'name', 'position', 'photo', 'jersey_number']);
        }

        if ($type === 'all' || $type === 'matches') {
            $results['matches'] = Match::where(function ($q) use ($query) {
                $q->where('home_team', 'like', "%{$query}%")
                  ->orWhere('away_team', 'like', "%{$query}%")
                  ->orWhere('competition', 'like', "%{$query}%");
            })
            ->limit($limit)
            ->get(['id', 'home_team', 'away_team', 'match_date', 'competition']);
        }

        if ($type === 'all' || $type === 'partners') {
            $results['partners'] = Partner::where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            })
            ->where('is_active', true)
            ->limit($limit)
            ->get(['id', 'name', 'logo', 'category']);
        }

        if ($type === 'all' || $type === 'events') {
            $results['events'] = Event::where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            })
            ->where('status', 'upcoming')
            ->limit($limit)
            ->get(['id', 'title', 'slug', 'start_datetime', 'venue']);
        }

        return response()->json([
            'query' => $query,
            'results' => $results,
        ]);
    }

    public function trending(Request $request): JsonResponse
    {
        // Get trending searches (most searched in last 7 days)
        $trending = DB::table('user_activities')
            ->where('activity_type', 'search')
            ->where('created_at', '>=', now()->subDays(7))
            ->select('metadata->query as query', DB::raw('count(*) as count'))
            ->groupBy('query')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        return response()->json(['trending' => $trending]);
    }

    public function suggestions(Request $request): JsonResponse
    {
        $request->validate([
            'query' => 'required|string|min:1',
        ]);

        $query = $request->input('query');

        // Get auto-complete suggestions
        $suggestions = [];

        // Content titles
        $contentSuggestions = Content::where('title', 'like', "{$query}%")
            ->where('status', 'published')
            ->limit(5)
            ->pluck('title');

        // Product names
        $productSuggestions = Product::where('name', 'like', "{$query}%")
            ->where('is_available', true)
            ->limit(5)
            ->pluck('name');

        $suggestions = $contentSuggestions->merge($productSuggestions)->unique()->take(10);

        return response()->json(['suggestions' => $suggestions]);
    }
}
