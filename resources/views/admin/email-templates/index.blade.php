<x-layouts.admin title="Email Templates">
    <div class="page-header">
        <h1 class="page-title">Email Templates</h1>
        @can('content-write')
            <a href="{{ route('admin.email-templates.create') }}" class="btn btn-primary">Add Email Template</a>
        @endcan
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
            <h2 class="card-title">All Templates</h2>
        </div>

        <div class="table-wrap">
            <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Subject</th>
                    <th>System</th>
                    <th>Status</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($emailTemplates as $emailTemplate)
                    <tr>
                        <td>{{ $emailTemplate->name }}</td>
                        <td><code>{{ $emailTemplate->slug }}</code></td>
                        <td>{{ $emailTemplate->subject }}</td>
                        <td>
                            @if ($emailTemplate->is_system)
                                <span class="badge badge-muted">System</span>
                            @endif
                        </td>
                        <td>
                            @if ($emailTemplate->is_active)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-muted">Inactive</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <div class="row-actions">
                                @can('content-write')
                                    <a href="{{ route('admin.email-templates.edit', $emailTemplate) }}" class="btn-icon" title="Edit">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4z"/></svg>
                                    </a>
                                @endcan
                                @can('content-delete')
                                    @unless ($emailTemplate->is_system)
                                        <form method="POST" action="{{ route('admin.email-templates.destroy', $emailTemplate) }}"
                                              onsubmit="return confirm('Delete {{ $emailTemplate->name }}? This cannot be undone.');" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-icon danger" title="Delete">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/></svg>
                                            </button>
                                        </form>
                                    @endunless
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="empty-row">No email templates yet.</td>
                    </tr>
                @endforelse
            </tbody>
            </table>
        </div>

        @if ($emailTemplates->hasPages())
            <div class="card-footer">
                {{ $emailTemplates->links() }}
            </div>
        @endif
    </div>
</x-layouts.admin>
