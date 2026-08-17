<x-layouts.admin title="Inventory: {{ $product->name }}">
    <div class="page-header">
        <div>
            <h1 class="page-title" style="margin-bottom: 0.35rem;">{{ $product->name }}</h1>
            <p style="color: var(--text-muted); margin: 0;">{{ $product->category->name }}</p>
        </div>
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <span class="badge {{ $product->isLowStock() ? 'badge-danger' : 'badge-success' }}">
                {{ $product->stock }} in stock
            </span>
            <a href="{{ route('admin.inventory.history', $product) }}" class="btn btn-secondary btn-sm">View History</a>
        </div>
    </div>

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

    <section class="card">
        <h2 class="section-title">Adjust Stock</h2>

        @can('content-write')
        <form method="POST" action="{{ route('admin.inventory.adjust', $product) }}">
            @csrf

            <div class="form-group">
                <label for="color_id" class="form-label">Color</label>
                <select id="color_id" name="color_id" class="form-control" required>
                    @foreach ($product->colors as $color)
                        <option value="{{ $color->id }}" @selected((string) old('color_id') === (string) $color->id)>{{ $color->name }}</option>
                    @endforeach
                </select>
                @error('color_id') <span class="field-error">{{ $message }}</span> @enderror
            </div>

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

    <p style="margin-top: 1.5rem;">
        <a href="{{ route('admin.inventory.index') }}">&larr; Back to Inventory</a>
    </p>
</x-layouts.admin>
