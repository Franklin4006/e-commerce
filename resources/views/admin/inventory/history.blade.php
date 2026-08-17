<x-layouts.admin title="Stock History: {{ $product->name }}">
    <div class="page-header">
        <div>
            <h1 class="page-title" style="margin-bottom: 0.35rem;">{{ $product->name }}</h1>
            <p style="color: var(--text-muted); margin: 0;">{{ $product->category->name }}</p>
        </div>
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <span class="badge {{ $product->isLowStock() ? 'badge-danger' : 'badge-success' }}">
                {{ $product->stock }} in stock
            </span>
            <a href="{{ route('admin.inventory.show', $product) }}" class="btn btn-secondary btn-sm">Adjust Stock</a>
        </div>
    </div>

    <section class="card">
        <h2 class="section-title">Stock History</h2>

        <ul class="status-timeline">
            @forelse ($product->stockMovements as $movement)
                <li class="status-timeline-item">
                    <div class="status-timeline-meta">
                        <span class="badge {{ $movement->quantity_change >= 0 ? 'badge-success' : 'badge-danger' }}">
                            {{ $movement->quantity_change >= 0 ? '+' : '' }}{{ $movement->quantity_change }}
                        </span>
                        <span class="status-timeline-date">{{ $movement->created_at->format('d M Y, h:i A') }}</span>
                    </div>
                    <p class="status-timeline-note">
                        {{ $movement->reason }}
                        @if ($movement->color) &middot; Color: {{ $movement->color->name }} @endif
                        @if ($movement->size) &middot; Size: {{ $movement->size }} @endif
                        @if ($movement->order) (Order {{ $movement->order->order_number }}) @endif
                        &middot; Stock after: {{ $movement->stock_after }}
                    </p>
                    @if ($movement->note)
                        <p class="status-timeline-note">{{ $movement->note }}</p>
                    @endif
                    @if ($movement->changedBy)
                        <p class="status-timeline-note">by {{ $movement->changedBy->name }}</p>
                    @endif
                </li>
            @empty
                <li class="status-timeline-item" style="border-left-color: transparent;">
                    <p class="status-timeline-note">No stock movements recorded yet.</p>
                </li>
            @endforelse
        </ul>
    </section>

    <p style="margin-top: 1.5rem;">
        <a href="{{ route('admin.inventory.index') }}">&larr; Back to Inventory</a>
    </p>
</x-layouts.admin>
