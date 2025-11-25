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
}
