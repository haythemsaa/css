<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    /**
     * Get user's wishlist
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Authentification requise',
            ], 401);
        }

        $wishlistItems = Wishlist::with('product')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'wishlist' => $wishlistItems->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product' => [
                        'id' => $item->product->id,
                        'name' => $item->product->name,
                        'slug' => $item->product->slug,
                        'price' => $item->product->price,
                        'sale_price' => $item->product->sale_price,
                        'images' => $item->product->images,
                        'is_available' => $item->product->is_available,
                        'stock_quantity' => $item->product->stock_quantity,
                        'category' => $item->product->category,
                    ],
                    'added_at' => $item->created_at->toIso8601String(),
                ];
            }),
        ]);
    }

    /**
     * Add product to wishlist
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
            'product_id' => 'required|exists:products,id',
        ]);

        $productId = $request->input('product_id');

        // Check if already in wishlist
        $exists = Wishlist::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Produit déjà dans la wishlist',
            ], 400);
        }

        Wishlist::create([
            'user_id' => $user->id,
            'product_id' => $productId,
        ]);

        return response()->json([
            'message' => 'Produit ajouté à la wishlist',
        ], 201);
    }

    /**
     * Remove product from wishlist
     */
    public function destroy(Request $request, int $productId): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Authentification requise',
            ], 401);
        }

        $deleted = Wishlist::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->delete();

        if (!$deleted) {
            return response()->json([
                'message' => 'Produit non trouvé dans la wishlist',
            ], 404);
        }

        return response()->json([
            'message' => 'Produit retiré de la wishlist',
        ]);
    }

    /**
     * Toggle product in wishlist
     */
    public function toggle(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Authentification requise',
            ], 401);
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $productId = $request->input('product_id');

        $wishlistItem = Wishlist::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->first();

        if ($wishlistItem) {
            $wishlistItem->delete();
            return response()->json([
                'message' => 'Produit retiré de la wishlist',
                'in_wishlist' => false,
            ]);
        } else {
            Wishlist::create([
                'user_id' => $user->id,
                'product_id' => $productId,
            ]);
            return response()->json([
                'message' => 'Produit ajouté à la wishlist',
                'in_wishlist' => true,
            ], 201);
        }
    }

    /**
     * Check if product is in wishlist
     */
    public function check(Request $request, int $productId): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'in_wishlist' => false,
            ]);
        }

        $inWishlist = Wishlist::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->exists();

        return response()->json([
            'in_wishlist' => $inWishlist,
        ]);
    }
}
