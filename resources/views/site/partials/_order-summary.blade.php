<div class="order-summary">
    <h2 class="section-title">Order Summary</h2>

    <div class="order-summary-row">
        <span>Total MRP Price</span>
        <span>₹{{ number_format($totalMrp, 2) }}</span>
    </div>

    <div class="order-summary-row">
        <span>Total Discounted Price</span>
        <span>₹{{ number_format($total, 2) }}</span>
    </div>

    @if ($savings > 0)
        <div class="order-summary-row order-summary-savings">
            <span>Total Save Amount</span>
            <span>&minus; ₹{{ number_format($savings, 2) }}</span>
        </div>
    @endif

    @if ($couponCode ?? null)
        <div class="order-summary-row order-summary-savings">
            <span>Coupon ({{ $couponCode }})</span>
            <span>
                &minus; ₹{{ number_format($couponDiscount, 2) }}
                <button type="button" class="link-remove-coupon" data-action="remove-coupon">Remove</button>
            </span>
        </div>
    @else
        <div class="coupon-apply-box">
            <input type="text" id="coupon-code-input" class="form-control" placeholder="Have a coupon code?">
            <button type="button" class="btn btn-secondary" data-action="apply-coupon">Apply</button>
        </div>
        <p class="coupon-error" id="coupon-error" style="display: none;"></p>
    @endif

    <div class="order-summary-row">
        <span>Shipping Charge</span>
        <span>
            @if ($shippingCharge > 0)
                ₹{{ number_format($shippingCharge, 2) }}
            @else
                <span class="badge badge-success">FREE</span>
            @endif
        </span>
    </div>

    <div class="order-summary-row order-summary-grand-total">
        <span>Total Amount</span>
        <span>₹{{ number_format($grandTotal, 2) }}</span>
    </div>

    @if ($showCheckoutButton ?? false)
        <a href="{{ route('checkout.create') }}" class="btn btn-primary order-summary-checkout-btn">Proceed to
            Checkout</a>
        @php
            $whatsappNumber = !empty($siteSettings['whatsapp_number'])
                ? preg_replace('/\D/', '', $siteSettings['whatsapp_number'])
                : null;
        @endphp
        @if ($whatsappNumber)
            <a href="https://wa.me/{{ $whatsappNumber }}" target="_blank" rel="noopener"
                class="btn btn-whatsapp checkout-whatsapp-support">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                        d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.29-1.39a9.9 9.9 0 0 0 4.75 1.21h.01c5.46 0 9.9-4.45 9.9-9.91 0-2.65-1.03-5.14-2.9-7.01A9.86 9.86 0 0 0 12.04 2zm0 18.13h-.01a8.2 8.2 0 0 1-4.19-1.15l-.3-.18-3.14.82.84-3.06-.2-.32a8.2 8.2 0 0 1-1.26-4.37c0-4.54 3.7-8.24 8.26-8.24 2.2 0 4.27.86 5.83 2.42a8.18 8.18 0 0 1 2.41 5.83c0 4.55-3.7 8.25-8.24 8.25zm4.52-6.17c-.25-.12-1.47-.72-1.7-.81-.23-.08-.39-.12-.56.13-.17.25-.64.81-.78.97-.14.17-.29.19-.53.06-.25-.12-1.05-.39-2-1.23-.74-.66-1.24-1.48-1.39-1.73-.14-.25-.02-.38.11-.51.11-.11.25-.29.37-.43.12-.15.16-.25.25-.42.08-.17.04-.31-.02-.44-.06-.12-.56-1.35-.77-1.85-.2-.48-.4-.42-.56-.43-.14-.01-.31-.01-.48-.01-.16 0-.43.06-.66.31-.23.25-.86.85-.86 2.06 0 1.22.88 2.4 1.01 2.56.12.17 1.73 2.64 4.19 3.7.59.25 1.04.4 1.4.52.59.19 1.12.16 1.54.1.47-.07 1.47-.6 1.67-1.18.21-.58.21-1.07.14-1.18-.06-.1-.23-.16-.48-.29z" />
                </svg>
                Support
            </a>
        @endif
    @endif
</div>
