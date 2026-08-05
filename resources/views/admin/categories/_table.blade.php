<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Image</th>
                <th>Name</th>
                <th>Priority</th>
                <th>Status</th>
                <th class="text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($categories as $category)
                <tr>
                    <td>
                        @if ($category->image)
                            <img src="{{ asset('storage/'.$category->image) }}" alt="{{ $category->name }}" class="thumb">
                        @else
                            <span class="thumb-placeholder">N/A</span>
                        @endif
                    </td>
                    <td>{{ $category->name }}</td>
                    <td>{{ $category->priority }}</td>
                    <td>
                        @if ($category->status)
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-muted">Inactive</span>
                        @endif
                    </td>
                    <td class="text-right">
                        <div class="row-actions">
                            @can('content-write')
                                <button type="button" class="btn-icon" title="Edit" data-action="edit-category" data-id="{{ $category->id }}">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4z"/></svg>
                                </button>
                            @endcan
                            @can('content-delete')
                                <button type="button" class="btn-icon danger" title="Delete" data-action="delete-category" data-id="{{ $category->id }}" data-name="{{ $category->name }}">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/></svg>
                                </button>
                            @endcan
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="empty-row">No categories yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div style="margin-top: 1rem;">
    {{ $categories->links() }}
</div>
