<?php

namespace App\Support;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductColor;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class Cart
{
    // Bumped from 'cart': the guest cart shape changed from
    // productId => size => qty to productId => colorId => size => qty when
    // color support was added, so old sessions must not be read as the new
    // shape (they'd crash array_sum() on a plain int instead of an array).
    protected const SESSION_KEY = 'cart_v2';

    public static function add(int $productId, string $size, int $quantity = 1, ?int $colorId = null): void
    {
        $quantity = max(1, $quantity);

        if (Auth::check()) {
            $item = CartItem::firstOrNew(['user_id' => Auth::id(), 'product_id' => $productId, 'size' => $size, 'product_color_id' => $colorId]);
            $item->quantity = ($item->exists ? $item->quantity : 0) + $quantity;
            $item->save();

            return;
        }

        $colorKey = $colorId ?? 0;
        $items = static::items();
        $items[$productId][$colorKey][$size] = ($items[$productId][$colorKey][$size] ?? 0) + $quantity;
        session([self::SESSION_KEY => $items]);
    }

    public static function update(int $productId, string $size, int $quantity, ?int $colorId = null): void
    {
        if (Auth::check()) {
            if ($quantity <= 0) {
                CartItem::where('user_id', Auth::id())->where('product_id', $productId)->where('size', $size)->where('product_color_id', $colorId)->delete();
            } else {
                CartItem::updateOrCreate(
                    ['user_id' => Auth::id(), 'product_id' => $productId, 'size' => $size, 'product_color_id' => $colorId],
                    ['quantity' => $quantity]
                );
            }

            return;
        }

        $colorKey = $colorId ?? 0;
        $items = static::items();

        if ($quantity <= 0) {
            unset($items[$productId][$colorKey][$size]);
            if (empty($items[$productId][$colorKey])) {
                unset($items[$productId][$colorKey]);
            }
            if (empty($items[$productId])) {
                unset($items[$productId]);
            }
        } else {
            $items[$productId][$colorKey][$size] = $quantity;
        }

        session([self::SESSION_KEY => $items]);
    }

    public static function remove(int $productId, string $size, ?int $colorId = null): void
    {
        if (Auth::check()) {
            CartItem::where('user_id', Auth::id())->where('product_id', $productId)->where('size', $size)->where('product_color_id', $colorId)->delete();

            return;
        }

        $colorKey = $colorId ?? 0;
        $items = static::items();
        unset($items[$productId][$colorKey][$size]);
        if (empty($items[$productId][$colorKey])) {
            unset($items[$productId][$colorKey]);
        }
        if (empty($items[$productId])) {
            unset($items[$productId]);
        }
        session([self::SESSION_KEY => $items]);
    }

    public static function count(): int
    {
        if (Auth::check()) {
            return (int) CartItem::where('user_id', Auth::id())->sum('quantity');
        }

        $total = 0;
        foreach (static::items() as $colors) {
            foreach ($colors as $sizes) {
                $total += array_sum($sizes);
            }
        }

        return $total;
    }

    public static function contents(): Collection
    {
        if (Auth::check()) {
            return CartItem::with('product.category', 'product.sizes', 'color')
                ->where('user_id', Auth::id())
                ->get()
                ->map(fn (CartItem $item) => static::toLineItem($item->product, $item->size, $item->quantity, $item->color))
                ->filter()
                ->values();
        }

        $items = static::items();

        if (empty($items)) {
            return collect();
        }

        $products = Product::with('category', 'sizes', 'colors')->whereIn('id', array_keys($items))->get()->keyBy('id');

        $lineItems = collect();

        foreach ($items as $productId => $colors) {
            $product = $products->get($productId);

            foreach ($colors as $colorKey => $sizes) {
                $colorId = (int) $colorKey ?: null;
                $color = $colorId && $product ? $product->colors->firstWhere('id', $colorId) : null;

                foreach ($sizes as $size => $quantity) {
                    $lineItems->push(static::toLineItem($product, $size, $quantity, $color));
                }
            }
        }

        return $lineItems->filter()->values();
    }

    public static function total(): float
    {
        return (float) static::contents()->sum('subtotal');
    }

    public static function totalMrp(): float
    {
        return (float) static::contents()->sum('mrp_subtotal');
    }

    public static function savings(): float
    {
        return (float) static::contents()->sum(fn ($item) => $item['mrp_subtotal'] - $item['subtotal']);
    }

    public static function clear(): void
    {
        if (Auth::check()) {
            CartItem::where('user_id', Auth::id())->delete();

            return;
        }

        session()->forget(self::SESSION_KEY);
    }

    /**
     * Merge the guest session cart into the authenticated user's database cart.
     */
    public static function mergeSessionIntoDatabase(int $userId): void
    {
        $items = static::items();

        foreach ($items as $productId => $colors) {
            foreach ($colors as $colorKey => $sizes) {
                $colorId = (int) $colorKey ?: null;

                foreach ($sizes as $size => $quantity) {
                    $item = CartItem::firstOrNew(['user_id' => $userId, 'product_id' => $productId, 'size' => $size, 'product_color_id' => $colorId]);
                    $item->quantity = ($item->exists ? $item->quantity : 0) + $quantity;
                    $item->save();
                }
            }
        }

        session()->forget(self::SESSION_KEY);
    }

    protected static function toLineItem(?Product $product, string $size, int $quantity, ?ProductColor $color = null): ?array
    {
        if (! $product) {
            return null;
        }

        return [
            'product' => $product,
            'size' => $size,
            'color' => $color,
            'quantity' => $quantity,
            'subtotal' => $product->sale_price * $quantity,
            'mrp_subtotal' => $product->mrp * $quantity,
        ];
    }

    protected static function items(): array
    {
        return session(self::SESSION_KEY, []);
    }
}
