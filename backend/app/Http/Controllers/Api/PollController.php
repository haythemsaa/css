<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Poll;
use App\Models\PollVote;
use Illuminate\Http\Request;

class PollController extends Controller
{
    /**
     * Get all polls
     */
    public function index(Request $request)
    {
        $query = Poll::query();

        // Filter by status
        $status = $request->get('status', 'active');
        if ($status === 'active') {
            $query->active();
        } elseif ($status === 'upcoming') {
            $query->upcoming();
        } elseif ($status === 'ended') {
            $query->ended();
        }

        $polls = $query->latest()->paginate(20);

        // Add user vote status if authenticated
        $user = $request->user();
        if ($user) {
            $polls->getCollection()->transform(function ($poll) use ($user) {
                $poll->has_voted = $poll->hasUserVoted($user);
                $poll->can_vote = $poll->canUserVote($user);
                return $poll;
            });
        }

        return response()->json($polls);
    }

    /**
     * Get a single poll
     */
    public function show(Request $request, $id)
    {
        $poll = Poll::findOrFail($id);
        $user = $request->user();

        $response = [
            'poll' => $poll,
            'is_active' => $poll->isActive(),
            'has_ended' => $poll->hasEnded(),
            'total_votes' => $poll->total_votes,
        ];

        if ($user) {
            $response['has_voted'] = $poll->hasUserVoted($user);
            $response['can_vote'] = $poll->canUserVote($user);

            // Show user's vote if they voted
            if ($poll->hasUserVoted($user)) {
                $userVote = PollVote::forPoll($poll->id)
                    ->forUser($user->id)
                    ->first();
                $response['user_vote'] = $userVote;
            }
        }

        // Show results if allowed
        if ($poll->show_results_before_vote || ($user && $poll->hasUserVoted($user)) || $poll->hasEnded()) {
            $response['results'] = $poll->getResults();
        }

        return response()->json($response);
    }

    /**
     * Vote on a poll
     */
    public function vote(Request $request, $id)
    {
        $request->validate([
            'option_index' => 'required|integer|min:0',
        ]);

        $user = $request->user();
        $poll = Poll::findOrFail($id);

        if (!$poll->canUserVote($user)) {
            return response()->json(['message' => 'You cannot vote on this poll'], 403);
        }

        // Validate option index
        if ($request->option_index >= count($poll->options)) {
            return response()->json(['message' => 'Invalid option'], 400);
        }

        // Create vote
        $vote = PollVote::create([
            'poll_id' => $poll->id,
            'user_id' => $user->id,
            'option_index' => $request->option_index,
        ]);

        return response()->json([
            'message' => 'Vote recorded successfully',
            'vote' => $vote,
            'results' => $poll->getResults(),
        ], 201);
    }

    /**
     * Get poll results
     */
    public function results(Request $request, $id)
    {
        $poll = Poll::findOrFail($id);
        $user = $request->user();

        // Check if user can see results
        if (!$poll->show_results_before_vote && !$poll->hasEnded()) {
            if (!$user || !$poll->hasUserVoted($user)) {
                return response()->json(['message' => 'Results not available yet'], 403);
            }
        }

        $results = $poll->getResults();

        return response()->json([
            'poll' => $poll,
            'results' => $results,
            'total_votes' => $poll->total_votes,
        ]);
    }
}
