<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentMethodResource;
use App\Models\PaymentMethod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = PaymentMethod::active();

        if ($request->has('context')) {
            $query->forContext($request->context);
        }

        if ($request->has('type')) {
            $query->byType($request->type);
        }

        $methods = $query->orderBy('display_order')->get();

        return response()->json([
            'payment_methods' => PaymentMethodResource::collection($methods),
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $method = PaymentMethod::findOrFail($id);

        return response()->json(new PaymentMethodResource($method));
    }

    public function calculateFees(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
        ]);

        $method = PaymentMethod::findOrFail($id);
        $fees = $method->calculateFees($validated['amount']);

        return response()->json($fees);
    }

    // ========================================
    // ADMIN METHODS
    // ========================================

    /**
     * Create a new payment method (Admin only)
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:payment_methods,code',
            'type' => 'required|in:mobile_wallet,bank_card,bank_transfer,cash,international',
            'description' => 'nullable|string',
            'logo_url' => 'nullable|string',
            'provider' => 'nullable|string',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'supported_currencies' => 'required|array',
            'min_amount' => 'nullable|numeric|min:0',
            'max_amount' => 'nullable|numeric|min:0',
            'transaction_fee' => 'nullable|numeric|min:0',
            'transaction_fee_percentage' => 'nullable|numeric|min:0|max:100',
            'config' => 'nullable|array',
            'processing_time' => 'nullable|string',
            'supports_refund' => 'boolean',
            'instructions' => 'nullable|string',
            'display_order' => 'nullable|integer|min:0',
            'available_for' => 'required|array',
        ]);

        $method = PaymentMethod::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Méthode de paiement créée avec succès',
            'data' => new PaymentMethodResource($method),
        ], 201);
    }

    /**
     * Update a payment method (Admin only)
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $method = PaymentMethod::findOrFail($id);

        $validated = $request->validate([
            'name' => 'string|max:255',
            'code' => 'string|unique:payment_methods,code,' . $id,
            'type' => 'in:mobile_wallet,bank_card,bank_transfer,cash,international',
            'description' => 'nullable|string',
            'logo_url' => 'nullable|string',
            'provider' => 'nullable|string',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'supported_currencies' => 'array',
            'min_amount' => 'nullable|numeric|min:0',
            'max_amount' => 'nullable|numeric|min:0',
            'transaction_fee' => 'nullable|numeric|min:0',
            'transaction_fee_percentage' => 'nullable|numeric|min:0|max:100',
            'config' => 'nullable|array',
            'processing_time' => 'nullable|string',
            'supports_refund' => 'boolean',
            'instructions' => 'nullable|string',
            'display_order' => 'nullable|integer|min:0',
            'available_for' => 'array',
        ]);

        $method->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Méthode de paiement mise à jour avec succès',
            'data' => new PaymentMethodResource($method->fresh()),
        ]);
    }

    /**
     * Delete a payment method (Admin only)
     */
    public function destroy(int $id): JsonResponse
    {
        $method = PaymentMethod::findOrFail($id);

        // Check if method has transactions
        if ($method->transactions()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Impossible de supprimer une méthode de paiement avec des transactions',
            ], 422);
        }

        $method->delete();

        return response()->json([
            'success' => true,
            'message' => 'Méthode de paiement supprimée avec succès',
        ]);
    }

    /**
     * Get payment method statistics (Admin only)
     */
    public function statistics(): JsonResponse
    {
        $stats = [
            'total_methods' => PaymentMethod::count(),
            'active_methods' => PaymentMethod::active()->count(),
            'inactive_methods' => PaymentMethod::where('is_active', false)->count(),
            'by_type' => PaymentMethod::selectRaw('type, count(*) as count')
                ->groupBy('type')
                ->get(),
            'most_used' => PaymentMethod::withCount('transactions')
                ->orderBy('transactions_count', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($method) {
                    return [
                        'name' => $method->name,
                        'transactions' => $method->transactions_count,
                    ];
                }),
        ];

        return response()->json($stats);
    }
}
