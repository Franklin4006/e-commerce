<x-layouts.admin title="Add Shipping Slab">
    <div class="page-header">
        <h1 class="page-title">Add Shipping Slab</h1>
        <a href="{{ route('admin.shipping-slabs.index') }}" class="btn btn-secondary">&larr; Back to Shipping Slabs</a>
    </div>

    <form method="POST" action="{{ route('admin.shipping-slabs.store') }}" class="max-w-form-lg">
        @csrf

        <div class="card card-flush">
            <div class="card-header">
                <h2 class="card-title">Slab Details</h2>
            </div>
            <div class="card-body">
                @include('admin.shipping-slabs._form')
            </div>
            <div class="card-footer form-actions">
                <button type="submit" class="btn btn-primary">Save Slab</button>
                <a href="{{ route('admin.shipping-slabs.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </div>
    </form>
</x-layouts.admin>
