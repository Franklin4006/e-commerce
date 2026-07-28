<x-layouts.admin title="Add Size">
    <h1 class="page-title">Add Size</h1>

    <div class="max-w-form-lg card">
        <form method="POST" action="{{ route('admin.sizes.store') }}">
            @csrf

            @include('admin.sizes._form')

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save</button>
                <a href="{{ route('admin.sizes.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</x-layouts.admin>
