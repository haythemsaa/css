<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'type' => 'nullable|string',
            'status' => 'nullable|string|in:upcoming,ongoing,completed',
            'featured' => 'nullable|boolean',
        ]);

        $query = Event::query();

        if ($request->has('type')) {
            $query->where('event_type', $request->input('type'));
        }

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->input('featured')) {
            $query->where('is_featured', true);
        }

        $events = $query->orderBy('start_datetime', 'asc')->paginate(20);

        return response()->json($events);
    }

    public function show(string $slug): JsonResponse
    {
        $event = Event::where('slug', $slug)->firstOrFail();

        return response()->json(['event' => $event]);
    }

    public function register(Request $request, int $eventId): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Authentification requise'], 401);
        }

        $event = Event::findOrFail($eventId);

        if (!$event->requires_registration) {
            return response()->json(['message' => 'Cet événement ne nécessite pas d\'inscription'], 400);
        }

        if ($event->max_attendees && $event->registered_count >= $event->max_attendees) {
            return response()->json(['message' => 'Événement complet'], 400);
        }

        if (EventRegistration::where('event_id', $eventId)->where('user_id', $user->id)->exists()) {
            return response()->json(['message' => 'Déjà inscrit à cet événement'], 400);
        }

        $registration = EventRegistration::create([
            'event_id' => $eventId,
            'user_id' => $user->id,
            'status' => 'registered',
            'qr_code' => 'EVT-' . strtoupper(substr(md5(uniqid()), 0, 16)),
            'registered_at' => now(),
        ]);

        $event->increment('registered_count');

        return response()->json([
            'message' => 'Inscription réussie',
            'registration' => $registration,
        ], 201);
    }

    public function myEvents(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Authentification requise'], 401);
        }

        $registrations = EventRegistration::with('event')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($registrations);
    }

    public function cancel(Request $request, int $registrationId): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Authentification requise'], 401);
        }

        $registration = EventRegistration::where('id', $registrationId)
            ->where('user_id', $user->id)
            ->firstOrFail();

        if ($registration->status === 'attended') {
            return response()->json(['message' => 'Impossible d\'annuler un événement déjà assisté'], 400);
        }

        $registration->update(['status' => 'cancelled']);
        $registration->event->decrement('registered_count');

        return response()->json(['message' => 'Inscription annulée']);
    }

    /**
     * Admin: Create a new event
     */
    public function adminStore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|unique:events,slug',
            'description' => 'nullable|string',
            'event_type' => 'required|in:match,meet_greet,training,conference,ceremony,other',
            'start_datetime' => 'required|date',
            'end_datetime' => 'nullable|date|after:start_datetime',
            'location' => 'nullable|string|max:255',
            'venue' => 'nullable|string|max:255',
            'max_attendees' => 'nullable|integer|min:1',
            'requires_registration' => 'boolean',
            'is_featured' => 'boolean',
            'status' => 'required|in:upcoming,ongoing,completed,cancelled',
            'banner_image' => 'nullable|url',
        ]);

        $event = Event::create($validated);

        return response()->json([
            'message' => 'Événement créé avec succès',
            'event' => $event,
        ], 201);
    }

    /**
     * Admin: Update an event
     */
    public function adminUpdate(Request $request, int $id): JsonResponse
    {
        $event = Event::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|unique:events,slug,' . $id,
            'description' => 'nullable|string',
            'event_type' => 'sometimes|in:match,meet_greet,training,conference,ceremony,other',
            'start_datetime' => 'sometimes|date',
            'end_datetime' => 'nullable|date|after:start_datetime',
            'location' => 'nullable|string|max:255',
            'venue' => 'nullable|string|max:255',
            'max_attendees' => 'nullable|integer|min:1',
            'requires_registration' => 'boolean',
            'is_featured' => 'boolean',
            'status' => 'sometimes|in:upcoming,ongoing,completed,cancelled',
            'banner_image' => 'nullable|url',
        ]);

        $event->update($validated);

        return response()->json([
            'message' => 'Événement mis à jour avec succès',
            'event' => $event,
        ]);
    }

    /**
     * Admin: Delete an event
     */
    public function adminDestroy(int $id): JsonResponse
    {
        $event = Event::findOrFail($id);
        $event->delete();

        return response()->json([
            'message' => 'Événement supprimé avec succès',
        ]);
    }
}
