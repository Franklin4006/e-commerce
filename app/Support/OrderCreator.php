<?php

namespace App\Support;

use App\Mail\TemplatedMail;
use App\Models\Address;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductSize;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use RuntimeException;

class OrderCreator
{
    /**
     * @param  array<int, array{product_id: int, product_name: string, size: string, color_id: ?int, color_name: ?string, quantity: int, mrp: float, sale_price: float, subtotal: float}>  $itemsData
     * @param  array{totalMrp: float, total: float, savings: float, shippingCharge: ?float, couponCode?: ?string, couponDiscount?: float, grandTotal: float}  $totals
     * @param  array<string, mixed>  $extra
     */
    public static function create(
        User $user,
        array $itemsData,
        Address $shippingAddress,
        Address $billingAddress,
        array $totals,
        string $paymentMethod,
        string $paymentStatus,
        array $extra = []
    ): Order {
        return DB::transaction(function () use ($user, $itemsData, $shippingAddress, $billingAddress, $totals, $paymentMethod, $paymentStatus, $extra) {
            $sizeRowsByKey = [];

            foreach ($itemsData as $item) {
                $key = $item['product_id'].':'.($item['color_id'] ?? 'none').':'.$item['size'];
                $sizeRow = ProductSize::where('product_id', $item['product_id'])
                    ->where('product_color_id', $item['color_id'] ?? null)
                    ->where('size', $item['size'])
                    ->lockForUpdate()
                    ->first();

                if (! $sizeRow || $sizeRow->stock < $item['quantity']) {
                    $variantLabel = $item['color_name'] ?? null;
                    $variantLabel = $variantLabel ? "{$variantLabel}, size {$item['size']}" : "size {$item['size']}";

                    throw new RuntimeException("Sorry, \"{$item['product_name']}\" ({$variantLabel}) doesn't have enough stock available.");
                }

                $sizeRowsByKey[$key] = $sizeRow;
            }

            $order = Order::create(array_merge([
                'user_id' => $user->id,
                'order_number' => Order::generateOrderNumber(),
                'status' => 'pending',
                'payment_method' => $paymentMethod,
                'payment_status' => $paymentStatus,
                'customer_name' => $user->name,
                'customer_email' => $user->email,
                'customer_phone' => $user->phone,
                'shipping_name' => $shippingAddress->name,
                'shipping_phone' => $shippingAddress->phone,
                'shipping_address_line1' => $shippingAddress->address_line1,
                'shipping_address_line2' => $shippingAddress->address_line2,
                'shipping_city' => $shippingAddress->city,
                'shipping_state' => $shippingAddress->state,
                'shipping_postal_code' => $shippingAddress->postal_code,
                'shipping_country' => $shippingAddress->country,
                'billing_name' => $billingAddress->name,
                'billing_phone' => $billingAddress->phone,
                'billing_address_line1' => $billingAddress->address_line1,
                'billing_address_line2' => $billingAddress->address_line2,
                'billing_city' => $billingAddress->city,
                'billing_state' => $billingAddress->state,
                'billing_postal_code' => $billingAddress->postal_code,
                'billing_country' => $billingAddress->country,
                'total_mrp' => $totals['totalMrp'],
                'total_discount' => $totals['savings'],
                'subtotal' => $totals['total'],
                'shipping_charge' => $totals['shippingCharge'] ?? 0,
                'coupon_code' => $totals['couponCode'] ?? null,
                'coupon_discount' => $totals['couponDiscount'] ?? 0,
                'grand_total' => $totals['grandTotal'],
            ], $extra));

            $order->statusHistories()->create([
                'status' => $order->status,
                'note' => 'Order placed.',
            ]);

            foreach ($itemsData as $item) {
                $order->items()->create([
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'size' => $item['size'],
                    'product_color_id' => $item['color_id'] ?? null,
                    'color_name' => $item['color_name'] ?? null,
                    'quantity' => $item['quantity'],
                    'mrp' => $item['mrp'],
                    'sale_price' => $item['sale_price'],
                    'subtotal' => $item['subtotal'],
                ]);

                $key = $item['product_id'].':'.($item['color_id'] ?? 'none').':'.$item['size'];
                $sizeRow = $sizeRowsByKey[$key];
                $sizeRow->decrement('stock', $item['quantity']);

                StockMovement::create([
                    'product_id' => $item['product_id'],
                    'size' => $item['size'],
                    'product_color_id' => $item['color_id'] ?? null,
                    'order_id' => $order->id,
                    'quantity_change' => -$item['quantity'],
                    'stock_after' => $sizeRow->stock,
                    'reason' => 'Order placed',
                ]);
            }

            return $order;
        });
    }

