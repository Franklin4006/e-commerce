<x-layouts.admin title="Add Color">
    <h1 class="page-title">Add Color</h1>

    <div class="max-w-form-lg card">
        <form method="POST" action="{{ route('admin.colors.store') }}">
            @csrf

            @include('admin.colors._form')

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save</button>
                <a href="{{ route('admin.colors.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</x-layouts.admin>
