<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display user's orders
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Authentification requise',
            ], 401);
        }

        $request->validate([
            'status' => 'nullable|string|in:pending,confirmed,processing,shipped,delivered,cancelled',
        ]);

        $query = Order::query()
            ->with('items.product')
            ->where('user_id', $user->id);

        if ($request->has('status')) {
            $query->ofStatus($request->input('status'));
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(20);

        return OrderResource::collection($orders);
    }

    /**
     * Display a single order
     */
    public function show(Request $request, Order $order): JsonResponse
    {
        $user = $request->user();

        if (!$user || $order->user_id !== $user->id) {
            return response()->json([
                'message' => 'Non autorisé',
            ], 403);
        }

        $order->load('items.product');

        return response()->json([
            'order' => new OrderResource($order),
        ]);
    }

    /**
     * Create a new order
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Authentification requise',
            ], 401);
        }

        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'shipping_address' => 'required|array',
            'shipping_address.full_name' => 'required|string',
            'shipping_address.phone' => 'required|string',
            'shipping_address.address_line' => 'required|string',
            'shipping_address.city' => 'required|string',
            'shipping_address.postal_code' => 'required|string',
            'payment_method' => 'required|string|in:d17,konnect,paymee,sadad,stripe',
            'notes' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            $subtotal = 0;
            $orderItems = [];

            // Validate products and calculate subtotal
            foreach ($request->input('items') as $item) {
                $product = Product::findOrFail($item['product_id']);

                if (!$product->is_available || $product->stock_quantity < $item['quantity']) {
                    DB::rollBack();
                    return response()->json([
                        'message' => "Le produit '{$product->name}' n'est pas disponible en quantité suffisante",
                    ], 400);
                }

                $unitPrice = $product->current_price;
                $totalPrice = $unitPrice * $item['quantity'];
                $subtotal += $totalPrice;

                $orderItems[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                ];
            }

            // Calculate totals
            $shippingCost = 7.00; // Fixed shipping
            $tax = $subtotal * 0.19; // 19% VAT
            $total = $subtotal + $tax + $shippingCost;

            // Create order
            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'user_id' => $user->id,
                'status' => 'pending',
                'subtotal' => $subtotal,
                'tax' => $tax,
                'shipping_cost' => $shippingCost,
                'discount' => 0,
                'total' => $total,
                'payment_method' => $request->input('payment_method'),
                'payment_status' => 'pending',
                'shipping_address' => $request->input('shipping_address'),
                'billing_address' => $request->input('billing_address', $request->input('shipping_address')),
                'notes' => $request->input('notes'),
            ]);

            // Create order items and decrement stock
            foreach ($orderItems as $item) {
                $order->items()->create([
                    'product_id' => $item['product']->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['total_price'],
                    'product_snapshot' => [
                        'name' => $item['product']->name,
                        'description' => $item['product']->description,
                        'images' => $item['product']->images,
                    ],
                ]);

                $item['product']->decrementStock($item['quantity']);
            }

            DB::commit();

            return response()->json([
                'message' => 'Commande créée avec succès',
                'order' => new OrderResource($order->load('items.product')),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Erreur lors de la création de la commande',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Cancel an order
     */
    public function cancel(Request $request, Order $order): JsonResponse
    {
        $user = $request->user();

        if (!$user || $order->user_id !== $user->id) {
            return response()->json([
                'message' => 'Non autorisé',
            ], 403);
        }

        if (!in_array($order->status, ['pending', 'confirmed'])) {
            return response()->json([
                'message' => 'Cette commande ne peut pas être annulée',
            ], 400);
        }

        $order->cancel();

        return response()->json([
            'message' => 'Commande annulée avec succès',
        ]);
    }

    // ========================================
    // ADMIN METHODS
    // ========================================

    /**
     * Admin: Get all orders
     */
    public function adminIndex(Request $request): JsonResponse
    {
        $query = Order::query()->with(['user', 'items.product']);

        // Filter by status
        if ($request->has('status')) {
            $query->ofStatus($request->status);
        }

        // Filter by payment status
        if ($request->has('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Search by order number or user
        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('order_number', 'like', "%{$request->search}%")
                  ->orWhereHas('user', function ($userQ) use ($request) {
                      $userQ->where('email', 'like', "%{$request->search}%")
                            ->orWhere('first_name', 'like', "%{$request->search}%")
                            ->orWhere('last_name', 'like', "%{$request->search}%");
                  });
            });
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $orders = $query->paginate($request->get('per_page', 20));

        return response()->json($orders);
    }

    /**
     * Admin: Update order status
     */
    public function adminUpdate(Request $request, Order $order): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'sometimes|in:pending,confirmed,processing,shipped,delivered,cancelled',
            'payment_status' => 'sometimes|in:pending,processing,completed,failed,refunded',
            'tracking_number' => 'nullable|string',
            'admin_notes' => 'nullable|string',
        ]);

        $order->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Commande mise à jour avec succès',
            'order' => $order->fresh(['user', 'items.product']),
        ]);
    }

    /**
     * Admin: Refund an order
     */
    public function adminDestroy(Order $order): JsonResponse
    {
        if (!in_array($order->status, ['pending', 'confirmed', 'processing'])) {
            return response()->json([
                'success' => false,
                'message' => 'Cette commande ne peut pas être annulée',
            ], 400);
        }

        $order->cancel();
        $order->update(['payment_status' => 'refunded']);

        return response()->json([
            'success' => true,
            'message' => 'Commande annulée et remboursée avec succès',
        ]);
    }
}
