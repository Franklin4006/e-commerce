<x-layouts.site title="Checkout">
    @php
        $whatsappNumber = ! empty($siteSettings['whatsapp_number']) ? preg_replace('/\D/', '', $siteSettings['whatsapp_number']) : null;
    @endphp

    <h1 class="page-title">Checkout</h1>

    @if ($errors->any())
        <div class="alert alert-error">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('checkout.store') }}">
        @csrf

        <div class="checkout-layout">
            <div class="checkout-main">
                <section class="card">
                    <h2 class="section-title">Shipping Address</h2>

                    <div class="address-option-list">
                        @foreach ($addresses as $address)
                            @include('site.partials._address-option', [
                                'address' => $address,
                                'groupName' => 'shipping_address_id',
                                'idPrefix' => 'shipping',
                                'checked' => $address->id === (int) request('shipping_address_id', $addresses->first()->id),
                            ])
                        @endforeach
                    </div>

                    <a href="{{ route('addresses.create', ['redirect' => 'checkout']) }}" class="link-add-address">+ Add a new address</a>
                </section>

                <section class="card">
                    <h2 class="section-title">Billing Address</h2>

                    <div class="form-group form-check">
                        <input type="checkbox" id="billing_same_as_shipping" name="billing_same_as_shipping" value="1" checked>
                        <label for="billing_same_as_shipping">Billing address same as shipping</label>
                    </div>

                    <div class="address-option-list" id="billing-address-list" style="display: none;">
                        @foreach ($addresses as $address)
                            @include('site.partials._address-option', [
                                'address' => $address,
                                'groupName' => 'billing_address_id',
                                'idPrefix' => 'billing',
                                'checked' => $loop->first,
                            ])
                        @endforeach
                    </div>
                </section>

                <section class="card">
                    <h2 class="section-title">Payment Method</h2>

                    @if (! $codEnabled && ! $razorpayEnabled)
                        <p class="field-error">No payment methods are currently available. Please contact support.</p>
                    @endif

                    @if ($codEnabled)
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="cod" checked>
                            Cash on Delivery
                        </label>
                    @endif
                    @if ($razorpayEnabled)
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="razorpay" {{ ! $codEnabled ? 'checked' : '' }}>
                            Pay Online (Card / UPI / Netbanking via Razorpay)
                        </label>
                    @endif
                </section>
            </div>

            <div class="checkout-summary">
                @include('site.partials._order-summary', ['showCheckoutButton' => false])

                <button type="submit" class="btn btn-primary order-summary-checkout-btn">Place Order</button>


                @if ($whatsappNumber)
                    <a href="https://wa.me/{{ $whatsappNumber }}" target="_blank" rel="noopener" class="btn btn-whatsapp checkout-whatsapp-support">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.29-1.39a9.9 9.9 0 0 0 4.75 1.21h.01c5.46 0 9.9-4.45 9.9-9.91 0-2.65-1.03-5.14-2.9-7.01A9.86 9.86 0 0 0 12.04 2zm0 18.13h-.01a8.2 8.2 0 0 1-4.19-1.15l-.3-.18-3.14.82.84-3.06-.2-.32a8.2 8.2 0 0 1-1.26-4.37c0-4.54 3.7-8.24 8.26-8.24 2.2 0 4.27.86 5.83 2.42a8.18 8.18 0 0 1 2.41 5.83c0 4.55-3.7 8.25-8.24 8.25zm4.52-6.17c-.25-.12-1.47-.72-1.7-.81-.23-.08-.39-.12-.56.13-.17.25-.64.81-.78.97-.14.17-.29.19-.53.06-.25-.12-1.05-.39-2-1.23-.74-.66-1.24-1.48-1.39-1.73-.14-.25-.02-.38.11-.51.11-.11.25-.29.37-.43.12-.15.16-.25.25-.42.08-.17.04-.31-.02-.44-.06-.12-.56-1.35-.77-1.85-.2-.48-.4-.42-.56-.43-.14-.01-.31-.01-.48-.01-.16 0-.43.06-.66.31-.23.25-.86.85-.86 2.06 0 1.22.88 2.4 1.01 2.56.12.17 1.73 2.64 4.19 3.7.59.25 1.04.4 1.4.52.59.19 1.12.16 1.54.1.47-.07 1.47-.6 1.67-1.18.21-.58.21-1.07.14-1.18-.06-.1-.23-.16-.48-.29z"/></svg>
                        Support
                    </a>
                @endif
            </div>
        </div>
    </form>

    <script src="{{ asset('js/checkout.js') }}"></script>
    <script src="{{ asset('js/coupon.js') }}"></script>
</x-layouts.site>
