<x-layouts.admin title="Edit Customer">
    <h1 class="page-title">{{ $customer->name }}</h1>

    @if (session('status'))
        <div class="alert alert-success">
            <p>{{ session('status') }}</p>
        </div>
    @endif

    <div class="checkout-layout">
        <section class="card">
            <h2 class="section-title">Customer Details</h2>

            @if ($errors->any())
                <div class="alert alert-error">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('admin.customers.update', $customer) }}">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="name" class="form-label">Name</label>
                    <input id="name" type="text" name="name" value="{{ old('name', $customer->name) }}" required class="form-control">
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email', $customer->email) }}" required class="form-control">
                </div>

                <div class="form-group">
                    <label for="phone" class="form-label">Phone</label>
                    <input id="phone" type="text" name="phone" value="{{ old('phone', $customer->phone) }}" class="form-control">
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">New Password</label>
                    <input id="password" type="password" name="password" class="form-control">
                    <small style="color: var(--text-muted);">Leave blank to keep the current password.</small>
                </div>

                <div class="form-group">
                    <label for="password_confirmation" class="form-label">Confirm New Password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" class="form-control">
                </div>

                @can('content-write')
                    <button type="submit" class="btn btn-primary">Save</button>
                @endcan
            </form>
        </section>

        <section class="card">
            <h2 class="section-title">Saved Addresses</h2>

            @forelse ($customer->addresses as $address)
                <div style="padding: 0.75rem 0; border-bottom: 1px solid var(--border-color);">
                    <p style="margin: 0 0 0.25rem;">
                        <strong>{{ $address->label }}</strong>
                        @if ($address->is_default)
                            <span class="badge badge-success">Default</span>
                        @endif
                    </p>
                    <p style="margin: 0; color: var(--text-muted); font-size: 0.875rem;">
                        {{ $address->name }}, {{ $address->phone }}<br>
                        {{ $address->address_line1 }}@if ($address->address_line2), {{ $address->address_line2 }}@endif,
                        {{ $address->city }}, {{ $address->state }} {{ $address->postal_code }}, {{ $address->country }}
                    </p>
                </div>
            @empty
                <p class="muted">No saved addresses yet.</p>
            @endforelse
        </section>
    </div>

    <section class="card">
        <h2 class="section-title">Recent Orders</h2>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Total</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($customer->orders as $order)
                        <tr>
                            <td>{{ $order->order_number }}</td>
                            <td>{{ $order->created_at->format('d M Y') }}</td>
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
                            <td colspan="5" class="empty-row">No orders yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <p style="margin-top: 1.5rem;">
        <a href="{{ route('admin.customers.index') }}">&larr; Back to Customers</a>
    </p>
</x-layouts.admin>
