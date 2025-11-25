<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\SupportTicketMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SupportTicketController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Authentification requise'], 401);
        }

        $request->validate([
            'status' => 'nullable|string|in:open,in_progress,waiting_user,resolved,closed',
        ]);

        $query = SupportTicket::where('user_id', $user->id);

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        $tickets = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json($tickets);
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Authentification requise'], 401);
        }

        $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|in:technical,account,payment,content,other',
            'priority' => 'nullable|string|in:low,medium,high,urgent',
        ]);

        $ticket = SupportTicket::create([
            'ticket_number' => 'TKT-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 8)),
            'user_id' => $user->id,
            'subject' => $request->input('subject'),
            'description' => $request->input('description'),
            'category' => $request->input('category'),
            'priority' => $request->input('priority', 'medium'),
            'status' => 'open',
        ]);

        // Create first message
        SupportTicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'message' => $request->input('description'),
            'is_staff_reply' => false,
        ]);

        return response()->json([
            'message' => 'Ticket créé avec succès',
            'ticket' => $ticket,
        ], 201);
    }

    public function show(Request $request, string $ticketNumber): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Authentification requise'], 401);
        }

        $ticket = SupportTicket::with('messages.user')
            ->where('ticket_number', $ticketNumber)
            ->where('user_id', $user->id)
            ->firstOrFail();

        return response()->json(['ticket' => $ticket]);
    }

    public function reply(Request $request, string $ticketNumber): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Authentification requise'], 401);
        }

        $ticket = SupportTicket::where('ticket_number', $ticketNumber)
            ->where('user_id', $user->id)
            ->firstOrFail();

        if ($ticket->status === 'closed') {
            return response()->json(['message' => 'Ce ticket est fermé'], 400);
        }

        $request->validate([
            'message' => 'required|string',
        ]);

        $message = SupportTicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'message' => $request->input('message'),
            'is_staff_reply' => false,
        ]);

        // Update ticket status
        if ($ticket->status === 'waiting_user') {
            $ticket->update(['status' => 'in_progress']);
        }

        return response()->json([
            'message' => 'Réponse ajoutée',
            'ticket_message' => $message,
        ], 201);
    }

    public function rate(Request $request, string $ticketNumber): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Authentification requise'], 401);
        }

        $ticket = SupportTicket::where('ticket_number', $ticketNumber)
            ->where('user_id', $user->id)
            ->firstOrFail();

        if ($ticket->status !== 'resolved' && $ticket->status !== 'closed') {
            return response()->json(['message' => 'Le ticket doit être résolu pour être évalué'], 400);
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $ticket->update([
            'satisfaction_rating' => $request->input('rating'),
            'satisfaction_comment' => $request->input('comment'),
        ]);

        return response()->json(['message' => 'Évaluation enregistrée']);
    }
}