    /**
     * Record an order the moment an online payment is initiated (the Razorpay
     * order has been created, but the shopper hasn't paid yet). Unlike
     * create(), this does not touch stock: nothing is sold until the payment
     * is actually confirmed, so stock stays available to other shoppers in
     * the meantime. The order sits as payment_status "pending" until
     * confirmOnlinePayment() or failOnlinePayment() resolves it.
     *
     * @param  array<int, array{product_id: int, product_name: string, size: string, color_id: ?int, color_name: ?string, quantity: int, mrp: float, sale_price: float, subtotal: float}>  $itemsData
     * @param  array{totalMrp: float, total: float, savings: float, shippingCharge: ?float, couponCode?: ?string, couponDiscount?: float, grandTotal: float}  $totals
     * @param  array<string, mixed>  $extra
     */
    public static function createPendingOnlinePayment(
        User $user,
        array $itemsData,
        Address $shippingAddress,
        Address $billingAddress,
        array $totals,
        string $paymentMethod,
        array $extra = []
    ): Order {
        return DB::transaction(function () use ($user, $itemsData, $shippingAddress, $billingAddress, $totals, $paymentMethod, $extra) {
            $order = Order::create(array_merge([
                'user_id' => $user->id,
                'order_number' => Order::generateOrderNumber(),
                'status' => 'pending',
                'payment_method' => $paymentMethod,
                'payment_status' => 'pending',
                'customer_name' => $user->name,
                'customer_email' => $user->email,
                'customer_phone' => $user->phone,
                'shipping_name' => $shippingAddress->name,
                'shipping_phone' => $shippingAddress->phone,
                'shipping_address_line1' => $shippingAddress->address_line1,
                'shipping_address_line2' => $shippingAddress->address_line2,
                'shipping_city' => $shippingAddress->city,
                'shipping_state' => $shippingAddress->state,
                'shipping_postal_code' => $shippingAddress->postal_code,
                'shipping_country' => $shippingAddress->country,
                'billing_name' => $billingAddress->name,
                'billing_phone' => $billingAddress->phone,
                'billing_address_line1' => $billingAddress->address_line1,
                'billing_address_line2' => $billingAddress->address_line2,
                'billing_city' => $billingAddress->city,
                'billing_state' => $billingAddress->state,
                'billing_postal_code' => $billingAddress->postal_code,
                'billing_country' => $billingAddress->country,
                'total_mrp' => $totals['totalMrp'],
                'total_discount' => $totals['savings'],
                'subtotal' => $totals['total'],
                'shipping_charge' => $totals['shippingCharge'] ?? 0,
                'coupon_code' => $totals['couponCode'] ?? null,
                'coupon_discount' => $totals['couponDiscount'] ?? 0,
                'grand_total' => $totals['grandTotal'],
            ], $extra));

            $order->statusHistories()->create([
                'status' => $order->status,
                'note' => 'Order placed. Awaiting payment confirmation.',
            ]);

            foreach ($itemsData as $item) {
                $order->items()->create([
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'size' => $item['size'],
                    'product_color_id' => $item['color_id'] ?? null,
                    'color_name' => $item['color_name'] ?? null,
                    'quantity' => $item['quantity'],
                    'mrp' => $item['mrp'],
                    'sale_price' => $item['sale_price'],
                    'subtotal' => $item['subtotal'],
                ]);
            }

            return $order;
        });
    }

