@php
    $colors = $product->colors;

    if ($colors->isNotEmpty()) {
        $sizesByColor = $colors->mapWithKeys(fn ($color) => [
            (string) $color->id => collect(\App\Models\Size::names())->mapWithKeys(
                fn ($size) => [$size => $product->stockForSize($size, $color->id)]
            ),
        ]);
    } else {
        $sizesByColor = collect([
            '0' => collect(\App\Models\Size::names())->mapWithKeys(
                fn ($size) => [$size => $product->stockForSize($size)]
            ),
        ]);
    }
@endphp
<div class="size-selector" data-sizes-by-color='{{ $sizesByColor->toJson() }}' data-has-colors="{{ $colors->isNotEmpty() ? '1' : '0' }}">
    <p class="size-selector-label">Select Size</p>
    <div class="size-selector-options">
        @foreach (\App\Models\Size::names() as $sizeOption)
            <button type="button" class="size-option" data-size="{{ $sizeOption }}" {{ $colors->isNotEmpty() ? 'disabled' : ($sizesByColor['0'][$sizeOption] <= 0 ? 'disabled' : '') }}>
                {{ $sizeOption }}
            </button>
        @endforeach
    </div>
    <p class="size-selector-hint">Please select a size.</p>
</div>
