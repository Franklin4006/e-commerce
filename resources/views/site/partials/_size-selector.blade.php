@php
    $colors = $product->colors;

    if ($colors->isNotEmpty()) {
        $sizesByColor = $colors->mapWithKeys(fn ($color) => [
            (string) $color->id => collect(\App\Models\Size::names())->mapWithKeys(
                fn ($size) => [$size => $product->stockForSize($size, $color->id)]
            ),
        ]);

        // A size is worth rendering as long as it's in stock for at least one
        // color; the JS hides/shows individual buttons as the shopper picks
        // a color, since availability varies per color.
        $availableSizes = collect(\App\Models\Size::names())
            ->filter(fn ($size) => $sizesByColor->contains(fn ($stocks) => $stocks[$size] > 0))
            ->values();
    } else {
        $sizesByColor = collect([
            '0' => collect(\App\Models\Size::names())->mapWithKeys(
                fn ($size) => [$size => $product->stockForSize($size)]
            ),
        ]);

        $availableSizes = collect(\App\Models\Size::names())
            ->filter(fn ($size) => $sizesByColor['0'][$size] > 0)
            ->values();
    }
@endphp
<div class="size-selector" data-sizes-by-color='{{ $sizesByColor->toJson() }}' data-has-colors="{{ $colors->isNotEmpty() ? '1' : '0' }}">
    <p class="size-selector-label">Select Size</p>
    <div class="size-selector-options">
        @foreach ($availableSizes as $sizeOption)
            <button type="button" class="size-option" data-size="{{ $sizeOption }}" {{ $colors->isNotEmpty() ? 'disabled' : '' }}>
                {{ $sizeOption }}
            </button>
        @endforeach
    </div>
    <p class="size-selector-hint">Please select a size.</p>
    <p class="size-selector-empty out-of-stock" data-role="size-empty" style="display: none;">Out of stock</p>
</div>
