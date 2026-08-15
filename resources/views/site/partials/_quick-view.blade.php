@php
    [$priceWhole, $priceFrac] = explode('.', number_format($product->sale_price, 2, '.', ''));
    $ratingCount = $product->approved_reviews_count ?? $product->reviewsCount();
    $ratingAvg = $ratingCount > 0
        ? (float) ($product->approved_reviews_avg_rating ?? $product->averageRating())
        : 0.0;
@endphp
<button type="button" class="qv-close" data-action="close-quick-view" aria-label="Close">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
</button>

<div class="quick-view-layout">
    <div class="quick-view-image-wrap">
        @if ($product->thumbnail)
            <img src="{{ asset('uploads/'.$product->thumbnail) }}" alt="{{ $product->name }}" class="quick-view-image">
        @else
            <div class="quick-view-image"></div>
        @endif
    </div>

    <div class="quick-view-info">
        @if ($product->relationLoaded('category') && $product->category)
            <p class="product-card-category">{{ $product->category->name }}</p>
        @endif
        <h2 class="qv-title">{{ $product->name }}</h2>

        @if ($ratingCount > 0)
            @include('site.partials._stars', ['rating' => $ratingAvg, 'count' => $ratingCount])
        @endif

        <div class="pc-price">
            <span class="price price-lg">
                <span class="price-cur">₹</span><span class="price-whole">{{ number_format((int) $priceWhole) }}</span><span class="price-frac">{{ $priceFrac }}</span>
            </span>
            @if ($product->discountPercentage() > 0)
                <span class="price-off">{{ $product->discountPercentage() }}% off</span>
            @endif
        </div>

        @if ($product->stock > 0)
            <div class="product-detail-purchase" data-product-id="{{ $product->id }}">
                @include('site.partials._color-selector', ['product' => $product])
                @include('site.partials._size-selector', ['product' => $product])

                <p class="buybox-stock" data-role="stock-text">Select a size to check availability</p>

                <div class="qty-stepper">
                    <button type="button" class="qty-btn" data-action="qty-decrease" aria-label="Decrease quantity">&minus;</button>
                    <input type="text" class="qty-input" value="1" inputmode="numeric" aria-label="Quantity">
                    <button type="button" class="qty-btn" data-action="qty-increase" aria-label="Increase quantity">+</button>
                </div>

                <button type="button" class="btn btn-primary btn-lg btn-pill add-to-cart-btn" data-product-name="{{ $product->name }}" disabled>
                    Add to Cart
                </button>
            </div>
        @else
            <p class="buybox-stock out-of-stock">Currently unavailable</p>
        @endif

        <a href="{{ route('product.show', $product) }}" class="quick-view-details-link">View full details</a>
    </div>
</div>
