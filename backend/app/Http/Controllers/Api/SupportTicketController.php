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

    // ========================================
    // ADMIN SUPPORT METHODS
    // ========================================

    /**
     * Admin: Get all support tickets
     */
    public function adminIndex(Request $request): JsonResponse
    {
        $query = SupportTicket::query()->with('user');

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by category
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        // Filter by priority
        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }

        // Search by ticket number or user
        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('ticket_number', 'like', "%{$request->search}%")
                  ->orWhere('subject', 'like', "%{$request->search}%")
                  ->orWhereHas('user', function ($userQ) use ($request) {
                      $userQ->where('email', 'like', "%{$request->search}%")
                            ->orWhere('first_name', 'like', "%{$request->search}%");
                  });
            });
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $tickets = $query->paginate($request->get('per_page', 20));

        return response()->json($tickets);
    }

    /**
     * Admin: Reply to a ticket
     */
    public function adminReply(Request $request, string $ticketNumber): JsonResponse
    {
        $ticket = SupportTicket::where('ticket_number', $ticketNumber)->firstOrFail();

        $request->validate([
            'message' => 'required|string',
        ]);

        $message = SupportTicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => $request->user()->id,
            'message' => $request->input('message'),
            'is_staff_reply' => true,
        ]);

        // Update ticket status
        $ticket->update(['status' => 'waiting_user']);

        return response()->json([
            'success' => true,
            'message' => 'Réponse envoyée',
            'ticket_message' => $message,
        ], 201);
    }

    /**
     * Admin: Close a ticket
     */
    public function adminUpdate(Request $request, string $ticketNumber): JsonResponse
    {
        $ticket = SupportTicket::where('ticket_number', $ticketNumber)->firstOrFail();

        $validated = $request->validate([
            'status' => 'required|in:open,in_progress,waiting_user,resolved,closed',
            'priority' => 'nullable|in:low,medium,high,urgent',
        ]);

        $ticket->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Ticket mis à jour',
            'ticket' => $ticket->fresh('user'),
        ]);
    }

    /**
     * Admin: Delete a ticket
     */
    public function adminDestroy(string $ticketNumber): JsonResponse
    {
        $ticket = SupportTicket::where('ticket_number', $ticketNumber)->firstOrFail();

        // Delete messages
        SupportTicketMessage::where('ticket_id', $ticket->id)->delete();

        // Delete ticket
        $ticket->delete();

        return response()->json([
            'success' => true,
            'message' => 'Ticket supprimé avec succès',
        ]);
    }
}
