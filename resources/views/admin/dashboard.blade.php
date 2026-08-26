<x-layouts.admin title="Dashboard">
    <h1 class="page-title">Dashboard</h1>

    <div class="card card-flush">
        <div class="card-header card-header-filters">
            <form method="GET" action="{{ route('admin.dashboard') }}" class="filter-bar">
                <select name="period" id="dashboard-period-select" class="form-control">
                    @foreach ($periods as $value => $label)
                        <option value="{{ $value }}" @selected($period === $value)>{{ $label }}</option>
                    @endforeach
                </select>

                <div id="dashboard-from-group" style="{{ $period === 'custom' ? '' : 'display: none;' }}">
                    <input type="date" name="from" value="{{ request('from') }}" class="form-control" placeholder="From">
                </div>

                <div id="dashboard-to-group" style="{{ $period === 'custom' ? '' : 'display: none;' }}">
                    <input type="date" name="to" value="{{ request('to') }}" class="form-control" placeholder="To">
                </div>

                <button type="submit" class="btn btn-primary">Filter</button>
                @if ($period !== 'this_month')
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Reset</a>
                @endif

                <span class="muted" style="margin-left: auto; align-self: center;">{{ $rangeLabel }}</span>
            </form>
        </div>
    </div>

    <div class="dashboard-stats">
        <a href="{{ route('admin.orders.index') }}" class="stat-tile tile-success">
            <span class="stat-tile-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
            </span>
            <div class="stat-tile-body">
                <div class="stat-tile-value">₹{{ number_format($revenueTotal, 0) }}</div>
                <div class="stat-tile-label">Revenue ({{ $periodLabel }})</div>
            </div>
        </a>

        <a href="{{ route('admin.orders.index') }}" class="stat-tile tile-info">
            <span class="stat-tile-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
            </span>
            <div class="stat-tile-body">
                <div class="stat-tile-value">{{ number_format($orderCount) }}</div>
                <div class="stat-tile-label">Orders ({{ $periodLabel }})</div>
            </div>
        </a>

        <a href="{{ route('admin.customers.index') }}" class="stat-tile tile-warning">
            <span class="stat-tile-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </span>
            <div class="stat-tile-body">
                <div class="stat-tile-value">{{ number_format($customerCount) }}</div>
                <div class="stat-tile-label">Customers total</div>
            </div>
        </a>

        <a href="{{ route('admin.products.index') }}" class="stat-tile tile-primary">
            <span class="stat-tile-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 8v13H3V8"/><path d="M1 3h22v5H1z"/><path d="M10 12h4"/></svg>
            </span>
            <div class="stat-tile-body">
                <div class="stat-tile-value">{{ number_format($activeProductCount) }}</div>
                <div class="stat-tile-label">Active products</div>
            </div>
        </a>
    </div>

    <div class="card card-flush card-outline dashboard-chart-card">
        <div class="card-header">
            <h2 class="card-title">Revenue ({{ $periodLabel }})</h2>
        </div>
        <div class="card-body">
            @php $chartMax = max(1, collect($chartData)->max('total')); @endphp
            <div class="dashboard-chart">
                @foreach ($chartData as $day)
                    <div class="dashboard-chart-bar" style="height: {{ $day['total'] > 0 ? max(2, round($day['total'] / $chartMax * 100)) : 1 }}%"
                         title="{{ $day['label'] }}: ₹{{ number_format($day['total'], 2) }}"></div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="dashboard-columns">
        <div class="card card-flush card-outline card-outline-info">
            <div class="card-header">
                <h2 class="card-title">Recent Orders</h2>
                <div class="card-tools">
                    <a href="{{ route('admin.orders.index') }}" class="btn-link">View all</a>
                </div>
            </div>

            <div class="card-body">
                @if ($recentOrders->isEmpty())
                    <p class="muted">No orders yet.</p>
                @else
                    <div class="table-wrap" style="box-shadow: none;">
                        <table>
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Customer</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th class="text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recentOrders as $order)
                                    <tr>
                                        <td>{{ $order->order_number }}</td>
                                        <td>{{ $order->customer_name }}</td>
                                        <td>₹{{ number_format($order->grand_total, 2) }}</td>
                                        <td><span class="badge {{ \App\Models\Order::badgeClassForStatus($order->status) }}">{{ ucfirst($order->status) }}</span></td>
                                        <td>{{ $order->created_at->format('d M Y') }}</td>
                                        <td class="text-right">
                                            <a href="{{ route('admin.orders.show', $order) }}" class="btn-icon" title="View">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <div class="card card-flush card-outline card-outline-danger">
            <div class="card-header">
                <h2 class="card-title">Low Stock Alerts</h2>
                <div class="card-tools">
                    <a href="{{ route('admin.inventory.index') }}" class="btn-link">View all</a>
                </div>
            </div>

            <div class="card-body">
                @if ($lowStockProducts->isEmpty())
                    <p class="muted">All products are well stocked.</p>
                @else
                    <div class="low-stock-list">
                        @foreach ($lowStockProducts as $product)
                            <a href="{{ route('admin.inventory.show', $product) }}" class="low-stock-item">
                                <span class="low-stock-item-name">{{ $product->name }}</span>
                                <span class="badge {{ $product->stock <= 0 ? 'badge-danger' : 'badge-muted' }}">{{ $product->stock }} left</span>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script src="{{ asset('js/dashboard-filter.js') }}"></script>
</x-layouts.admin>
