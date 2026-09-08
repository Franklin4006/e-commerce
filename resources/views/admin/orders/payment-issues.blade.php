<x-layouts.admin title="Payment Issues">
    <div class="page-header">
        <h1 class="page-title">Payment Issues</h1>
    </div>

    @if (session('status'))
        <div class="alert alert-success">
            <p>{{ session('status') }}</p>
        </div>
    @endif

    <div class="card card-flush">
        <div class="card-header card-header-filters">
            <form method="GET" action="{{ route('admin.orders.payment-issues') }}" class="filter-bar">
                <input type="text" name="search" placeholder="Search by order number..." class="form-control" value="{{ request('search') }}">

                <select name="payment_status" class="form-control">
                    <option value="">Pending &amp; Failed</option>
                    <option value="pending" @selected(request('payment_status') === 'pending')>Pending</option>
                    <option value="failed" @selected(request('payment_status') === 'failed')>Failed</option>
                </select>

                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{ route('admin.orders.payment-issues') }}" class="btn btn-secondary">Reset</a>
            </form>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Method</th>
                        <th>Payment</th>
                        <th>Order Status</th>
                        <th>Total</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr>
                            <td>{{ $order->order_number }}</td>
                            <td>{{ $order->customer_name }}</td>
                            <td>{{ $order->created_at->format('d M Y') }}</td>
                            <td>{{ strtoupper($order->payment_method) }}</td>
                            <td><span class="badge {{ \App\Models\Order::badgeClassForPaymentStatus($order->payment_status) }}">{{ ucfirst($order->payment_status) }}</span></td>
                            <td><span class="badge {{ \App\Models\Order::badgeClassForStatus($order->status) }}">{{ ucfirst($order->status) }}</span></td>
                            <td>₹{{ number_format($order->grand_total, 2) }}</td>
                            <td class="text-right">
                                <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-secondary btn-sm">Review &amp; Update</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="empty-row">No pending or failed payments right now.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($orders->hasPages())
            <div class="card-footer">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</x-layouts.admin>
