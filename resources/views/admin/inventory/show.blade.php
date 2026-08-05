<x-layouts.admin title="Inventory: {{ $product->name }}">
    <div class="page-header">
        <div>
            <h1 class="page-title" style="margin-bottom: 0.35rem;">{{ $product->name }}</h1>
            <p style="color: var(--text-muted); margin: 0;">{{ $product->category->name }}</p>
        </div>
        <span class="badge {{ $product->isLowStock() ? 'badge-danger' : 'badge-success' }}">
            {{ $product->stock }} in stock
        </span>
    </div>

    @if ($product->colors->isEmpty())
        <div style="display: flex; gap: 0.5rem; margin-bottom: 1.5rem;">
            @foreach (\App\Models\Size::names() as $sizeOption)
                @php $sizeStock = $product->sizes->firstWhere('size', $sizeOption)->stock ?? 0; @endphp
                <span class="badge {{ $sizeStock > 0 ? 'badge-success' : 'badge-danger' }}">{{ $sizeOption }}: {{ $sizeStock }}</span>
            @endforeach
        </div>
    @else
        <div style="margin-bottom: 1.5rem;">
            @foreach ($product->colors as $color)
                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                    @if ($color->hex)
                        <span style="display: inline-block; width: 14px; height: 14px; border-radius: 50%; background: {{ $color->hex }}; border: 1px solid var(--border-color, #e5e7eb);"></span>
                    @endif
                    <strong style="min-width: 100px;">{{ $color->name }}</strong>
                    @foreach (\App\Models\Size::names() as $sizeOption)
                        @php $sizeStock = $color->sizes->firstWhere('size', $sizeOption)->stock ?? 0; @endphp
                        <span class="badge {{ $sizeStock > 0 ? 'badge-success' : 'badge-danger' }}">{{ $sizeOption }}: {{ $sizeStock }}</span>
                    @endforeach
                </div>
            @endforeach
        </div>
    @endif

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

    <div class="checkout-layout">
        <section class="card">
            <h2 class="section-title">Adjust Stock</h2>

            @can('content-write')
            <form method="POST" action="{{ route('admin.inventory.adjust', $product) }}">
                @csrf

                @if ($product->colors->isNotEmpty())
                    <div class="form-group">
                        <label for="color_id" class="form-label">Color</label>
                        <select id="color_id" name="color_id" class="form-control" required>
                            @foreach ($product->colors as $color)
                                <option value="{{ $color->id }}" @selected((string) old('color_id') === (string) $color->id)>{{ $color->name }}</option>
                            @endforeach
                        </select>
                        @error('color_id') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                @endif

                <div class="form-group">
                    <label for="size" class="form-label">Size</label>
                    <select id="size" name="size" class="form-control" required>
                        @foreach (\App\Models\Size::names() as $sizeOption)
                            <option value="{{ $sizeOption }}" @selected(old('size') === $sizeOption)>{{ $sizeOption }}</option>
                        @endforeach
                    </select>
                    @error('size') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="direction" class="form-label">Direction</label>
                    <select id="direction" name="direction" class="form-control">
                        <option value="add" @selected(old('direction') === 'add')>Add Stock</option>
                        <option value="remove" @selected(old('direction') === 'remove')>Remove Stock</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="quantity" class="form-label">Quantity</label>
                    <input id="quantity" type="number" name="quantity" value="{{ old('quantity') }}" min="1" required class="form-control">
                    @error('quantity') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="note" class="form-label">Note (optional)</label>
                    <textarea id="note" name="note" rows="2" class="form-control" placeholder="e.g. Restocked from supplier, damaged units removed">{{ old('note') }}</textarea>
                    @error('note') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                <button type="submit" class="btn btn-primary">Save Adjustment</button>
            </form>
            @else
                <p class="muted">You don't have permission to adjust stock.</p>
            @endcan
        </section>

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
    </div>

    <p style="margin-top: 1.5rem;">
        <a href="{{ route('admin.inventory.index') }}">&larr; Back to Inventory</a>
    </p>
</x-layouts.admin>
