<x-layouts.admin title="Edit Product">
    <div class="page-header">
        <h1 class="page-title">Edit Product</h1>
        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">&larr; Back to Products</a>
    </div>

    <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data" class="max-w-form-lg">
        @csrf
        @method('PUT')

        <div class="card card-flush">
            <div class="card-header">
                <h2 class="card-title">Product Details</h2>
            </div>
            <div class="card-body">
                @include('admin.products._form')
            </div>
            <div class="card-footer form-actions">
                <button type="submit" class="btn btn-primary">Save Product</button>
                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </div>
    </form>

    <script src="{{ asset('js/searchable-select.js') }}"></script>
    <script src="{{ asset('js/rich-text-editor.js') }}"></script>
    <script src="{{ asset('js/product-form.js') }}"></script>
    <script src="{{ asset('js/product-slug-form.js') }}"></script>
    <script src="{{ asset('js/product-specs-form.js') }}"></script>
    <script src="{{ asset('js/product-colors-form.js') }}"></script>
</x-layouts.admin>
