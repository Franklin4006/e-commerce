<x-layouts.admin title="Inventory">
    <h1 class="page-title">Inventory</h1>

    <div class="card card-flush">
        <div class="card-header card-header-filters">
            <form method="GET" action="{{ route('admin.inventory.index') }}" class="filter-bar">
                <input type="text" name="search" placeholder="Search by product name..." class="form-control" value="{{ request('search') }}">

                <label class="form-check" style="display: flex; align-items: center; gap: 0.4rem;">
                    <input type="checkbox" name="low_stock" value="1" @checked(request()->boolean('low_stock')) onchange="this.form.submit()">
                    Low stock only
                </label>

                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{ route('admin.inventory.index') }}" class="btn btn-secondary">Reset</a>
            </form>
        </div>

        <div class="table-wrap">
            <table>
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Stock</th>
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
                        <td>
                            {{ $product->stock }}
                            @if ($product->isLowStock())
                                <span class="badge badge-danger">Low Stock</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <a href="{{ route('admin.inventory.show', $product) }}" class="btn btn-secondary btn-sm">Adjust</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="empty-row">No products found.</td>
                    </tr>
                @endforelse
            </tbody>
            </table>
        </div>

        @if ($products->hasPages())
            <div class="card-footer">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</x-layouts.admin>
