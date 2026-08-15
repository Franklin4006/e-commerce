@if ($errors->any())
    <div class="alert alert-error">
        @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif

<div class="form-group">
    <label for="name" class="form-label">Product Name</label>
    <input id="name" type="text" name="name" value="{{ old('name', $product->name ?? '') }}" required class="form-control">
    @error('name') <span class="field-error">{{ $message }}</span> @enderror
</div>

<div class="form-group">
    <label for="category_id" class="form-label">Category</label>
    <select id="category_id" name="category_id" required class="form-control" data-searchable-select>
        <option value="">Select a category</option>
        @foreach ($categories as $category)
            <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id ?? '') == $category->id)>{{ $category->name }}</option>
        @endforeach
    </select>
    @error('category_id') <span class="field-error">{{ $message }}</span> @enderror
</div>

<div class="form-group">
    <label for="description" class="form-label">Description</label>
    <textarea id="description" name="description" rows="3" class="form-control" data-rich-text>{{ old('description', $product->description ?? '') }}</textarea>
    @error('description') <span class="field-error">{{ $message }}</span> @enderror
</div>

<div class="form-row">
    <div class="form-group">
        <label for="mrp" class="form-label">MRP (₹)</label>
        <input id="mrp" type="number" name="mrp" value="{{ old('mrp', $product->mrp ?? 0) }}" min="0" step="0.01" required class="form-control">
        @error('mrp') <span class="field-error">{{ $message }}</span> @enderror
    </div>

    <div class="form-group">
        <label for="sale_price" class="form-label">Sale Price (₹)</label>
        <input id="sale_price" type="number" name="sale_price" value="{{ old('sale_price', $product->sale_price ?? 0) }}" min="0" step="0.01" required class="form-control">
        @error('sale_price') <span class="field-error">{{ $message }}</span> @enderror
        <small style="color: var(--text-muted);">Must not exceed MRP.</small>
    </div>
</div>

@php
    $sizeNames = \App\Models\Size::names();
    $colorlessSizes = isset($product) ? $product->sizes->where('product_color_id', null) : collect();
@endphp
<div class="form-group">
    <label class="form-label">
        Stock by Size
        <a href="{{ route('admin.sizes.index') }}" target="_blank" style="font-weight: 400; font-size: 0.8rem;">Manage sizes</a>
    </label>
    <small style="display: block; color: var(--text-muted); margin-bottom: 0.5rem;">
        Used only if this product has no colors below. Once you add colors, stock is tracked per color instead.
    </small>
    <div class="form-row">
        @foreach ($sizeNames as $sizeOption)
            <div class="form-group">
                <label for="sizes_{{ $sizeOption }}" class="form-label">{{ $sizeOption }}</label>
                <input id="sizes_{{ $sizeOption }}" type="number" name="sizes[{{ $sizeOption }}]"
                    value="{{ old('sizes.'.$sizeOption, $colorlessSizes->firstWhere('size', $sizeOption)->stock ?? 0) }}"
                    min="0" required class="form-control">
                @error('sizes.'.$sizeOption) <span class="field-error">{{ $message }}</span> @enderror
            </div>
        @endforeach
    </div>
</div>

@php
    $paletteColors = \App\Models\Color::ordered()->get();

    if (old('colors')) {
        $colorRows = collect(old('colors'))->values();
    } else {
        $colorRows = isset($product)
            ? $product->colors->map(fn ($color) => [
                'id' => $color->id,
                'color_id' => $color->color_id,
                'images' => $color->images->map(fn ($img) => ['id' => $img->id, 'path' => $img->image]),
                'sizes' => collect($sizeNames)->mapWithKeys(
                    fn ($s) => [$s => $color->sizes->firstWhere('size', $s)->stock ?? 0]
                ),
            ])
            : collect();
    }
@endphp

