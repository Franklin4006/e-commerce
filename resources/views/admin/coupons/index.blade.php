<x-layouts.admin title="Coupons">
    <div class="page-header">
        <h1 class="page-title">Coupons</h1>
        @can('content-write')
            <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary">Add Coupon</a>
        @endcan
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

    <div class="card card-flush">
        <div class="card-header card-header-filters">
            <form method="GET" class="filter-bar">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by code" class="form-control">
                <button type="submit" class="btn btn-secondary">Filter</button>
            </form>
        </div>

        <div class="table-wrap">
            <table>
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Type</th>
                    <th>Value</th>
                    <th>Usage</th>
                    <th>Expires</th>
                    <th>Status</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($coupons as $coupon)
                    <tr>
                        <td><strong>{{ $coupon->code }}</strong></td>
                        <td>{{ $coupon->type === 'percentage' ? 'Percentage' : 'Fixed Amount' }}</td>
                        <td>
                            @if ($coupon->type === 'percentage')
                                {{ rtrim(rtrim(number_format($coupon->value, 2), '0'), '.') }}%
                            @else
                                ₹{{ number_format($coupon->value, 2) }}
                            @endif
                        </td>
                        <td>{{ $coupon->used_count }}{{ $coupon->usage_limit ? ' / '.$coupon->usage_limit : '' }}</td>
                        <td>{{ $coupon->expires_at ? $coupon->expires_at->format('d M Y') : '—' }}</td>
                        <td>
                            @if ($coupon->is_active)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-muted">Inactive</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <div class="row-actions">
                                @can('content-write')
                                    <a href="{{ route('admin.coupons.edit', $coupon) }}" class="btn-icon" title="Edit">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4z"/></svg>
                                    </a>
                                @endcan
                                @can('content-delete')
                                    <form method="POST" action="{{ route('admin.coupons.destroy', $coupon) }}"
                                          onsubmit="return confirm('Delete this coupon? This cannot be undone.');" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-icon danger" title="Delete">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/></svg>
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="empty-row">No coupons yet.</td>
                    </tr>
                @endforelse
            </tbody>
            </table>
        </div>

        @if ($coupons->hasPages())
            <div class="card-footer">
                {{ $coupons->links() }}
            </div>
        @endif
    </div>
</x-layouts.admin>