    /**
     * Confirm a pending online-payment order once Razorpay verification
     * succeeds: reserves stock now (this is the first point the sale is
     * certain) and marks the order paid.
     *
     * @param  array<int, array{product_id: int, product_name: string, size: string, color_id: ?int, color_name: ?string, quantity: int, mrp: float, sale_price: float, subtotal: float}>  $itemsData
     * @param  array<string, mixed>  $extra
     *
     * @throws RuntimeException if stock ran out between initiation and confirmation.
     */
    public static function confirmOnlinePayment(Order $order, array $itemsData, array $extra = []): Order
    {
        return DB::transaction(function () use ($order, $itemsData, $extra) {
            foreach ($itemsData as $item) {
                $sizeRow = ProductSize::where('product_id', $item['product_id'])
                    ->where('product_color_id', $item['color_id'] ?? null)
                    ->where('size', $item['size'])
                    ->lockForUpdate()
                    ->first();

                if (! $sizeRow || $sizeRow->stock < $item['quantity']) {
                    $variantLabel = $item['color_name'] ?? null;
                    $variantLabel = $variantLabel ? "{$variantLabel}, size {$item['size']}" : "size {$item['size']}";

                    throw new RuntimeException("Sorry, \"{$item['product_name']}\" ({$variantLabel}) doesn't have enough stock available.");
                }

                $sizeRow->decrement('stock', $item['quantity']);

                StockMovement::create([
                    'product_id' => $item['product_id'],
                    'size' => $item['size'],
                    'product_color_id' => $item['color_id'] ?? null,
                    'order_id' => $order->id,
                    'quantity_change' => -$item['quantity'],
                    'stock_after' => $sizeRow->stock,
                    'reason' => 'Order placed',
                ]);
            }

            $order->update(array_merge(['payment_status' => 'paid'], $extra));

            return $order;
        });
    }

    /**
     * Mark a pending online-payment order as failed (e.g. Razorpay signature
     * verification failed). No stock was ever reserved for it, so there's
     * nothing to release.
     *
     * @param  array<string, mixed>  $extra
     */
    public static function failOnlinePayment(Order $order, array $extra = []): Order
    {
        $order->update(array_merge(['payment_status' => 'failed'], $extra));

        $order->statusHistories()->create([
            'status' => $order->status,
            'note' => 'Payment verification failed.',
        ]);

        return $order;
    }

    /**
     * @return array<int, array{product_id: int, product_name: string, size: string, color_id: ?int, color_name: ?string, quantity: int, mrp: float, sale_price: float, subtotal: float}>
     */
    public static function snapshotItems(Collection $items): array
    {
        return $items->map(fn ($item) => [
            'product_id' => $item['product']->id,
            'product_name' => $item['product']->name,
            'size' => $item['size'],
            'color_id' => $item['color']?->id,
            'color_name' => $item['color']?->name,
            'quantity' => $item['quantity'],
            'mrp' => (float) $item['product']->mrp,
            'sale_price' => (float) $item['product']->sale_price,
            'subtotal' => (float) $item['subtotal'],
        ])->values()->all();
    }

    /**
     * @param  array<int, array{product_id: int|string, size: string, color_id: int|string|null, quantity: int|string}>  $pairs
     * @return array<int, array{product_id: int, product_name: string, size: string, color_id: ?int, color_name: ?string, quantity: int, mrp: float, sale_price: float, subtotal: float}>
     */
    public static function snapshotItemsFromInput(array $pairs): array
    {
        $productIds = collect($pairs)->pluck('product_id')->unique()->all();
        $products = Product::with('colors')->whereIn('id', $productIds)->get()->keyBy('id');

        return collect($pairs)->map(function ($pair) use ($products) {
            $product = $products->get((int) $pair['product_id']);

            if (! $product) {
                throw new RuntimeException('One of the selected products could not be found.');
            }

            $colorId = ! empty($pair['color_id']) ? (int) $pair['color_id'] : null;
            $color = $colorId ? $product->colors->firstWhere('id', $colorId) : null;

            if ($colorId && ! $color) {
                throw new RuntimeException("The selected color for \"{$product->name}\" could not be found.");
            }

            $quantity = (int) $pair['quantity'];

            return [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'size' => $pair['size'],
                'color_id' => $color?->id,
                'color_name' => $color?->name,
                'quantity' => $quantity,
                'mrp' => (float) $product->mrp,
                'sale_price' => (float) $product->sale_price,
                'subtotal' => (float) $product->sale_price * $quantity,
            ];
        })->values()->all();
    }

    public static function sendConfirmationEmail(User $user, Order $order): void
    {
        Mail::to($user->email)->send(new TemplatedMail('order-confirmation', [
            'name' => $user->name,
            'order_number' => $order->order_number,
            'grand_total' => number_format((float) $order->grand_total, 2),
            'order_url' => route('checkout.confirmation', $order),
        ]));
    }

    public static function sendStatusUpdateEmail(Order $order): void
    {
        $order->loadMissing('user');

        Mail::to($order->user->email)->send(new TemplatedMail('order-status-update', [
            'name' => $order->user->name,
            'order_number' => $order->order_number,
            'status' => ucfirst($order->status),
            'order_url' => route('orders.show', $order),
        ]));
    }
}
