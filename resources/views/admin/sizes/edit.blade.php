<x-layouts.admin title="Edit Size">
    <h1 class="page-title">Edit Size</h1>

    <div class="max-w-form-lg card">
        <form method="POST" action="{{ route('admin.sizes.update', $size) }}">
            @csrf
            @method('PUT')

            @include('admin.sizes._form')

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save</button>
                <a href="{{ route('admin.sizes.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</x-layouts.admin>
