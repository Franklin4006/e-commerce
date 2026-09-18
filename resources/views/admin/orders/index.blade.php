<x-layouts.admin title="Orders">
    <div class="page-header">
        <h1 class="page-title">Orders</h1>
        @can('content-write')
            <a href="{{ route('admin.orders.create') }}" class="btn btn-primary">Create Order</a>
        @endcan
    </div>

    <div class="card card-flush">
        <div class="card-header card-header-filters">
            <form method="GET" action="{{ route('admin.orders.index') }}" class="filter-bar">
                <input type="text" name="search" placeholder="Search by order number..." class="form-control" value="{{ request('search') }}">

                <select name="status" class="form-control">
                    <option value="">All Statuses</option>
                    @foreach (\App\Models\Order::STATUSES as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>

                <select name="payment_method" class="form-control">
                    <option value="">All Payment Types</option>
                    @foreach (\App\Models\Order::PAYMENT_METHODS as $method)
                        <option value="{{ $method }}" @selected(request('payment_method') === $method)>{{ $method === 'cod' ? 'Cash on Delivery' : ucfirst($method) }}</option>
                    @endforeach
                </select>

                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">Reset</a>
            </form>
        </div>

        <form method="POST" action="{{ route('admin.orders.bulk-status.update') }}" id="bulk-status-form">
            @csrf
            @method('PUT')

            <div class="card-header card-header-filters" id="bulk-actions-bar" style="display: none;">
                <div class="filter-bar">
                    <span class="muted"><span id="bulk-selected-count">0</span> selected</span>

                    <select name="status" class="form-control" required>
                        <option value="">Set status to...</option>
                        @foreach (\App\Models\Order::STATUSES as $status)
                            <option value="{{ $status }}">{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>

                    <button type="submit" class="btn btn-primary">Apply</button>
                </div>
            </div>

            <div class="table-wrap">
                <table>
                <thead>
                    <tr>
                        <th style="width: 2rem;"><input type="checkbox" id="order-select-all"></th>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th>Total</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr>
                            <td><input type="checkbox" name="order_ids[]" value="{{ $order->id }}" class="order-row-checkbox"></td>
                            <td>{{ $order->order_number }}</td>
                            <td>{{ $order->customer_name }}</td>
                            <td>{{ $order->created_at->format('d M Y') }}</td>
                            <td>
                                {{ strtoupper($order->payment_method) }}
                                <span class="badge {{ \App\Models\Order::badgeClassForPaymentStatus($order->payment_status) }}">{{ ucfirst($order->payment_status) }}</span>
                            </td>
                            <td><span class="badge {{ \App\Models\Order::badgeClassForStatus($order->status) }}">{{ ucfirst($order->status) }}</span></td>
                            <td>₹{{ number_format($order->grand_total, 2) }}</td>
                            <td class="text-right">
                                <a href="{{ route('admin.orders.show', $order) }}" class="btn-icon" title="View">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="empty-row">No orders yet.</td>
                        </tr>
                    @endforelse
                </tbody>
                </table>
            </div>
        </form>

        @if ($orders->hasPages())
            <div class="card-footer">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

    <script src="{{ asset('js/orders-bulk-status.js') }}"></script>
</x-layouts.admin>
