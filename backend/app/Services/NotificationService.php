<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;

class NotificationService
{
    /**
     * Send push notification via Firebase
     */
    public function sendPushNotification(
        User $user,
        string $title,
        string $body,
        ?array $data = null
    ): bool {
        $fcmToken = $user->pushTokens()->where('is_active', true)->first()?->token;

        if (!$fcmToken) {
            return false;
        }

        $serverKey = config('services.fcm.server_key');

        try {
            $response = Http::withHeaders([
                'Authorization' => "key={$serverKey}",
                'Content-Type' => 'application/json',
            ])->post('https://fcm.googleapis.com/fcm/send', [
                'to' => $fcmToken,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                    'sound' => 'default',
                ],
                'data' => $data ?? [],
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            \Log::error('Push notification failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send email notification
     */
    public function sendEmail(User $user, string $subject, string $content): bool
    {
        // TODO: Implement email sending
        return true;
    }

    /**
     * Send SMS notification
     */
    public function sendSMS(User $user, string $message): bool
    {
        if (!$user->phone) {
            return false;
        }

        // TODO: Integrate with SMS gateway
        return true;
    }

    /**
     * Create in-app notification
     */
    public function createNotification(
        User $user,
        string $type,
        string $title,
        string $message,
        ?array $data = null
    ): void {
        $user->notifications()->create([
            'type' => $type,
            'data' => [
                'title' => $title,
                'message' => $message,
                'data' => $data,
            ],
        ]);
    }

    /**
     * Send match notification
     */
    public function notifyMatchStarting(int $matchId): void
    {
        $users = User::where('user_type', '!=', 'free')->get();

        foreach ($users as $user) {
            $this->sendPushNotification(
                $user,
                'Match commence bientôt !',
                'Le match CSS va commencer dans 30 minutes',
                ['match_id' => $matchId]
            );
        }
    }

    /**
     * Send new content notification
     */
    public function notifyNewContent(int $contentId, string $title): void
    {
        $users = User::where('user_type', '!=', 'free')->get();

        foreach ($users as $user) {
            $this->sendPushNotification(
                $user,
                'Nouveau contenu exclusif !',
                $title,
                ['content_id' => $contentId]
            );
        }
    }
}
