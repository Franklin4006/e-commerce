<x-layouts.admin title="States">
    <div class="page-header">
        <h1 class="page-title">States</h1>
        <a href="{{ route('admin.states.create') }}" class="btn btn-primary">Add State</a>
    </div>

    @if (session('status'))
        <div class="alert alert-success">
            <p>{{ session('status') }}</p>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-error">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div class="card card-flush">
        <div class="card-header card-header-filters">
            <form method="GET" class="filter-bar">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by state name" class="form-control">
                <button type="submit" class="btn btn-secondary">Filter</button>
            </form>
        </div>

        <div class="table-wrap">
            <table>
            <thead>
                <tr>
                    <th>State</th>
                    <th>Shipping Charge</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($states as $state)
                    <tr>
                        <td><strong>{{ $state->name }}</strong></td>
                        <td>₹{{ number_format($state->shipping_charge, 2) }}</td>
                        <td class="text-right">
                            <div class="row-actions">
                                <a href="{{ route('admin.states.edit', $state) }}" class="btn-icon" title="Edit">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4z"/></svg>
                                </a>
                                <form method="POST" action="{{ route('admin.states.destroy', $state) }}"
                                      onsubmit="return confirm('Delete this state? This cannot be undone.');" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-icon danger" title="Delete">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="empty-row">No states yet.</td>
                    </tr>
                @endforelse
            </tbody>
            </table>
        </div>

        @if ($states->hasPages())
            <div class="card-footer">
                {{ $states->links() }}
            </div>
        @endif
    </div>
</x-layouts.admin>
