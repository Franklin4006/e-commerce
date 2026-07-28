<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function show(Product $product): View
    {
        $product->load('colors.images', 'category', 'approvedReviews.user', 'specifications', 'sizes');

        $alreadyReviewed = false;
        $canReview = false;

        if (auth()->check()) {
            $userId = auth()->id();

            $alreadyReviewed = Review::where('user_id', $userId)->where('product_id', $product->id)->exists();

            if (! $alreadyReviewed) {
                $canReview = OrderItem::whereHas('order', function ($query) use ($userId) {
                    $query->where('user_id', $userId)->where('status', 'delivered');
                })->where('product_id', $product->id)->exists();
            }
        }

        $relatedProducts = $this->relatedProductsFor($product);
        $activeCoupons = $this->activeCoupons();

        return view('site.product', compact('product', 'alreadyReviewed', 'canReview', 'relatedProducts', 'activeCoupons'));
    }

    public function quickView(Product $product): View
    {
        $product->load('sizes', 'colors.images');

        return view('site.partials._quick-view', compact('product'));
    }

    /**
     * Admin-curated related products, filled out with other products from the
     * same category when there aren't enough to make a full row.
     */
    private function relatedProductsFor(Product $product, int $limit = 4): Collection
    {
        $related = $product->relatedProducts()->with('category')->where('status', true)->get();

        if ($related->count() < $limit) {
            $excludeIds = $related->pluck('id')->push($product->id);

            $fallback = Product::with('category')
                ->where('category_id', $product->category_id)
                ->where('status', true)
                ->whereNotIn('id', $excludeIds)
                ->latest()
                ->take($limit - $related->count())
                ->get();

            $related = $related->concat($fallback);
        }

        return $related->take($limit);
    }

    /**
     * Site-wide coupons currently usable, for the PDP's "Available Offers"
     * box — real, live coupon data (never fabricated bank/card offers).
     */
    private function activeCoupons(int $limit = 3): Collection
    {
        return Coupon::where('is_active', true)
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>=', now()))
            ->where(fn ($q) => $q->whereNull('usage_limit')->orWhereColumn('used_count', '<', 'usage_limit'))
            ->orderByDesc('value')
            ->take($limit)
            ->get();
    }
}
