@if ($errors->any())
    <div class="alert alert-error">
        @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif

<div class="form-group">
    <label for="name" class="form-label">Name</label>
    <input id="name" type="text" name="name" value="{{ old('name', $color->name ?? '') }}" required class="form-control" placeholder="e.g. Maroon, Navy Blue">
    @error('name') <span class="field-error">{{ $message }}</span> @enderror
</div>

<div class="form-group">
    <label for="hex" class="form-label">Swatch Color</label>
    <input id="hex" type="color" name="hex" value="{{ old('hex', $color->hex ?? '#000000') }}" class="form-control" style="height: 42px; padding: 0.15rem; cursor: pointer;">
    @error('hex') <span class="field-error">{{ $message }}</span> @enderror
</div>

<div class="form-group">
    <label for="sort_order" class="form-label">Sort Order</label>
    <input id="sort_order" type="number" name="sort_order" value="{{ old('sort_order', $color->sort_order ?? 0) }}" min="0" class="form-control">
    @error('sort_order') <span class="field-error">{{ $message }}</span> @enderror
    <small style="color: var(--text-muted);">Lower numbers appear first when choosing a color.</small>
</div>
