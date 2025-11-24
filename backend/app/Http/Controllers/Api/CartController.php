<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Get user's cart
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Authentification requise',
            ], 401);
        }

        $cart = Cart::with('items.product')->firstOrCreate([
            'user_id' => $user->id,
        ]);

        return response()->json([
            'cart' => [
                'id' => $cart->id,
                'items' => $cart->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'product' => [
                            'id' => $item->product->id,
                            'name' => $item->product->name,
                            'slug' => $item->product->slug,
                            'price' => $item->product->current_price,
                            'images' => $item->product->images,
                            'is_available' => $item->product->is_available,
                            'stock_quantity' => $item->product->stock_quantity,
                        ],
                        'quantity' => $item->quantity,
                        'subtotal' => $item->subtotal,
                    ];
                }),
                'items_count' => $cart->getItemsCount(),
                'total' => $cart->getTotal(),
            ],
        ]);
    }

    /**
     * Add item to cart
     */
    public function addItem(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Authentification requise',
            ], 401);
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->input('product_id'));

        if (!$product->is_available) {
            return response()->json([
                'message' => 'Ce produit n\'est pas disponible',
            ], 400);
        }

        if ($product->stock_quantity < $request->input('quantity')) {
            return response()->json([
                'message' => 'Stock insuffisant',
            ], 400);
        }

        $cart = Cart::firstOrCreate(['user_id' => $user->id]);
        $cart->addItem($request->input('product_id'), $request->input('quantity'));

        return response()->json([
            'message' => 'Produit ajouté au panier',
            'cart' => [
                'items_count' => $cart->getItemsCount(),
                'total' => $cart->getTotal(),
            ],
        ]);
    }

    /**
     * Update item quantity
     */
    public function updateItem(Request $request, int $productId): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Authentification requise',
            ], 401);
        }

        $request->validate([
            'quantity' => 'required|integer|min:0',
        ]);

        $cart = Cart::where('user_id', $user->id)->first();

        if (!$cart) {
            return response()->json([
                'message' => 'Panier non trouvé',
            ], 404);
        }

        $product = Product::findOrFail($productId);

        if ($request->input('quantity') > 0 && $product->stock_quantity < $request->input('quantity')) {
            return response()->json([
                'message' => 'Stock insuffisant',
            ], 400);
        }

        $cart->updateItemQuantity($productId, $request->input('quantity'));

        return response()->json([
            'message' => 'Panier mis à jour',
            'cart' => [
                'items_count' => $cart->getItemsCount(),
                'total' => $cart->getTotal(),
            ],
        ]);
    }

    /**
     * Remove item from cart
     */
    public function removeItem(Request $request, int $productId): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Authentification requise',
            ], 401);
        }

        $cart = Cart::where('user_id', $user->id)->first();

        if (!$cart) {
            return response()->json([
                'message' => 'Panier non trouvé',
            ], 404);
        }

        $cart->removeItem($productId);

        return response()->json([
            'message' => 'Produit retiré du panier',
        ]);
    }

    /**
     * Clear cart
     */
    public function clear(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Authentification requise',
            ], 401);
        }

        $cart = Cart::where('user_id', $user->id)->first();

        if ($cart) {
            $cart->clear();
        }

        return response()->json([
            'message' => 'Panier vidé',
        ]);
    }
}
