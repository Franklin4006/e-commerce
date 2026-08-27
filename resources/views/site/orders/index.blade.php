<x-layouts.site title="My Orders">
    @include('site.partials._account-header')

    @include('site.partials._account-nav')

    <div class="profile-tab-panel active">
        <h2 class="section-title" style="margin: 0 0 1rem;">My Orders</h2>

        @if ($orders->isEmpty())
            <div class="empty-state">
                <span class="empty-state-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 8v13H3V8"/><path d="M1 3h22v5H1z"/><path d="M10 12h4"/></svg>
                </span>
                <p class="empty-state-title">No orders yet</p>
                <p class="empty-state-text">You haven't placed any orders yet.</p>
                <a href="{{ route('home') }}" class="btn btn-primary">Start Shopping</a>
            </div>
        @else
            <div class="orders-list">
                @foreach ($orders as $order)
                    @php
                        $statusColor = match ($order->status) {
                            'delivered' => 'var(--box-success)',
                            'processing', 'shipped' => 'var(--box-info)',
                            'cancelled' => 'var(--box-danger)',
                            default => 'var(--box-warning)',
                        };
                    @endphp
                    <article class="order-card">
                        <div class="order-card-head">
                            <div class="order-card-head-cell">
                                <span class="order-card-head-label">Order Placed</span>
                                <span class="order-card-head-value">{{ $order->created_at->format('d M Y') }}</span>
                            </div>
                            <div class="order-card-head-cell">
                                <span class="order-card-head-label">Total</span>
                                <span class="order-card-head-value">₹{{ number_format($order->grand_total, 2) }}</span>
                            </div>
                            <div class="order-card-head-cell">
                                <span class="order-card-head-label">Ship To</span>
                                <span class="order-card-head-value">{{ $order->shipping_name }}</span>
                            </div>
                            <div class="order-card-head-cell order-card-head-right">
                                <span class="order-card-head-label">Order # {{ $order->order_number }}</span>
                                <a href="{{ route('orders.show', $order) }}" class="order-card-head-link">View order details</a>
                            </div>
                        </div>

                        <div class="order-card-body">
                            <div class="order-card-main">
                                <div class="order-card-status">
                                    <span class="order-status-dot" style="--s: {{ $statusColor }};"></span>
                                    {{ $order->status === 'delivered' ? 'Delivered' : ucfirst($order->status) }}
                                    @if ($order->payment_status === 'paid')
                                        <span class="badge badge-success order-card-paid">Paid</span>
                                    @endif
                                </div>

                                <div class="order-card-items">
                                    @foreach ($order->items as $item)
                                        @php
                                            $product = $item->product;
                                            $itemUrl = $product
                                                ? route('product.show', array_filter([
                                                    'product' => $product,
                                                    'color' => $item->product_color_id,
                                                    'size' => $item->size,
                                                ]))
                                                : route('orders.show', $order);
                                        @endphp
                                        <a class="order-card-item" href="{{ $itemUrl }}">
                                            @if ($product && $product->thumbnail)
                                                <img src="{{ asset('uploads/'.$product->thumbnail) }}" alt="{{ $item->product_name }}" class="order-card-item-img">
                                            @else
                                                <span class="order-card-item-img order-card-item-img--empty">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
                                                </span>
                                            @endif
                                            <span class="order-card-item-info">
                                                <span class="order-card-item-name">{{ $item->product_name }}</span>
                                                <span class="order-card-item-meta">Qty {{ $item->quantity }} &middot; ₹{{ number_format($item->sale_price, 2) }}</span>
                                            </span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>

                            <div class="order-card-actions">
                                <a href="{{ route('orders.show', $order) }}" class="btn btn-primary btn-sm">View order</a>
                                @if ($order->status === 'delivered')
                                    <a href="{{ route('orders.invoice', $order) }}" class="btn btn-outline btn-sm">Download invoice</a>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="orders-pagination">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

    <script src="{{ asset('js/avatar-upload.js') }}"></script>
</x-layouts.site>
