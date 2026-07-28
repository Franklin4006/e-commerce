<x-layouts.admin title="Sizes">
    <div class="page-header">
        <h1 class="page-title">Sizes</h1>
        <a href="{{ route('admin.sizes.create') }}" class="btn btn-primary">Add Size</a>
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
        <div class="card-header">
            <h2 class="card-title">All Sizes</h2>
        </div>

        <div class="table-wrap">
            <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Sort Order</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($sizes as $size)
                    <tr>
                        <td>{{ $size->name }}</td>
                        <td>{{ $size->sort_order }}</td>
                        <td class="text-right">
                            <div class="row-actions">
                                <a href="{{ route('admin.sizes.edit', $size) }}" class="btn-icon" title="Edit">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4z"/></svg>
                                </a>
                                <form method="POST" action="{{ route('admin.sizes.destroy', $size) }}"
                                      onsubmit="return confirm('Delete this size? This cannot be undone.');" style="display:inline;">
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
                        <td colspan="3" class="empty-row">No sizes yet.</td>
                    </tr>
                @endforelse
            </tbody>
            </table>
        </div>

        @if ($sizes->hasPages())
            <div class="card-footer">
                {{ $sizes->links() }}
            </div>
        @endif
    </div>
</x-layouts.admin>
