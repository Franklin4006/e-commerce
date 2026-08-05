<x-layouts.admin title="Add State">
    <div class="page-header">
        <h1 class="page-title">Add State</h1>
        <a href="{{ route('admin.states.index') }}" class="btn btn-secondary">&larr; Back to States</a>
    </div>

    <form method="POST" action="{{ route('admin.states.store') }}" class="max-w-form-lg">
        @csrf

        <div class="card card-flush">
            <div class="card-header">
                <h2 class="card-title">State Details</h2>
            </div>
            <div class="card-body">
                @include('admin.states._form')
            </div>
            <div class="card-footer form-actions">
                <button type="submit" class="btn btn-primary">Save State</button>
                <a href="{{ route('admin.states.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </div>
    </form>
</x-layouts.admin>
