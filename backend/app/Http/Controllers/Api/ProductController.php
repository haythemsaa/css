<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{
    /**
     * Display a listing of products
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $request->validate([
            'category' => 'nullable|string|in:jerseys,merchandise,accessories,collectibles',
            'in_stock' => 'nullable|boolean',
            'featured' => 'nullable|boolean',
            'sort' => 'nullable|string|in:popular,newest,price_asc,price_desc,rating',
        ]);

        $query = Product::query();

        if ($request->has('category')) {
            $query->ofCategory($request->input('category'));
        }

        if ($request->input('in_stock')) {
            $query->inStock();
        }

        if ($request->input('featured')) {
            $query->featured();
        }

        $query->available();

        // Sorting
        switch ($request->input('sort', 'popular')) {
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'price_asc':
                $query->orderByRaw('COALESCE(sale_price, price) ASC');
                break;
            case 'price_desc':
                $query->orderByRaw('COALESCE(sale_price, price) DESC');
                break;
            case 'rating':
                $query->orderBy('average_rating', 'desc');
                break;
            default: // popular
                $query->orderBy('sales_count', 'desc');
        }

        $products = $query->paginate(20);

        return ProductResource::collection($products);
    }

    /**
     * Display a single product
     */
    public function show(Product $product): ProductResource
    {
        $product->incrementViews();
        $product->load('reviews.user');

        return new ProductResource($product);
    }

    /**
     * Add a review to a product
     */
    public function addReview(Request $request, Product $product): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Authentification requise',
            ], 401);
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        // Check if user has already reviewed this product
        if (ProductReview::where('product_id', $product->id)->where('user_id', $user->id)->exists()) {
            return response()->json([
                'message' => 'Vous avez déjà évalué ce produit',
            ], 400);
        }

        // Check if user has purchased this product
        $hasPurchased = $user->orders()
            ->where('payment_status', 'paid')
            ->whereHas('items', function ($query) use ($product) {
                $query->where('product_id', $product->id);
            })
            ->exists();

        $review = ProductReview::create([
            'product_id' => $product->id,
            'user_id' => $user->id,
            'rating' => $request->input('rating'),
            'comment' => $request->input('comment'),
            'is_verified_purchase' => $hasPurchased,
        ]);

        return response()->json([
            'message' => 'Évaluation ajoutée avec succès',
            'review' => $review,
        ], 201);
    }

    /**
     * Get product reviews
     */
    public function reviews(Product $product): JsonResponse
    {
        $reviews = $product->reviews()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($reviews);
    }
}
