<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Image</th>
                <th>Title</th>
                <th>Position</th>
                <th>Priority</th>
                <th>Status</th>
                <th class="text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($banners as $banner)
                <tr>
                    <td>
                        @if ($banner->image)
                            <img src="{{ asset('uploads/'.$banner->image) }}" alt="{{ $banner->title }}" class="thumb">
                        @else
                            <span class="thumb-placeholder">N/A</span>
                        @endif
                    </td>
                    <td>{{ $banner->title }}</td>
                    <td>{{ \App\Models\Banner::TITLE_POSITIONS[$banner->title_position] ?? $banner->title_position }}</td>
                    <td>{{ $banner->priority }}</td>
                    <td>
                        @if ($banner->status)
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-muted">Inactive</span>
                        @endif
                    </td>
                    <td class="text-right">
                        <div class="row-actions">
                            @can('content-write')
                                <button type="button" class="btn-icon" title="Edit" data-action="edit-banner" data-id="{{ $banner->id }}">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4z"/></svg>
                                </button>
                            @endcan
                            @can('content-delete')
                                <button type="button" class="btn-icon danger" title="Delete" data-action="delete-banner" data-id="{{ $banner->id }}" data-name="{{ $banner->title }}">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/></svg>
                                </button>
                            @endcan
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="empty-row">No banners yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div style="margin-top: 1rem;">
    {{ $banners->links() }}
</div>
