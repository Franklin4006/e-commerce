<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Size;
use App\Support\Cart;
use App\Support\OrderTotals;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(Request $request): View
    {
        $items = Cart::contents();
        $coupon = Coupon::resolveApplied($request, Cart::total());
        $data = ['items' => $items] + OrderTotals::forCart($coupon);

        if ($request->ajax()) {
            return view('site.partials._cart-items', $data);
        }

        return view('site.cart', $data + ['siteSettings' => Setting::allSettings()]);
    }

    public function store(Request $request, Product $product): JsonResponse
    {
        $validated = $request->validate([
            'size' => ['required', 'string', Rule::in(Size::names())],
            'quantity' => ['nullable', 'integer', 'min:1'],
            'color_id' => ['nullable', 'integer', Rule::exists('product_colors', 'id')->where('product_id', $product->id)],
        ]);

        if ($product->hasColors() && ! $request->filled('color_id')) {
            return response()->json(['message' => 'Please select a color.'], 422);
        }

        $colorId = $request->filled('color_id') ? (int) $validated['color_id'] : null;
        $quantity = max(1, (int) ($validated['quantity'] ?? 1));
        $availableStock = $product->stockForSize($validated['size'], $colorId);

        if ($availableStock < 1) {
            return response()->json(['message' => 'This size is out of stock.'], 422);
        }

        Cart::add($product->id, $validated['size'], min($quantity, $availableStock), $colorId);

        return response()->json([
            'message' => 'Added to cart.',
            'count' => Cart::count(),
        ]);
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        $validated = $request->validate([
            'size' => ['required', 'string', Rule::in(Size::names())],
            'quantity' => ['nullable', 'integer'],
            'color_id' => ['nullable', 'integer', Rule::exists('product_colors', 'id')->where('product_id', $product->id)],
        ]);

        $colorId = $request->filled('color_id') ? (int) $validated['color_id'] : null;
        $quantity = (int) ($validated['quantity'] ?? 1);
        $availableStock = $product->stockForSize($validated['size'], $colorId);

        Cart::update($product->id, $validated['size'], min($quantity, $availableStock), $colorId);

        return response()->json([
            'message' => 'Cart updated.',
            'count' => Cart::count(),
            'total' => Cart::total(),
        ]);
    }

    public function destroy(Request $request, Product $product): JsonResponse
    {
        $validated = $request->validate([
            'size' => ['required', 'string', Rule::in(Size::names())],
            'color_id' => ['nullable', 'integer', Rule::exists('product_colors', 'id')->where('product_id', $product->id)],
        ]);

        $colorId = $request->filled('color_id') ? (int) $validated['color_id'] : null;

        Cart::remove($product->id, $validated['size'], $colorId);

        return response()->json([
            'message' => 'Item removed from cart.',
            'count' => Cart::count(),
            'total' => Cart::total(),
        ]);
    }
}
