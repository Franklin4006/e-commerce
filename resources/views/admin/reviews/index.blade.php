<x-layouts.admin title="Reviews">
    <h1 class="page-title">Reviews</h1>

    @if (session('status'))
        <div class="alert alert-success">
            <p>{{ session('status') }}</p>
        </div>
    @endif

    <div class="card card-flush">
        <div class="card-header card-header-filters">
            <form method="GET" action="{{ route('admin.reviews.index') }}" class="filter-bar">
                <select name="status" class="form-control">
                    <option value="">All Statuses</option>
                    @foreach (\App\Models\Review::STATUSES as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>

                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{ route('admin.reviews.index') }}" class="btn btn-secondary">Reset</a>
            </form>
        </div>

        <div class="table-wrap">
            <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Customer</th>
                    <th>Rating</th>
                    <th>Comment</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($reviews as $review)
                    <tr>
                        <td>{{ $review->product->name ?? 'Product removed' }}</td>
                        <td>{{ $review->user->name ?? 'Unknown' }}</td>
                        <td>
                            <span class="rating-stars">
                                @for ($i = 1; $i <= 5; $i++)
                                    <span class="{{ $i <= $review->rating ? 'star-filled' : 'star-empty' }}">&#9733;</span>
                                @endfor
                            </span>
                        </td>
                        <td>{{ Str::limit($review->comment, 60) }}</td>
                        <td>
                            @if ($review->status === 'approved')
                                <span class="badge badge-success">Approved</span>
                            @elseif ($review->status === 'rejected')
                                <span class="badge badge-danger">Rejected</span>
                            @else
                                <span class="badge badge-muted">Pending</span>
                            @endif
                        </td>
                        <td>{{ $review->created_at->format('d M Y') }}</td>
                        <td class="text-right">
                            <div class="row-actions">
                                @can('content-write')
                                    @if ($review->status !== 'approved')
                                        <form method="POST" action="{{ route('admin.reviews.status.update', $review) }}" style="display:inline;">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="approved">
                                            <button type="submit" class="btn-icon" title="Approve">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                                            </button>
                                        </form>
                                    @endif
                                    @if ($review->status !== 'rejected')
                                        <form method="POST" action="{{ route('admin.reviews.status.update', $review) }}" style="display:inline;">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="submit" class="btn-icon danger" title="Reject">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="M6 6l12 12"/></svg>
                                            </button>
                                        </form>
                                    @endif
                                @endcan
                                @can('content-delete')
                                    <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}"
                                          onsubmit="return confirm('Delete this review? This cannot be undone.');" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-icon danger" title="Delete">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/></svg>
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="empty-row">No reviews yet.</td>
                    </tr>
                @endforelse
            </tbody>
            </table>
        </div>

        @if ($reviews->hasPages())
            <div class="card-footer">
                {{ $reviews->links() }}
            </div>
        @endif
    </div>
</x-layouts.admin>
