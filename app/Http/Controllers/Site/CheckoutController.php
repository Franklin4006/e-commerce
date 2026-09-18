<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductSize;
use App\Models\Setting;
use App\Models\User;
use App\Support\Cart;
use App\Support\OrderCreator;
use App\Support\OrderTotals;
use App\Support\Razorpay;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Razorpay\Api\Errors\SignatureVerificationError;
use RuntimeException;

class CheckoutController extends Controller
{
    /**
     * Stash the single product/size/color/quantity chosen via "Buy Now" in the
     * session and send the shopper to checkout. Checkout then sources its items
     * from this instead of the cart, so the rest of the cart is left untouched.
     */
    public function buyNow(Request $request, Product $product): JsonResponse
    {
        $validated = $request->validate([
            'size' => ['required', 'string'],
            'color_id' => ['nullable', 'integer'],
            'quantity' => ['nullable', 'integer', 'min:1'],
        ]);

        $colorId = $validated['color_id'] ?? null;
        $color = $colorId ? $product->colors()->find($colorId) : null;

        if ($colorId && ! $color) {
            return response()->json(['message' => 'The selected color is unavailable.'], 422);
        }

        $quantity = max(1, (int) ($validated['quantity'] ?? 1));

        $sizeRow = ProductSize::where('product_id', $product->id)
            ->where('product_color_id', $colorId)
            ->where('size', $validated['size'])
            ->first();

        if (! $sizeRow || $sizeRow->stock < $quantity) {
            return response()->json(['message' => 'Sorry, this item is out of stock.'], 422);
        }

        $request->session()->put('buy_now', [
            'product_id' => $product->id,
            'size' => $validated['size'],
            'color_id' => $colorId,
            'quantity' => $quantity,
        ]);

        return response()->json(['redirect' => route('checkout.create')]);
    }

    public function create(Request $request): View|RedirectResponse
    {
        $items = $this->checkoutItems($request);

        if ($items->isEmpty()) {
            $request->session()->forget('buy_now');

            return redirect()->route('cart.index')->withErrors(['cart' => 'Your cart is empty.']);
        }

        $addresses = $request->user()->addresses()->orderByDesc('is_default')->latest()->get();

        if ($addresses->isEmpty()) {
            return redirect()->route('addresses.create', ['redirect' => 'checkout'])->with('status', 'Please add an address before checking out.');
        }

        $coupon = Coupon::resolveApplied($request, (float) $items->sum('subtotal'));

        $previewAddress = $request->filled('shipping_address_id')
            ? $addresses->firstWhere('id', (int) $request->query('shipping_address_id'))
            : null;
        $previewAddress ??= $addresses->first();

        $data = [
            'items' => $items,
            'addresses' => $addresses,
            'razorpayEnabled' => Razorpay::isConfigured(),
            'codEnabled' => Setting::get('cod_enabled', '1') !== '0',
            'siteSettings' => Setting::allSettings(),
        ] + OrderTotals::forCart($previewAddress, $coupon, $items);

        return view('site.checkout.create', $data);
    }

