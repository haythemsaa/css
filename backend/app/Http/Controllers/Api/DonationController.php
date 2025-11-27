<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DonationController extends Controller
{
    /**
     * Store a new donation
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'campaign_id' => 'nullable|exists:campaigns,id',
            'amount' => 'required|numeric|min:5',
            'payment_method' => 'required|in:card,d17,konnect,paymee,sadad,bank_transfer',
            'is_anonymous' => 'boolean',
            'message' => 'nullable|string|max:500',
            'is_recurring' => 'boolean',
            'recurring_interval' => 'nullable|in:monthly,quarterly,yearly',
        ]);

        $user = $request->user();

        // Create donation
        $donation = Donation::create([
            'user_id' => $user?->id,
            'campaign_id' => $validated['campaign_id'] ?? null,
            'amount' => $validated['amount'],
            'currency' => 'TND',
            'payment_method' => $validated['payment_method'],
            'status' => 'pending',
            'is_anonymous' => $validated['is_anonymous'] ?? false,
            'donor_name' => $validated['is_anonymous'] ? null : $user?->full_name,
            'donor_email' => $validated['is_anonymous'] ? null : $user?->email,
            'message' => $validated['message'] ?? null,
            'is_recurring' => $validated['is_recurring'] ?? false,
            'recurring_interval' => $validated['recurring_interval'] ?? null,
        ]);

        // TODO: Process payment with selected gateway
        // $paymentResult = $this->processPayment($donation);

        // For now, mark as completed
        $donation->update([
            'status' => 'completed',
            'transaction_id' => 'TXN_' . strtoupper(uniqid()),
        ]);

        // Update campaign if specified
        if ($donation->campaign_id) {
            $campaign = Campaign::find($donation->campaign_id);
            $campaign->increment('current_amount', $donation->amount);
            $campaign->increment('donors_count');
        }

        // Award loyalty points
        if ($user) {
            $points = (int) ($donation->amount / 5); // 1 point per 5 TND
            $user->addLoyaltyPoints($points);
        }

        return response()->json([
            'message' => 'Donation successful',
            'donation' => $donation,
            'points_earned' => $user ? (int) ($donation->amount / 5) : 0,
        ], 201);
    }

    /**
     * Get user's donation history
     */
    public function history(Request $request): JsonResponse
    {
        $donations = Donation::query()
            ->with('campaign')
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($donations);
    }

    /**
     * Get donation statistics
     */
    public function stats(Request $request): JsonResponse
    {
        $user = $request->user();

        $totalDonated = Donation::query()
            ->where('user_id', $user->id)
            ->where('status', 'completed')
            ->sum('amount');

        $donationCount = Donation::query()
            ->where('user_id', $user->id)
            ->where('status', 'completed')
            ->count();

        $recurringDonations = Donation::query()
            ->where('user_id', $user->id)
            ->where('is_recurring', true)
            ->where('status', 'completed')
            ->count();

        return response()->json([
            'total_donated' => round($totalDonated, 2),
            'donation_count' => $donationCount,
            'recurring_donations' => $recurringDonations,
            'average_donation' => $donationCount > 0 ? round($totalDonated / $donationCount, 2) : 0,
        ]);
    }

    /**
     * Get donation certificate
     */
    public function certificate(int $id): JsonResponse
    {
        $donation = Donation::query()
            ->where('user_id', request()->user()->id)
            ->findOrFail($id);

        if ($donation->status !== 'completed') {
            return response()->json([
                'message' => 'Certificate only available for completed donations',
            ], 400);
        }

        // Generate certificate if not exists
        if (!$donation->certificate_number) {
            $donation->update([
                'certificate_number' => 'CERT_' . strtoupper(uniqid()),
            ]);
            // TODO: Generate PDF certificate
        }

        return response()->json([
            'certificate_number' => $donation->certificate_number,
            'certificate_url' => $donation->certificate_pdf,
            'donation' => $donation,
        ]);
    }
}
