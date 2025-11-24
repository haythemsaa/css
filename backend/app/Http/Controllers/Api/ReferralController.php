<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ReferralProgram;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ReferralController extends Controller
{
    /**
     * Get user's referral code
     */
    public function myCode(Request $request)
    {
        $user = $request->user();

        // Generate referral code if user doesn't have one
        if (!$user->referral_code) {
            do {
                $code = 'CSS-' . strtoupper(Str::random(6));
            } while (User::where('referral_code', $code)->exists());

            $user->referral_code = $code;
            $user->save();
        }

        return response()->json([
            'referral_code' => $user->referral_code,
            'share_url' => url('/register?ref=' . $user->referral_code),
            'share_message' => "Rejoignez CSS avec mon code de parrainage {$user->referral_code} et gagnez des points !",
        ]);
    }

    /**
     * Invite friends
     */
    public function invite(Request $request)
    {
        $request->validate([
            'emails' => 'required|array|min:1|max:10',
            'emails.*' => 'required|email',
        ]);

        $user = $request->user();

        // Ensure user has a referral code
        if (!$user->referral_code) {
            do {
                $code = 'CSS-' . strtoupper(Str::random(6));
            } while (User::where('referral_code', $code)->exists());

            $user->referral_code = $code;
            $user->save();
        }

        // This would normally send emails via a queue
        // Simplified here
        $invitationsSent = 0;
        foreach ($request->emails as $email) {
            // Send invitation email logic here
            $invitationsSent++;
        }

        return response()->json([
            'message' => "{$invitationsSent} invitation(s) sent successfully",
            'referral_code' => $user->referral_code,
        ]);
    }

    /**
     * Get referral statistics
     */
    public function stats(Request $request)
    {
        $user = $request->user();

        $referrals = ReferralProgram::forReferrer($user->id)->get();

        $stats = [
            'total_referrals' => $referrals->count(),
            'completed_referrals' => $referrals->where('status', 'completed')->count(),
            'pending_referrals' => $referrals->where('status', 'pending')->count(),
            'total_rewards_earned' => $referrals->where('status', 'completed')->sum('referrer_reward'),
        ];

        $recentReferrals = ReferralProgram::with('referred')
            ->forReferrer($user->id)
            ->latest()
            ->limit(10)
            ->get();

        return response()->json([
            'stats' => $stats,
            'recent_referrals' => $recentReferrals,
        ]);
    }

    /**
     * Get referral rewards history
     */
    public function rewards(Request $request)
    {
        $user = $request->user();

        $rewards = ReferralProgram::with('referred')
            ->forReferrer($user->id)
            ->completed()
            ->latest()
            ->paginate(20);

        $totalRewards = ReferralProgram::forReferrer($user->id)
            ->completed()
            ->sum('referrer_reward');

        return response()->json([
            'rewards' => $rewards,
            'total_earned' => $totalRewards,
        ]);
    }
}
