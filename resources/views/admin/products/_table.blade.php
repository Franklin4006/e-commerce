<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Thumbnail</th>
                <th>Name</th>
                <th>Category</th>
                <th>MRP</th>
                <th>Sale Price</th>
                <th>Discount</th>
                <th>Stock</th>
                <th>Priority</th>
                <th>Status</th>
                <th class="text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($products as $product)
                <tr>
                    <td>
                        @if ($product->thumbnail)
                            <img src="{{ asset('uploads/'.$product->thumbnail) }}" alt="{{ $product->name }}" class="thumb">
                        @else
                            <span class="thumb-placeholder">N/A</span>
                        @endif
                    </td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->category->name }}</td>
                    <td>₹{{ number_format($product->mrp, 2) }}</td>
                    <td>₹{{ number_format($product->sale_price, 2) }}</td>
                    <td>
                        @if ($product->discountPercentage() > 0)
                            <span class="badge badge-success">{{ $product->discountPercentage() }}% OFF</span>
                        @else
                            <span class="badge badge-muted">&mdash;</span>
                        @endif
                    </td>
                    <td>
                        @if ($product->stock > 0)
                            {{ $product->stock }}
                        @else
                            <span class="badge badge-muted">Out of stock</span>
                        @endif
                    </td>
                    <td>{{ $product->priority }}</td>
                    <td>
                        @if ($product->status)
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-muted">Inactive</span>
                        @endif
                    </td>
                    <td class="text-right">
                        <div class="row-actions">
                            @can('content-write')
                                <a href="{{ route('admin.products.edit', $product) }}" class="btn-icon" title="Edit">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4z"/></svg>
                                </a>
                            @endcan
                            @can('content-delete')
                                <button type="button" class="btn-icon danger" title="Delete" data-action="delete-product" data-id="{{ $product->id }}" data-name="{{ $product->name }}">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/></svg>
                                </button>
                            @endcan
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="empty-row">No products yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div style="margin-top: 1rem;">
    {{ $products->links() }}
</div>
