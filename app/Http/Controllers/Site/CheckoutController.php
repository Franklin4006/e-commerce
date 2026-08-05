<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Order;
use App\Models\Setting;
use App\Models\User;
use App\Support\Cart;
use App\Support\OrderCreator;
use App\Support\OrderTotals;
use App\Support\Razorpay;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Razorpay\Api\Errors\SignatureVerificationError;
use RuntimeException;

class CheckoutController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        $items = Cart::contents();

        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->withErrors(['cart' => 'Your cart is empty.']);
        }

        $addresses = $request->user()->addresses()->orderByDesc('is_default')->latest()->get();

        if ($addresses->isEmpty()) {
            return redirect()->route('addresses.create', ['redirect' => 'checkout'])->with('status', 'Please add an address before checking out.');
        }

        $coupon = Coupon::resolveApplied($request, Cart::total());

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
        ] + OrderTotals::forCart($previewAddress, $coupon);

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

        $items = Cart::contents();

        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->withErrors(['cart' => 'Your cart is empty.']);
        }

        $coupon = Coupon::resolveApplied($request, Cart::total());
        $totals = OrderTotals::forCart($shippingAddress, $coupon);
        $itemsData = OrderCreator::snapshotItems($items);

        if ($validated['payment_method'] === 'cod') {
            try {
                $order = OrderCreator::create($user, $itemsData, $shippingAddress, $billingAddress, $totals, 'cod', 'pending');
            } catch (RuntimeException $e) {
                return back()->withErrors(['cart' => $e->getMessage()]);
            }

            if ($coupon) {
                static::redeemCoupon($coupon, $user, $order);
            }

            Cart::clear();

            OrderCreator::sendConfirmationEmail($user, $order);

            return redirect()->route('checkout.confirmation', $order);
        }

        $amountInPaise = (int) round($totals['grandTotal'] * 100);

        $razorpayOrder = Razorpay::client()->order->create([
            'amount' => $amountInPaise,
            'currency' => 'INR',
            'receipt' => 'rcpt_'.Str::random(12),
        ]);

        $request->session()->put('razorpay_checkout', [
            'razorpay_order_id' => $razorpayOrder->id,
            'shipping_address_id' => $shippingAddress->id,
            'billing_address_id' => $billingAddress->id,
            'items' => $itemsData,
            'totals' => $totals,
            'coupon_id' => $coupon?->id,
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

        try {
            Razorpay::client()->utility->verifyPaymentSignature([
                'razorpay_order_id' => $validated['razorpay_order_id'],
                'razorpay_payment_id' => $validated['razorpay_payment_id'],
                'razorpay_signature' => $validated['razorpay_signature'],
            ]);
        } catch (SignatureVerificationError $e) {
            return redirect()->route('checkout.create')->withErrors(['payment' => 'Payment verification failed. Please try again.']);
        }

        $shippingAddress = $user->addresses()->find($pending['shipping_address_id']);
        $billingAddress = $user->addresses()->find($pending['billing_address_id']);

        abort_if(! $shippingAddress || ! $billingAddress, 404);

        try {
            $order = OrderCreator::create(
                $user,
                $pending['items'],
                $shippingAddress,
                $billingAddress,
                $pending['totals'],
                'razorpay',
                'paid',
                [
                    'razorpay_order_id' => $validated['razorpay_order_id'],
                    'razorpay_payment_id' => $validated['razorpay_payment_id'],
                ]
            );
        } catch (RuntimeException $e) {
            // Payment has already been captured by Razorpay at this point. Fulfilment
            // failing on stock is rare (cart was priced/locked at initiate time) but if
            // it happens this needs a manual refund via the Razorpay dashboard for now.
            return redirect()->route('checkout.create')->withErrors(['cart' => $e->getMessage()]);
        }

        if ($pending['coupon_id'] ?? null) {
            $coupon = Coupon::find($pending['coupon_id']);

            if ($coupon) {
                static::redeemCoupon($coupon, $user, $order);
            }
        }

        $request->session()->forget('razorpay_checkout');

        Cart::clear();

        OrderCreator::sendConfirmationEmail($user, $order);

        return redirect()->route('checkout.confirmation', $order);
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
