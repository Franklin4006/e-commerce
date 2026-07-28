@if ($items->isNotEmpty())
    @php $itemCount = $items->sum('quantity'); @endphp
    <div class="cart-layout">
        <div class="cart-main">
            <div class="cart-panel">
                <div class="cart-panel-head">
                    <h2 class="cart-panel-title">Shopping Cart</h2>
                    <span class="cart-panel-price-label">Price</span>
                </div>

                <div class="cart-table">
                    @foreach ($items as $item)
                        <div class="cart-row">
                            @if ($item['product']->thumbnail)
                                <img src="{{ asset('storage/'.$item['product']->thumbnail) }}" alt="{{ $item['product']->name }}" class="cart-row-image">
                            @else
                                <div class="cart-row-image"></div>
                            @endif

                            <div class="cart-row-info">
                                <a href="{{ route('product.show', $item['product']) }}" class="cart-row-name">{{ $item['product']->name }}</a>
                                @if ($item['color'])
                                    <span class="cart-row-size">Color: {{ $item['color']->name }}</span>
                                @endif
                                <span class="cart-row-size">Size: {{ $item['size'] }}</span>
                                @if ($item['product']->stockForSize($item['size'], $item['color']?->id) > 0)
                                    <span class="cart-row-stock in-stock">In stock</span>
                                @else
                                    <span class="cart-row-stock out-of-stock">Out of stock</span>
                                @endif
                                <div class="cart-row-controls">
                                    <div class="qty-stepper cart-row-qty">
                                        <button type="button" class="qty-btn" data-action="cart-qty-decrease">&minus;</button>
                                        <input type="text" class="qty-input" value="{{ $item['quantity'] }}" data-product-id="{{ $item['product']->id }}" data-size="{{ $item['size'] }}" data-color-id="{{ $item['color']?->id }}" inputmode="numeric" aria-label="Quantity">
                                        <button type="button" class="qty-btn" data-action="cart-qty-increase">+</button>
                                    </div>
                                    <button type="button" class="cart-row-delete" data-action="cart-remove" data-product-id="{{ $item['product']->id }}" data-size="{{ $item['size'] }}" data-color-id="{{ $item['color']?->id }}">Delete</button>
                                </div>
                            </div>

                            <div class="cart-row-pricing">
                                <p class="cart-row-subtotal">₹{{ number_format($item['subtotal'], 2) }}</p>
                                @if ($item['product']->discountPercentage() > 0)
                                    <p class="cart-row-mrp"><span class="strike">₹{{ number_format($item['product']->mrp * $item['quantity'], 2) }}</span></p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="cart-panel-foot">
                    Subtotal ({{ $itemCount }} {{ Str::plural('item', $itemCount) }}):
                    <strong>₹{{ number_format($total, 2) }}</strong>
                </div>
            </div>
        </div>

        <div class="cart-side">
            @include('site.partials._order-summary', ['showCheckoutButton' => true])
        </div>
    </div>
@else
    <div class="empty-state">
        <span class="empty-state-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
        </span>
        <p class="empty-state-title">Your cart is empty</p>
        <p class="empty-state-text">Looks like you haven't added anything to your cart yet.</p>
        <a href="{{ route('home') }}" class="btn btn-primary">Continue Shopping</a>
    </div>
@endif
