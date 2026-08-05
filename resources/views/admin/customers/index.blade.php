<x-layouts.admin title="Customers">
    <div class="page-header">
        <h1 class="page-title">Customers</h1>
        @can('content-write')
            <a href="{{ route('admin.customers.create') }}" class="btn btn-primary">Add Customer</a>
        @endcan
    </div>

    @if (session('status'))
        <div class="alert alert-success">
            <p>{{ session('status') }}</p>
        </div>
    @endif

    <div class="card card-flush">
        <div class="card-header card-header-filters">
            <form method="GET" action="{{ route('admin.customers.index') }}" class="filter-bar">
                <input type="text" name="search" placeholder="Search by name or email..." class="form-control" value="{{ request('search') }}">
                <button type="submit" class="btn btn-primary">Search</button>
                <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary">Reset</a>
            </form>
        </div>

        <div class="table-wrap">
            <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Orders</th>
                    <th>Joined</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($customers as $customer)
                    <tr>
                        <td>{{ $customer->name }}</td>
                        <td>{{ $customer->email }}</td>
                        <td>{{ $customer->phone ?: '—' }}</td>
                        <td>{{ $customer->orders_count }}</td>
                        <td>{{ $customer->created_at->format('d M Y') }}</td>
                        <td class="text-right">
                            <a href="{{ route('admin.customers.edit', $customer) }}" class="btn-icon" title="Edit">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4z"/></svg>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="empty-row">No customers yet.</td>
                    </tr>
                @endforelse
            </tbody>
            </table>
        </div>

        @if ($customers->hasPages())
            <div class="card-footer">
                {{ $customers->links() }}
            </div>
        @endif
    </div>
</x-layouts.admin>
