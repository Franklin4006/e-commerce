<x-layouts.admin title="Edit Admin User">
    <div class="page-header">
        <h1 class="page-title">Edit Admin User</h1>
        <a href="{{ route('admin.admin-users.index') }}" class="btn btn-secondary">&larr; Back to Admin Users</a>
    </div>

    @if (session('status'))
        <div class="alert alert-success">
            <p>{{ session('status') }}</p>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.admin-users.update', $adminUser) }}" class="max-w-form-lg">
        @csrf
        @method('PUT')

        <div class="card card-flush">
            <div class="card-header">
                <h2 class="card-title">Admin User Details</h2>
            </div>
            <div class="card-body">
                @include('admin.admin-users._form')
            </div>
            <div class="card-footer form-actions">
                <button type="submit" class="btn btn-primary">Save Admin User</button>
                <a href="{{ route('admin.admin-users.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </div>
    </form>
</x-layouts.admin>
