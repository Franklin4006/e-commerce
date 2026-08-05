<x-layouts.admin title="Order Value Shipping Charges">
    <div class="page-header">
        <h1 class="page-title">Order Value Shipping Charges</h1>
        @can('content-write')
            <a href="{{ route('admin.shipping-slabs.create') }}" class="btn btn-primary">Add Slab</a>
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
        <div class="table-wrap">
            <table>
            <thead>
                <tr>
                    <th>Order Value From</th>
                    <th>Order Value To</th>
                    <th>Shipping Charge</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($shippingSlabs as $slab)
                    <tr>
                        <td>₹{{ number_format($slab->min_amount, 2) }}</td>
                        <td>{{ $slab->max_amount !== null ? '₹'.number_format($slab->max_amount, 2) : 'No limit' }}</td>
                        <td>₹{{ number_format($slab->shipping_charge, 2) }}</td>
                        <td class="text-right">
                            <div class="row-actions">
                                @can('content-write')
                                    <a href="{{ route('admin.shipping-slabs.edit', $slab) }}" class="btn-icon" title="Edit">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4z"/></svg>
                                    </a>
                                @endcan
                                @can('content-delete')
                                    <form method="POST" action="{{ route('admin.shipping-slabs.destroy', $slab) }}"
                                          onsubmit="return confirm('Delete this shipping slab? This cannot be undone.');" style="display:inline;">
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
                        <td colspan="4" class="empty-row">No shipping slabs yet.</td>
                    </tr>
                @endforelse
            </tbody>
            </table>
        </div>

        @if ($shippingSlabs->hasPages())
            <div class="card-footer">
                {{ $shippingSlabs->links() }}
            </div>
        @endif
    </div>
</x-layouts.admin>
