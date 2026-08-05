<x-layouts.admin title="Products">
    <div class="page-header">
        <h1 class="page-title">Products</h1>
        @can('content-write')
            <a href="{{ route('admin.products.create') }}" class="btn btn-primary">Add Product</a>
        @endcan
    </div>

    <div class="card card-flush">
        <div class="card-header card-header-filters">
            <div class="filter-bar" id="product-filters">
                <input type="text" name="search" placeholder="Search by name..." class="form-control" id="filter-search" value="{{ request('search') }}">

        <select name="category_id" class="form-control" id="filter-category" data-searchable-select>
            <option value="">All Categories</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>

        <select name="status" class="form-control" id="filter-status">
            <option value="">All Statuses</option>
            <option value="1" @selected(request('status') === '1')>Active</option>
            <option value="0" @selected(request('status') === '0')>Inactive</option>
        </select>

                <button type="button" class="btn btn-secondary" id="filter-reset">Reset</button>
            </div>
        </div>

        <div id="products-table">
            @include('admin.products._table')
        </div>
    </div>

    <div class="modal-overlay" id="delete-modal">
        <div class="modal">
            <div class="modal-header">
                <h2 class="modal-title">Delete Product</h2>
                <button type="button" class="modal-close" data-action="close-delete-modal">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="modal-body">
                <p>Delete <strong id="delete-product-name"></strong>? This cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-action="close-delete-modal">Cancel</button>
                <button type="button" class="btn btn-primary" style="background: var(--color-danger);" id="confirm-delete-btn">Delete</button>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/searchable-select.js') }}"></script>
    <script src="{{ asset('js/products.js') }}"></script>
</x-layouts.admin>
