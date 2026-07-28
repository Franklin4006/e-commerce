<x-layouts.admin title="Order {{ $order->order_number }}">
    <div class="page-header">
        <div>
            <h1 class="page-title" style="margin-bottom: 0.35rem;">Order {{ $order->order_number }}</h1>
            <p style="color: var(--text-muted); margin: 0;">Placed on {{ $order->created_at->format('d M Y, h:i A') }} by {{ $order->customer_name }}</p>
        </div>
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <span class="badge {{ \App\Models\Order::badgeClassForStatus($order->status) }}">{{ ucfirst($order->status) }}</span>
            <button type="button" class="btn btn-secondary btn-sm no-print" onclick="window.print()">Print</button>
            <a href="{{ route('admin.orders.invoice', $order) }}" class="btn btn-secondary btn-sm no-print">Download Invoice</a>
        </div>
    </div>

    @if (session('status'))
        <div class="alert alert-success">
            <p>{{ session('status') }}</p>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-error">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    @php
        $statusColor = match ($order->status) {
            'delivered' => 'bg-success',
            'processing', 'shipped' => 'bg-info',
            'cancelled' => 'bg-danger',
            default => 'bg-warning',
        };
    @endphp
    <div class="info-box-row">
        <div class="info-box">
            <span class="info-box-icon bg-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            </span>
            <div class="info-box-content">
                <span class="info-box-text">Order Total</span>
                <span class="info-box-number">₹{{ number_format($order->grand_total, 2) }}</span>
            </div>
        </div>
        <div class="info-box">
            <span class="info-box-icon {{ $order->payment_status === 'paid' ? 'bg-success' : 'bg-warning' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
            </span>
            <div class="info-box-content">
                <span class="info-box-text">Payment</span>
                <span class="info-box-number">{{ ucfirst($order->payment_status) }}</span>
            </div>
        </div>
        <div class="info-box">
            <span class="info-box-icon {{ $statusColor }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
            </span>
            <div class="info-box-content">
                <span class="info-box-text">Status</span>
                <span class="info-box-number">{{ ucfirst($order->status) }}</span>
            </div>
        </div>
        <div class="info-box">
            <span class="info-box-icon bg-info">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 8v13H3V8"/><path d="M1 3h22v5H1z"/><path d="M10 12h4"/></svg>
            </span>
            <div class="info-box-content">
                <span class="info-box-text">Items</span>
                <span class="info-box-number">{{ $order->items->sum('quantity') }}</span>
            </div>
        </div>
    </div>

    <section class="card card-flush">
        <div class="card-header"><h2 class="card-title">Order Items</h2></div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->items as $item)
                        <tr>
                            <td>
                                {{ $item->product_name }}
                                @if ($item->size || $item->color_name)
                                    <br>
                                    <small style="color: var(--text-muted);">
                                        @if ($item->color_name) Color: {{ $item->color_name }} @endif
                                        @if ($item->size) @if ($item->color_name) &middot; @endif Size: {{ $item->size }} @endif
                                    </small>
                                @endif
                            </td>
                            <td>{{ $item->quantity }}</td>
                            <td>₹{{ number_format($item->sale_price, 2) }}</td>
                            <td>₹{{ number_format($item->subtotal, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    <div class="checkout-layout">
        <section class="card card-flush">
            <div class="card-header"><h2 class="card-title">Shipping Address</h2></div>
            <p class="address-option-text" style="padding: 1.25rem;">
                {{ $order->shipping_name }}<br>
                {{ $order->shipping_phone }}<br>
                {{ $order->shipping_address_line1 }}@if ($order->shipping_address_line2), {{ $order->shipping_address_line2 }}@endif<br>
                {{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_postal_code }}<br>
                {{ $order->shipping_country }}
            </p>
        </section>

        <section class="card card-flush">
            <div class="card-header"><h2 class="card-title">Billing Address</h2></div>
            <p class="address-option-text" style="padding: 1.25rem;">
                {{ $order->billing_name }}<br>
                {{ $order->billing_phone }}<br>
                {{ $order->billing_address_line1 }}@if ($order->billing_address_line2), {{ $order->billing_address_line2 }}@endif<br>
                {{ $order->billing_city }}, {{ $order->billing_state }} {{ $order->billing_postal_code }}<br>
                {{ $order->billing_country }}
            </p>
        </section>
    </div>

    <section class="card">
        <h2 class="section-title">Payment</h2>
        <p>Method: {{ strtoupper($order->payment_method) }}</p>
        <p>Status: <span class="badge {{ $order->payment_status === 'paid' ? 'badge-success' : 'badge-muted' }}">{{ ucfirst($order->payment_status) }}</span></p>
        @if ($order->razorpay_payment_id)
            <p>Razorpay Payment ID: {{ $order->razorpay_payment_id }}</p>
        @endif

        @if ($order->payment_method === 'cod')
            <form method="POST" action="{{ route('admin.orders.payment-status.update', $order) }}" class="no-print" style="margin-top: 1rem;">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="payment_status" class="form-label">Update Payment Status</label>
                    <select id="payment_status" name="payment_status" class="form-control">
                        @foreach (\App\Models\Order::PAYMENT_STATUSES as $paymentStatus)
                            <option value="{{ $paymentStatus }}" @selected(old('payment_status', $order->payment_status) === $paymentStatus)>{{ ucfirst($paymentStatus) }}</option>
                        @endforeach
                    </select>
                    <small style="color: var(--text-muted);">Cash on Delivery orders aren't marked paid automatically &mdash; update this once the payment is collected.</small>
                </div>

                <button type="submit" class="btn btn-secondary">Update Payment Status</button>
            </form>
        @endif
    </section>

    <div class="checkout-layout">
        <section class="card no-print">
            <h2 class="section-title">Update Status</h2>

            <form method="POST" action="{{ route('admin.orders.status.update', $order) }}">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="status" class="form-label">Status</label>
                    <select id="status" name="status" class="form-control">
                        @foreach (\App\Models\Order::STATUSES as $status)
                            <option value="{{ $status }}" @selected(old('status', $order->status) === $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                    @if ($order->status !== 'cancelled')
                        <small style="color: var(--text-muted);">Choosing "Cancelled" will restock the ordered items.</small>
                    @endif
                </div>

                <div class="form-group">
                    <label for="note" class="form-label">Note (optional)</label>
                    <textarea id="note" name="note" rows="2" class="form-control" placeholder="e.g. Shipped via BlueDart, tracking #123456"></textarea>
                    @error('note') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                <button type="submit" class="btn btn-primary">Update Status</button>
            </form>
        </section>

        <section class="card">
            <h2 class="section-title">Status History</h2>

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
                            @if ($history->changedBy)
                                <p class="order-timeline-by">Updated by {{ $history->changedBy->name }}</p>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ol>
        </section>
    </div>

    <div class="order-summary" style="max-width: 400px; margin-left: auto;">
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

    <p class="no-print" style="margin-top: 1.5rem;">
        <a href="{{ route('admin.orders.index') }}">&larr; Back to Orders</a>
    </p>
</x-layouts.admin>
