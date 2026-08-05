<x-layouts.admin title="Admin Users">
    <div class="page-header">
        <h1 class="page-title">Admin Users</h1>
        <a href="{{ route('admin.admin-users.create') }}" class="btn btn-primary">Add Admin User</a>
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
            <form method="GET" action="{{ route('admin.admin-users.index') }}" class="filter-bar">
                <input type="text" name="search" placeholder="Search by name or email..." class="form-control" value="{{ request('search') }}">
                <button type="submit" class="btn btn-primary">Search</button>
                <a href="{{ route('admin.admin-users.index') }}" class="btn btn-secondary">Reset</a>
            </form>
        </div>

        <div class="table-wrap">
            <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Role</th>
                    <th>Joined</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($adminUsers as $adminUser)
                    <tr>
                        <td>{{ $adminUser->name }}</td>
                        <td>{{ $adminUser->email }}</td>
                        <td>{{ $adminUser->phone ?: '—' }}</td>
                        <td>{{ $adminUser->role?->label() ?? '—' }}</td>
                        <td>{{ $adminUser->created_at->format('d M Y') }}</td>
                        <td class="text-right">
                            <div class="row-actions">
                                <a href="{{ route('admin.admin-users.edit', $adminUser) }}" class="btn-icon" title="Edit">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4z"/></svg>
                                </a>
                                @if ($adminUser->id !== auth()->id())
                                    <form method="POST" action="{{ route('admin.admin-users.destroy', $adminUser) }}"
                                          onsubmit="return confirm('Delete this admin user? This cannot be undone.');" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-icon danger" title="Delete">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/></svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="empty-row">No admin users yet.</td>
                    </tr>
                @endforelse
            </tbody>
            </table>
        </div>

        @if ($adminUsers->hasPages())
            <div class="card-footer">
                {{ $adminUsers->links() }}
            </div>
        @endif
    </div>
</x-layouts.admin>