    public function store(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'shipping_address_id' => ['required', 'integer'],
            'billing_address_id' => ['nullable', 'integer'],
            'payment_method' => ['required', 'in:cod,razorpay'],
        ]);

        if ($validated['payment_method'] === 'cod' && Setting::get('cod_enabled', '1') === '0') {
            return back()->withErrors(['payment_method' => 'Cash on Delivery is currently unavailable. Please choose another payment method.']);
        }

        if ($validated['payment_method'] === 'razorpay' && ! Razorpay::isConfigured()) {
            return back()->withErrors(['payment_method' => 'Online payment is currently unavailable. Please select Cash on Delivery.']);
        }

        $shippingAddress = $user->addresses()->find($validated['shipping_address_id']);
        abort_if(! $shippingAddress, 404);

        $billingSameAsShipping = $request->boolean('billing_same_as_shipping', true);

        if ($billingSameAsShipping) {
            $billingAddress = $shippingAddress;
        } else {
            $billingAddress = $validated['billing_address_id'] ?? null
                ? $user->addresses()->find($validated['billing_address_id'])
                : null;

            if (! $billingAddress) {
                return back()->withErrors(['billing_address_id' => 'Please select a billing address.'])->withInput();
            }
        }

        $items = $this->checkoutItems($request);

        if ($items->isEmpty()) {
            $request->session()->forget('buy_now');

            return redirect()->route('cart.index')->withErrors(['cart' => 'Your cart is empty.']);
        }

        // Re-validate everything right before the order is actually placed: stock
        // and the coupon can both have changed since the checkout page was loaded
        // (another shopper bought the last unit, the coupon expired/hit its limit,
        // etc.), so catch that here with a clear message instead of letting it
        // surface as a confusing failure deeper in order/payment creation.
        $appliedCouponCode = $request->session()->get('applied_coupon');
        $coupon = Coupon::resolveApplied($request, (float) $items->sum('subtotal'));

        if ($appliedCouponCode && ! $coupon) {
            return back()->withErrors(['coupon' => 'Your coupon is no longer valid and has been removed. Please review your order and try again.']);
        }

        $itemsData = OrderCreator::snapshotItems($items);

        try {
            OrderCreator::assertStockAvailable($itemsData);
        } catch (RuntimeException $e) {
            return back()->withErrors(['cart' => $e->getMessage()]);
        }

        $totals = OrderTotals::forCart($shippingAddress, $coupon, $items);

        if ($validated['payment_method'] === 'cod') {
            try {
                $order = OrderCreator::create($user, $itemsData, $shippingAddress, $billingAddress, $totals, 'cod', 'pending');
            } catch (RuntimeException $e) {
                return back()->withErrors(['cart' => $e->getMessage()]);
            }

            if ($coupon) {
                static::redeemCoupon($coupon, $user, $order);
            }

            $this->clearCheckoutItems($request);

            OrderCreator::sendConfirmationEmail($user, $order);

            return redirect()->route('checkout.confirmation', $order);
        }

        $amountInPaise = (int) round($totals['grandTotal'] * 100);

        $razorpayOrder = Razorpay::client()->order->create([
            'amount' => $amountInPaise,
            'currency' => 'INR',
            'receipt' => 'rcpt_'.Str::random(12),
        ]);

        // Record the order the moment payment is initiated (not just once it
        // succeeds), so an abandoned or failed payment still leaves a paper
        // trail visible on the admin Payment Issues page instead of vanishing.
        // Stock is reserved right away too, so it can't be oversold while the
        // shopper is on the Razorpay payment screen.
        try {
            $order = OrderCreator::createPendingOnlinePayment(
                $user,
                $itemsData,
                $shippingAddress,
                $billingAddress,
                $totals,
                'razorpay',
                ['razorpay_order_id' => $razorpayOrder->id]
            );
        } catch (RuntimeException $e) {
            return back()->withErrors(['cart' => $e->getMessage()]);
        }

        $request->session()->put('razorpay_checkout', [
            'order_id' => $order->id,
            'razorpay_order_id' => $razorpayOrder->id,
            'items' => $itemsData,
            'coupon_id' => $coupon?->id,
            'buy_now' => $request->session()->has('buy_now'),
        ]);

        return view('site.checkout.pay', [
            'razorpayOrderId' => $razorpayOrder->id,
            'razorpayKey' => Razorpay::keyId(),
            'amount' => $amountInPaise,
            'user' => $user,
            'shippingAddress' => $shippingAddress,
            'siteName' => Setting::get('site_name') ?: config('app.name'),
        ]);
    }

    public function verifyRazorpayPayment(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'razorpay_payment_id' => ['required', 'string'],
            'razorpay_order_id' => ['required', 'string'],
            'razorpay_signature' => ['required', 'string'],
        ]);

        $pending = $request->session()->get('razorpay_checkout');

        if (! $pending || $pending['razorpay_order_id'] !== $validated['razorpay_order_id']) {
            return redirect()->route('checkout.create')->withErrors(['payment' => 'Your checkout session has expired. Please try again.']);
        }

        $order = Order::where('id', $pending['order_id'])->where('user_id', $user->id)->first();

        abort_if(! $order, 404);

        try {
            Razorpay::client()->utility->verifyPaymentSignature([
                'razorpay_order_id' => $validated['razorpay_order_id'],
                'razorpay_payment_id' => $validated['razorpay_payment_id'],
                'razorpay_signature' => $validated['razorpay_signature'],
            ]);
        } catch (SignatureVerificationError $e) {
            OrderCreator::failOnlinePayment($order, $pending['items'], ['razorpay_payment_id' => $validated['razorpay_payment_id']]);

            $request->session()->forget('razorpay_checkout');

            return redirect()->route('checkout.create')->withErrors(['payment' => 'Payment verification failed. Please try again.']);
        }

        // Stock was already reserved when the payment was initiated, so this
        // just marks the order paid.
        OrderCreator::confirmOnlinePayment($order, ['razorpay_payment_id' => $validated['razorpay_payment_id']]);

        if ($pending['coupon_id'] ?? null) {
            $coupon = Coupon::find($pending['coupon_id']);

            if ($coupon) {
                static::redeemCoupon($coupon, $user, $order);
            }
        }

        $request->session()->forget('razorpay_checkout');

        if ($pending['buy_now'] ?? false) {
            $request->session()->forget('buy_now');
        } else {
            Cart::clear();
        }

        OrderCreator::sendConfirmationEmail($user, $order);

        return redirect()->route('checkout.confirmation', $order);
    }

    /**
     * Resolve the items being checked out: a single Buy Now selection if one is
     * pending in the session, otherwise the full cart.
     */
    protected function checkoutItems(Request $request): Collection
    {
        $buyNow = $request->session()->get('buy_now');

        if (! $buyNow) {
            return Cart::contents();
        }

        $product = Product::with('category', 'sizes', 'colors')->find($buyNow['product_id']);

        if (! $product) {
            return collect();
        }

        $color = $buyNow['color_id'] ? $product->colors->firstWhere('id', $buyNow['color_id']) : null;

        $item = Cart::buildItem($product, $buyNow['size'], $buyNow['quantity'], $color);

        return $item ? collect([$item]) : collect();
    }

    /**
     * Clear whatever was just checked out: the Buy Now selection if that's what
     * was used, otherwise the whole cart. Never clears the cart during a Buy Now
     * order, since the cart's own items weren't part of it.
     */
    protected function clearCheckoutItems(Request $request): void
    {
        if ($request->session()->has('buy_now')) {
            $request->session()->forget('buy_now');

            return;
        }

        Cart::clear();
    }

    protected static function redeemCoupon(Coupon $coupon, User $user, Order $order): void
    {
        $coupon->increment('used_count');

        CouponUsage::create([
            'coupon_id' => $coupon->id,
            'user_id' => $user->id,
            'order_id' => $order->id,
            'discount_amount' => $order->coupon_discount,
        ]);
    }

    public function confirmation(Request $request, Order $order): View
    {
        abort_if($order->user_id !== $request->user()->id, 403);

        $order->load('items', 'statusHistories');

        return view('site.checkout.confirmation', compact('order'));
    }
}
