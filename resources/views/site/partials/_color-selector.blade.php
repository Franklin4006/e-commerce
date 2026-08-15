@if ($product->colors->isNotEmpty())
    <div class="color-selector" data-role="color-selector">
        <p class="size-selector-label">
            Color<span class="color-selector-selected-name" data-role="selected-color-name"></span>
        </p>
        <div class="color-selector-options">
            @foreach ($product->colors as $color)
                @php
                    $galleryUrls = $color->images->map(fn ($img) => asset('uploads/'.$img->image));
                    $coverUrl = $galleryUrls->first();
                @endphp
                <button type="button" class="color-option" data-color-id="{{ $color->id }}" data-color-name="{{ $color->name }}"
                        data-image="{{ $coverUrl }}" data-images='{{ $galleryUrls->toJson() }}' title="{{ $color->name }}">
                    @if ($coverUrl)
                        <img src="{{ $coverUrl }}" alt="{{ $color->name }}">
                    @elseif ($color->hex)
                        <span class="color-option-swatch" style="background: {{ $color->hex }};"></span>
                    @else
                        <span class="color-option-swatch color-option-swatch-empty">{{ Str::substr($color->name, 0, 1) }}</span>
                    @endif
                </button>
            @endforeach
        </div>
    </div>
@endif