<div class="form-group">
    <label class="form-label">
        Colors (optional)
        <a href="{{ route('admin.colors.index') }}" target="_blank" style="font-weight: 400; font-size: 0.8rem;">Manage colors</a>
    </label>
    <small style="display: block; color: var(--text-muted); margin-bottom: 0.5rem;">
        Pick a color from the palette to give it its own photo gallery and its own stock by size. Leave empty for a single-color product.
    </small>

    <div id="color-rows">
        @foreach ($colorRows as $i => $color)
            <div class="color-row" data-color-row>
                <input type="hidden" name="colors[{{ $i }}][id]" value="{{ $color['id'] ?? '' }}">
                <div class="color-row-main">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Color</label>
                            <select name="colors[{{ $i }}][color_id]" class="form-control color-row-select">
                                <option value="">Select a color</option>
                                @foreach ($paletteColors as $paletteColor)
                                    <option value="{{ $paletteColor->id }}" data-hex="{{ $paletteColor->hex }}" @selected((string) ($color['color_id'] ?? '') === (string) $paletteColor->id)>{{ $paletteColor->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group" style="max-width: 60px;">
                            <label class="form-label">&nbsp;</label>
                            <span class="color-row-swatch-preview"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Photos</label>
                        <input type="file" name="colors[{{ $i }}][images][]" accept="image/*" multiple class="form-control color-images-input">
                        <small style="color: var(--text-muted);">You can select multiple images</small>
                        <div class="gallery-grid color-existing-images">
                            @foreach ($color['images'] ?? [] as $img)
                                <div class="gallery-item">
                                    <img src="{{ asset('uploads/'.$img['path']) }}" alt="">
                                    <button type="button" class="gallery-item-remove"
                                            data-url="{{ route('admin.products.color-images.destroy', [$product, $img['id']]) }}">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="M6 6l12 12"/></svg>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                        <div class="gallery-grid color-new-images-preview"></div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Stock by Size</label>
                        <div class="form-row">
                            @foreach ($sizeNames as $sizeOption)
                                <div class="form-group">
                                    <label class="form-label">{{ $sizeOption }}</label>
                                    <input type="number" name="colors[{{ $i }}][sizes][{{ $sizeOption }}]" value="{{ $color['sizes'][$sizeOption] ?? 0 }}" min="0" class="form-control">
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <button type="button" class="btn-icon danger color-row-remove" data-action="remove-color-row" title="Remove color">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="M6 6l12 12"/></svg>
                </button>
            </div>
        @endforeach
    </div>

    <button type="button" class="btn btn-secondary" id="add-color-row">+ Add Color</button>

    <template id="color-row-template">
        <div class="color-row" data-color-row>
            <input type="hidden" name="colors[__INDEX__][id]" value="">
            <div class="color-row-main">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Color</label>
                        <select name="colors[__INDEX__][color_id]" class="form-control color-row-select">
                            <option value="">Select a color</option>
                            @foreach ($paletteColors as $paletteColor)
                                <option value="{{ $paletteColor->id }}" data-hex="{{ $paletteColor->hex }}">{{ $paletteColor->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" style="max-width: 60px;">
                        <label class="form-label">&nbsp;</label>
                        <span class="color-row-swatch-preview"></span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Photos</label>
                    <input type="file" name="colors[__INDEX__][images][]" accept="image/*" multiple class="form-control color-images-input">
                    <small style="color: var(--text-muted);">You can select multiple images</small>
                    <div class="gallery-grid color-existing-images"></div>
                    <div class="gallery-grid color-new-images-preview"></div>
                </div>

                <div class="form-group">
                    <label class="form-label">Stock by Size</label>
                    <div class="form-row">
                        @foreach ($sizeNames as $sizeOption)
                            <div class="form-group">
                                <label class="form-label">{{ $sizeOption }}</label>
                                <input type="number" name="colors[__INDEX__][sizes][{{ $sizeOption }}]" value="0" min="0" class="form-control">
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <button type="button" class="btn-icon danger color-row-remove" data-action="remove-color-row" title="Remove color">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="M6 6l12 12"/></svg>
            </button>
        </div>
    </template>
</div>

<div class="form-group">
    <label for="priority" class="form-label">Priority</label>
    <input id="priority" type="number" name="priority" value="{{ old('priority', $product->priority ?? 0) }}" min="0" class="form-control">
    <small style="color: var(--text-muted);">Lower numbers appear first on the website.</small>
    @error('priority') <span class="field-error">{{ $message }}</span> @enderror
</div>

<div class="form-group">
    <label for="thumbnail" class="form-label">Thumbnail Image</label>
    <input id="thumbnail" type="file" name="thumbnail" accept="image/*" class="form-control">
    <small style="color: var(--text-muted);">PNG, JPG up to 2MB</small>
    @error('thumbnail') <span class="field-error">{{ $message }}</span> @enderror
    <img id="thumbnail-preview" src="{{ isset($product) && $product->thumbnail ? asset('uploads/'.$product->thumbnail) : '' }}"
         alt="" class="thumb-lg form-preview" style="{{ isset($product) && $product->thumbnail ? '' : 'display: none;' }}">
</div>

<div class="form-group">
    <label for="related_products" class="form-label">Related Products</label>
    <select id="related_products" name="related_products[]" multiple data-searchable-select>
        @php
            $selectedRelatedIds = collect(old('related_products', isset($product) ? $product->relatedProducts->pluck('id')->all() : []));
        @endphp
        @foreach ($allProducts as $productOption)
            @continue(isset($product) && $productOption->id === $product->id)
            <option value="{{ $productOption->id }}" @selected($selectedRelatedIds->contains((string) $productOption->id) || $selectedRelatedIds->contains($productOption->id))>
                {{ $productOption->name }}
            </option>
        @endforeach
    </select>
</div>

<div class="form-group">
    <label class="form-label">Specifications</label>

    @php
        if (old('specification_keys')) {
            $specRows = collect(old('specification_keys'))
                ->map(fn ($key, $i) => ['key' => $key, 'value' => old('specification_values')[$i] ?? '']);
        } else {
            $specRows = isset($product)
                ? $product->specifications->map(fn ($spec) => ['key' => $spec->key, 'value' => $spec->value])
                : collect();
        }
    @endphp

    <div id="specification-rows">
        @foreach ($specRows as $spec)
            <div class="spec-row">
                <input type="text" name="specification_keys[]" value="{{ $spec['key'] }}" placeholder="e.g. Material" class="form-control">
                <input type="text" name="specification_values[]" value="{{ $spec['value'] }}" placeholder="e.g. Cotton" class="form-control">
                <button type="button" class="btn-icon danger" data-action="remove-spec-row" title="Remove">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="M6 6l12 12"/></svg>
                </button>
            </div>
        @endforeach
    </div>

    <button type="button" class="btn btn-secondary" id="add-spec-row">+ Add Specification</button>

    <template id="spec-row-template">
        <div class="spec-row">
            <input type="text" name="specification_keys[]" placeholder="e.g. Material" class="form-control">
            <input type="text" name="specification_values[]" placeholder="e.g. Cotton" class="form-control">
            <button type="button" class="btn-icon danger" data-action="remove-spec-row" title="Remove">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="M6 6l12 12"/></svg>
            </button>
        </div>
    </template>
</div>

<div class="form-group">
    <label for="status" class="form-label">Status</label>
    <select id="status" name="status" class="form-control">
        <option value="1" @selected(old('status', $product->status ?? true) == '1')>Active</option>
        <option value="0" @selected(old('status', $product->status ?? true) == '0')>Inactive</option>
    </select>
</div>
