<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Poll;
use App\Models\PollVote;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PollController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Poll::with(['options', 'statistics', 'creator'])
            ->withCount('votes');

        if ($request->has('status')) {
            if ($request->status === 'active') {
                $query->active();
            } else {
                $query->where('status', $request->status);
            }
        }

        if ($request->has('category')) {
            $query->byCategory($request->category);
        }

        if ($request->boolean('featured')) {
            $query->featured();
        }

        $user = $request->user();
        if (!$user || !$user->is_socios) {
            $query->public();
        }

        $query->orderByDesc('is_featured')
            ->orderByDesc('created_at');

        $polls = $query->paginate($request->input('per_page', 15));

        if ($user) {
            $polls->getCollection()->transform(function ($poll) use ($user) {
                $poll->user_has_voted = $poll->hasVoted($user);
                $poll->user_can_vote = $poll->canVote($user);
                return $poll;
            });
        }

        return response()->json([
            'polls' => $polls->items(),
            'pagination' => [
                'current_page' => $polls->currentPage(),
                'last_page' => $polls->lastPage(),
                'per_page' => $polls->perPage(),
                'total' => $polls->total(),
            ],
        ]);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $poll = Poll::with(['options.votes', 'statistics', 'creator'])
            ->findOrFail($id);

        $user = $request->user();

        if ($poll->visibility === 'socios_only' && (!$user || !$user->is_socios)) {
            return response()->json([
                'message' => 'Ce sondage est réservé aux membres Socios'
            ], 403);
        }

        $poll->user_has_voted = $user ? $poll->hasVoted($user) : false;
        $poll->user_can_vote = $user ? $poll->canVote($user) : false;

        if ($user && $poll->user_has_voted) {
            $poll->user_vote = $poll->getUserVote($user);
        }

        if ($poll->show_results_before_vote || $poll->user_has_voted || $poll->is_closed) {
            $poll->load(['options' => function ($query) {
                $query->withCount('votes');
            }]);

            $totalVotes = $poll->total_votes;
            $poll->options->each(function ($option) use ($totalVotes) {
                $option->vote_count = $option->votes_count ?? 0;
                $option->vote_percentage = $totalVotes > 0
                    ? round(($option->vote_count / $totalVotes) * 100, 1)
                    : 0;
            });
        }

        return response()->json($poll);
    }

    public function vote(Request $request, int $id): JsonResponse
    {
        $poll = Poll::with('options')->findOrFail($id);
        $user = $request->user();

        if (!$poll->is_active) {
            return response()->json(['message' => 'Ce sondage n\'est plus actif'], 400);
        }

        if (!$user && !$poll->allow_anonymous) {
            return response()->json(['message' => 'Vous devez être connecté pour voter'], 401);
        }

        if ($user && $poll->hasVoted($user)) {
            return response()->json(['message' => 'Vous avez déjà voté sur ce sondage'], 400);
        }

        if ($poll->visibility === 'socios_only' && (!$user || !$user->is_socios)) {
            return response()->json(['message' => 'Ce sondage est réservé aux membres Socios'], 403);
        }

        $rules = [];
        switch ($poll->type) {
            case 'single':
                $rules['option_id'] = 'required|exists:poll_options,id';
                break;
            case 'multiple':
                $rules['option_ids'] = 'required|array|min:1';
                $rules['option_ids.*'] = 'exists:poll_options,id';
                break;
            case 'rating':
                $rules['rating'] = 'required|integer|min:1|max:10';
                break;
            case 'text':
                $rules['text_response'] = 'required|string|max:1000';
                break;
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation échouée', 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $votes = [];

            switch ($poll->type) {
                case 'single':
                    $votes[] = PollVote::create([
                        'poll_id' => $poll->id,
                        'poll_option_id' => $request->option_id,
                        'user_id' => $user?->id,
                        'is_anonymous' => $request->boolean('is_anonymous', false),
                        'ip_address' => $request->ip(),
                    ]);
                    break;

                case 'multiple':
                    foreach ($request->option_ids as $optionId) {
                        $votes[] = PollVote::create([
                            'poll_id' => $poll->id,
                            'poll_option_id' => $optionId,
                            'user_id' => $user?->id,
                            'is_anonymous' => $request->boolean('is_anonymous', false),
                            'ip_address' => $request->ip(),
                        ]);
                    }
                    break;

                case 'rating':
                    $votes[] = PollVote::create([
                        'poll_id' => $poll->id,
                        'user_id' => $user?->id,
                        'rating_value' => $request->rating,
                        'is_anonymous' => $request->boolean('is_anonymous', false),
                        'ip_address' => $request->ip(),
                    ]);
                    break;

                case 'text':
                    $votes[] = PollVote::create([
                        'poll_id' => $poll->id,
                        'user_id' => $user?->id,
                        'text_response' => $request->text_response,
                        'is_anonymous' => $request->boolean('is_anonymous', false),
                        'ip_address' => $request->ip(),
                    ]);
                    break;
            }

            $poll->updateStatistics();

            DB::commit();

            return response()->json([
                'message' => 'Vote enregistré avec succès',
                'poll' => $poll->load('options', 'statistics'),
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Erreur lors du vote', 'error' => $e->getMessage()], 500);
        }
    }

    public function results(int $id): JsonResponse
    {
        $poll = Poll::with(['options', 'statistics'])->findOrFail($id);

        if (!$poll->is_closed && !$poll->show_results_before_vote) {
            return response()->json(['message' => 'Les résultats ne sont pas encore disponibles'], 403);
        }

        $results = [
            'poll' => $poll,
            'total_votes' => $poll->total_votes,
            'total_voters' => $poll->total_voters,
            'options' => $poll->options->map(function ($option) use ($poll) {
                return [
                    'id' => $option->id,
                    'text' => $option->text,
                    'vote_count' => $option->vote_count,
                    'vote_percentage' => $option->vote_percentage,
                ];
            }),
        ];

        if ($poll->type === 'rating') {
            $results['average_rating'] = $poll->statistics->average_rating;
        }

        return response()->json($results);
    }
}
