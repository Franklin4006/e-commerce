<div class="order-detail-layout">
    <div class="order-detail-main">
        <section class="card card-flush">
            <div class="card-header"><h2 class="card-title">Order Status</h2></div>
            <div class="card-body">
                @if ($order->status === 'cancelled')
                    <div class="order-progress-cancelled">
                        <span class="order-progress-cancelled-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M15 9l-6 6"/><path d="M9 9l6 6"/></svg>
                        </span>
                        <div>
                            <p class="order-progress-cancelled-title">Order Cancelled</p>
                            <p class="order-progress-cancelled-sub">This order will not be processed further.</p>
                        </div>
                    </div>
                @else
                    @php
                        $progressSteps = [
                            'pending' => 'Order Placed',
                            'processing' => 'Processing',
                            'shipped' => 'Shipped',
                            'delivered' => 'Delivered',
                        ];
                        $progressKeys = array_keys($progressSteps);
                        $currentStepIndex = array_search($order->status, $progressKeys);
                        $currentStepIndex = $currentStepIndex === false ? 0 : $currentStepIndex;
                        $isDelivered = $order->status === 'delivered';
                    @endphp
                    <ol class="order-progress-bar">
                        @foreach ($progressSteps as $stepKey => $stepLabel)
                            <li class="order-progress-step {{ $loop->index < $currentStepIndex || ($isDelivered && $loop->index === $currentStepIndex) ? 'is-done' : ($loop->index === $currentStepIndex ? 'is-active' : 'is-upcoming') }}">
                                <span class="order-progress-icon">
                                    @switch($stepKey)
                                        @case('pending')
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                                            @break
                                        @case('processing')
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4a2 2 0 0 0 1-1.73Z"/><path d="M3.3 7 12 12l8.7-5"/><path d="M12 22V12"/></svg>
                                            @break
                                        @case('shipped')
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/><path d="M15 18H9"/><path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.62l-3.48-4.35A1 1 0 0 0 17.52 8H14"/><circle cx="17" cy="18" r="2"/><circle cx="7" cy="18" r="2"/></svg>
                                            @break
                                        @case('delivered')
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>
                                            @break
                                    @endswitch
                                </span>
                                <span class="order-progress-label">{{ $stepLabel }}</span>
                            </li>
                        @endforeach
                    </ol>
                @endif
            </div>
        </section>

        <section class="card card-flush">
            <div class="card-header"><h2 class="card-title">Order Items</h2></div>
            <div class="table-wrap" style="box-shadow: none;">
                <table class="order-items-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Qty</th>
                            <th class="text-right">Price</th>
                            <th class="text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->items as $item)
                            @php
                                $product = $item->product;
                                $productUrl = $product ? route('product.show', array_filter([
                                    'product' => $product,
                                    'color' => $item->product_color_id,
                                    'size' => $item->size,
                                ])) : null;
                            @endphp
                            <tr>
                                <td class="order-item-name">
                                    <div class="order-item-product">
                                        @if ($product)
                                            <a href="{{ $productUrl }}" class="order-item-thumb-link">
                                                @if ($product->thumbnail)
                                                    <img src="{{ asset('uploads/'.$product->thumbnail) }}" alt="{{ $item->product_name }}" class="order-item-thumb">
                                                @else
                                                    <span class="order-item-thumb order-item-thumb--empty"></span>
                                                @endif
                                            </a>
                                        @else
                                            <span class="order-item-thumb order-item-thumb--empty"></span>
                                        @endif
                                        <div class="order-item-info">
                                            @if ($product)
                                                <a href="{{ $productUrl }}" class="order-item-link">{{ $item->product_name }}</a>
                                            @else
                                                {{ $item->product_name }}
                                            @endif
                                            @if ($item->size || $item->color_name)
                                                <br>
                                                <small style="color: var(--text-muted);">
                                                    @if ($item->color_name) {{ $item->color_name }} @endif
                                                    @if ($item->size) @if ($item->color_name) &middot; @endif Size {{ $item->size }} @endif
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $item->quantity }}</td>
                                <td class="text-right">₹{{ number_format($item->sale_price, 2) }}</td>
                                <td class="text-right">₹{{ number_format($item->subtotal, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <section class="card card-flush">
            @php
                $trackingSteps = ['pending', 'processing', 'shipped', 'delivered'];
                $trackingLabels = [
                    'pending' => 'Order Placed',
                    'processing' => 'Processing',
                    'shipped' => 'Shipped',
                    'delivered' => 'Delivered',
                ];
                $trackingCurrentIndex = array_search($order->status, $trackingSteps);
                $trackingHistoryByStatus = $order->statusHistories->keyBy('status');
                $trackingCompletedCount = $trackingCurrentIndex === false ? 0 : $trackingCurrentIndex + 1;
            @endphp
            <div class="card-header">
                <h2 class="card-title">Order Tracking</h2>
                @if ($order->status === 'cancelled')
                    <span class="badge badge-danger">Cancelled</span>
                @else
                    <span class="order-timeline-count">{{ $trackingCompletedCount }} of {{ count($trackingSteps) }} steps completed</span>
                @endif
            </div>
            <div class="card-body">
                @if ($order->status === 'cancelled')
                    <ol class="order-timeline">
                        @foreach ($order->statusHistories as $history)
                            @php
                                $tlColor = match ($history->status) {
                                    'delivered' => 'var(--box-success)',
                                    'processing', 'shipped' => 'var(--box-info)',
                                    'cancelled' => 'var(--box-danger)',
                                    default => 'var(--box-warning)',
                                };
                            @endphp
                            <li class="order-timeline-item {{ $loop->first ? 'is-current' : '' }}" style="--tl-color: {{ $tlColor }};">
                                <span class="order-timeline-marker">
                                    @if ($history->status === 'cancelled')
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="M6 6l12 12"/></svg>
                                    @else
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                                    @endif
                                </span>
                                <div class="order-timeline-content">
                                    <div class="order-timeline-head">
                                        <span class="order-timeline-status">{{ ucfirst($history->status) }}</span>
                                        <span class="order-timeline-date">{{ $history->created_at->format('d M Y, h:i A') }}</span>
                                    </div>
                                    @if ($history->note)
                                        <p class="order-timeline-note">{{ $history->note }}</p>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ol>
                @else
                    <ol class="order-timeline">
                        @foreach ($trackingSteps as $index => $statusKey)
                            @php
                                $history = $trackingHistoryByStatus->get($statusKey);
                                $isReached = $trackingCurrentIndex !== false && $index <= $trackingCurrentIndex;
                                $isCurrent = $index === $trackingCurrentIndex;
                                $tlColor = match ($statusKey) {
                                    'delivered' => 'var(--box-success)',
                                    'processing', 'shipped' => 'var(--box-info)',
                                    default => 'var(--box-warning)',
                                };
                            @endphp
                            <li class="order-timeline-item {{ $isCurrent ? 'is-current' : '' }} {{ $isReached ? '' : 'is-upcoming' }}" style="--tl-color: {{ $isReached ? $tlColor : 'var(--adminlte-border, var(--border-color))' }};">
                                <span class="order-timeline-marker">
                                    @if ($isReached)
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                                    @else
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="7"/></svg>
                                    @endif
                                </span>
                                <div class="order-timeline-content">
                                    <div class="order-timeline-head">
                                        <span class="order-timeline-status">{{ $trackingLabels[$statusKey] }}</span>
                                        @if ($history)
                                            <span class="order-timeline-date">{{ $history->created_at->format('d M Y, h:i A') }}</span>
                                        @else
                                            <span class="order-timeline-date order-timeline-date-pending">Pending</span>
                                        @endif
                                    </div>
                                    @if ($history && $history->note)
                                        <p class="order-timeline-note">{{ $history->note }}</p>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ol>
                @endif
            </div>
        </section>
    </div>

    <aside class="order-detail-side">
        <div class="order-summary">
            <h2 class="section-title">Order Summary</h2>

            <div class="order-summary-row">
                <span>Total MRP Price</span>
                <span>₹{{ number_format($order->total_mrp, 2) }}</span>
            </div>

            <div class="order-summary-row">
                <span>Total Discounted Price</span>
                <span>₹{{ number_format($order->subtotal, 2) }}</span>
            </div>

            @if ($order->total_discount > 0)
                <div class="order-summary-row order-summary-savings">
                    <span>Total Save Amount</span>
                    <span>&minus; ₹{{ number_format($order->total_discount, 2) }}</span>
                </div>
            @endif

            @if ($order->coupon_code && $order->coupon_discount > 0)
                <div class="order-summary-row order-summary-savings">
                    <span>Coupon ({{ $order->coupon_code }})</span>
                    <span>&minus; ₹{{ number_format($order->coupon_discount, 2) }}</span>
                </div>
            @endif

            <div class="order-summary-row">
                <span>Shipping Charge</span>
                <span>
                    @if ($order->shipping_charge > 0)
                        ₹{{ number_format($order->shipping_charge, 2) }}
                    @else
                        <span class="badge badge-success">FREE</span>
                    @endif
                </span>
            </div>

            <div class="order-summary-row order-summary-grand-total">
                <span>Total Amount</span>
                <span>₹{{ number_format($order->grand_total, 2) }}</span>
            </div>
        </div>

        <section class="card card-flush">
            <div class="card-header"><h2 class="card-title">Delivery Address</h2></div>
            <div class="card-body">
                <p class="order-address">
                    <strong>{{ $order->shipping_name }}</strong><br>
                    {{ $order->shipping_phone }}<br>
                    {{ $order->shipping_address_line1 }}@if ($order->shipping_address_line2), {{ $order->shipping_address_line2 }}@endif<br>
                    {{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_postal_code }}<br>
                    {{ $order->shipping_country }}
                </p>
            </div>
        </section>

        <section class="card card-flush">
            <div class="card-header"><h2 class="card-title">Billing Address</h2></div>
            <div class="card-body">
                <p class="order-address">
                    <strong>{{ $order->billing_name }}</strong><br>
                    {{ $order->billing_phone }}<br>
                    {{ $order->billing_address_line1 }}@if ($order->billing_address_line2), {{ $order->billing_address_line2 }}@endif<br>
                    {{ $order->billing_city }}, {{ $order->billing_state }} {{ $order->billing_postal_code }}<br>
                    {{ $order->billing_country }}
                </p>
            </div>
        </section>

        <section class="card card-flush">
            <div class="card-header"><h2 class="card-title">Payment</h2></div>
            <div class="card-body">
                <p class="order-payment-line">Method: <strong>{{ strtoupper($order->payment_method) }}</strong></p>
                <p class="order-payment-line">
                    Status:
                    <span class="badge {{ $order->payment_status === 'paid' ? 'badge-success' : 'badge-muted' }}">{{ ucfirst($order->payment_status) }}</span>
                </p>
            </div>
        </section>
    </aside>
</div>
