<x-layouts.admin title="Create Order">
    @if (! $customer)
        <h1 class="page-title">Create Order</h1>

        <div class="max-w-form-lg card">
            <h2 class="section-title">Select Customer</h2>

            <form method="GET" action="{{ route('admin.orders.create') }}">
                <div class="form-group">
                    <label for="customer_id" class="form-label">Customer</label>
                    <select id="customer_id" name="customer_id" class="form-control" data-searchable-select onchange="this.form.submit()">
                        <option value="">Select a customer</option>
                        @foreach ($customers as $c)
                            <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->email }})</option>
                        @endforeach
                    </select>
                </div>
            </form>

            <p style="margin-top: 1rem;">
                Don't see the customer? <a href="{{ route('admin.customers.create') }}">Create a new customer</a> first.
            </p>
        </div>

        <script src="{{ asset('js/searchable-select.js') }}"></script>
    @else
        <div class="page-header">
            <div>
                <h1 class="page-title" style="margin-bottom: 0.35rem;">Create Order</h1>
                <p style="color: var(--text-muted); margin: 0;">
                    For {{ $customer->name }} ({{ $customer->email }}) &middot;
                    <a href="{{ route('admin.orders.create') }}">Change customer</a>
                </p>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-error">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('admin.orders.store') }}" id="admin-order-form">
            @csrf
            <input type="hidden" name="customer_id" value="{{ $customer->id }}">

            <section class="card">
                <h2 class="section-title">Products</h2>

                <div id="product-rows"></div>

                <button type="button" class="btn btn-secondary" id="add-product-row">+ Add Product</button>

                <template id="product-row-template">
                    <div class="product-row" style="display: flex; gap: 0.75rem; align-items: flex-start; margin-bottom: 0.75rem;">
                        <select name="product_id[]" class="form-control admin-order-product-select" data-searchable-select required>
                            <option value="">Select a product</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}" data-colors='{{ $product->colors->map(fn ($c) => ["id" => $c->id, "name" => $c->name])->toJson() }}'>{{ $product->name }} (Stock: {{ $product->stock }})</option>
                            @endforeach
                        </select>
                        <select name="color_id[]" class="form-control admin-order-color-select" style="max-width: 140px; display: none;">
                            <option value="">Color</option>
                        </select>
                        <select name="size[]" class="form-control" style="max-width: 100px;" required>
                            <option value="">Size</option>
                            @foreach (\App\Models\Size::names() as $sizeOption)
                                <option value="{{ $sizeOption }}">{{ $sizeOption }}</option>
                            @endforeach
                        </select>
                        <input type="number" name="quantity[]" value="1" min="1" class="form-control" style="max-width: 100px;" required>
                        <button type="button" class="btn-icon danger" data-action="remove-product-row" title="Remove">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="M6 6l12 12"/></svg>
                        </button>
                    </div>
                </template>
            </section>

            <section class="card">
                <h2 class="section-title">Shipping Address</h2>

                @if ($customer->addresses->isNotEmpty())
                    <div class="address-option-list">
                        @foreach ($customer->addresses as $address)
                            @include('site.partials._address-option', [
                                'address' => $address,
                                'groupName' => 'shipping_address_id',
                                'idPrefix' => 'shipping',
                                'checked' => $loop->first,
                            ])
                        @endforeach

                        <label for="shipping-address-new" class="address-option">
                            <input type="radio" id="shipping-address-new" name="shipping_address_id" value="">
                            <div class="address-option-body"><strong>+ Add a new address</strong></div>
                        </label>
                    </div>
                @endif

                <div id="new-shipping-address-fields" style="{{ $customer->addresses->isNotEmpty() ? 'display:none;' : '' }} margin-top: 1rem;">
                    @include('admin.orders._address-fields', ['prefix' => 'new_shipping'])
                </div>
            </section>

            <section class="card">
                <h2 class="section-title">Billing Address</h2>

                <div class="form-group form-check">
                    <input type="checkbox" id="billing_same_as_shipping" name="billing_same_as_shipping" value="1" checked>
                    <label for="billing_same_as_shipping">Billing address same as shipping</label>
                </div>

                <div id="billing-address-section" style="display: none;">
                    @if ($customer->addresses->isNotEmpty())
                        <div class="address-option-list">
                            @foreach ($customer->addresses as $address)
                                @include('site.partials._address-option', [
                                    'address' => $address,
                                    'groupName' => 'billing_address_id',
                                    'idPrefix' => 'billing',
                                    'checked' => $loop->first,
                                ])
                            @endforeach

                            <label for="billing-address-new" class="address-option">
                                <input type="radio" id="billing-address-new" name="billing_address_id" value="">
                                <div class="address-option-body"><strong>+ Add a new address</strong></div>
                            </label>
                        </div>
                    @endif

                    <div id="new-billing-address-fields" style="{{ $customer->addresses->isNotEmpty() ? 'display:none;' : '' }} margin-top: 1rem;">
                        @include('admin.orders._address-fields', ['prefix' => 'new_billing'])
                    </div>
                </div>
            </section>

            <section class="card">
                <h2 class="section-title">Payment Method</h2>

                <label class="payment-option">
                    <input type="radio" name="payment_method" value="cod" checked>
                    Cash on Delivery
                </label>
                <label class="payment-option">
                    <input type="radio" name="payment_method" value="manual">
                    Already Paid (offline/phone payment)
                </label>
            </section>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Create Order</button>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>

        <script src="{{ asset('js/searchable-select.js') }}"></script>
        <script src="{{ asset('js/admin-order-form.js') }}"></script>
    @endif
</x-layouts.admin>
