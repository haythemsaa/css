<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\PushNotificationToken;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Get user's notifications
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Notification::forUser($user->id);

        // Filter by type
        if ($request->has('type')) {
            $query->byType($request->type);
        }

        // Filter by read status
        if ($request->has('unread_only') && $request->unread_only) {
            $query->unread();
        }

        $notifications = $query->latest()->paginate(20);

        $stats = [
            'total' => Notification::forUser($user->id)->count(),
            'unread' => Notification::forUser($user->id)->unread()->count(),
        ];

        return response()->json([
            'notifications' => $notifications,
            'stats' => $stats,
        ]);
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead(Request $request, $id)
    {
        $user = $request->user();
        $notification = Notification::forUser($user->id)->findOrFail($id);

        $notification->markAsRead();

        return response()->json([
            'message' => 'Notification marked as read',
            'notification' => $notification->fresh(),
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(Request $request)
    {
        $user = $request->user();

        Notification::forUser($user->id)
            ->unread()
            ->update(['read_at' => now()]);

        return response()->json([
            'message' => 'All notifications marked as read',
        ]);
    }

    /**
     * Get notification preferences
     */
    public function preferences(Request $request)
    {
        $user = $request->user();

        // This would normally fetch from a preferences table
        // Simplified here with default preferences
        $preferences = [
            'email' => [
                'match_start' => true,
                'new_content' => true,
                'new_offer' => true,
                'forum_reply' => true,
                'trade_offer' => true,
            ],
            'push' => [
                'match_start' => true,
                'new_content' => false,
                'new_offer' => true,
                'forum_reply' => true,
                'trade_offer' => true,
            ],
            'sms' => [
                'match_start' => false,
                'important_only' => true,
            ],
        ];

        return response()->json($preferences);
    }

    /**
     * Update notification preferences
     */
    public function updatePreferences(Request $request)
    {
        $request->validate([
            'preferences' => 'required|array',
        ]);

        $user = $request->user();

        // This would normally save to a preferences table
        // Simplified here

        return response()->json([
            'message' => 'Preferences updated successfully',
            'preferences' => $request->preferences,
        ]);
    }

    /**
     * Register device token for push notifications
     */
    public function registerDeviceToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'platform' => 'required|string|in:ios,android,web',
            'device_name' => 'nullable|string',
        ]);

        $user = $request->user();

        // Check if token already exists
        $existingToken = PushNotificationToken::where('token', $request->token)->first();

        if ($existingToken) {
            $existingToken->update([
                'user_id' => $user->id,
                'platform' => $request->platform,
                'device_name' => $request->device_name,
                'is_active' => true,
                'last_used_at' => now(),
            ]);

            return response()->json([
                'message' => 'Device token updated',
                'token' => $existingToken,
            ]);
        }

        // Create new token
        $token = PushNotificationToken::create([
            'user_id' => $user->id,
            'token' => $request->token,
            'platform' => $request->platform,
            'device_name' => $request->device_name,
            'is_active' => true,
            'last_used_at' => now(),
        ]);

        return response()->json([
            'message' => 'Device token registered successfully',
            'token' => $token,
        ], 201);
    }
}
