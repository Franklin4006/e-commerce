<div class="product-card" @isset($aosDelay) data-aos="fade-up" data-aos-delay="{{ $aosDelay }}" @endisset>
    <div class="product-card-image-wrap">
        <a href="{{ route('product.show', $product) }}" class="product-card-link">
            @if ($product->thumbnail)
                <img src="{{ asset('uploads/'.$product->thumbnail) }}" alt="{{ $product->name }}" class="product-card-image">
            @else
                <div class="product-card-image"></div>
            @endif
        </a>

        @if ($product->stock <= 0)
            <span class="stock-badge out-of-stock">Out of Stock</span>
        @elseif ($product->discountPercentage() > 0)
            <span class="product-card-badge">{{ $product->discountPercentage() }}% OFF</span>
        @endif

        @php $inWishlist = \App\Support\Wishlist::has($product->id); @endphp
        <button type="button" class="wishlist-btn {{ $inWishlist ? 'active' : '' }}"
                data-action="toggle-wishlist" data-product-id="{{ $product->id }}"
                aria-pressed="{{ $inWishlist ? 'true' : 'false' }}"
                aria-label="{{ $inWishlist ? 'Remove from wishlist' : 'Add to wishlist' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
        </button>
    </div>

    <div class="product-card-body">
        <a href="{{ route('product.show', $product) }}" class="product-card-link">
            @if ($product->relationLoaded('category') && $product->category)
                <p class="product-card-category">{{ $product->category->name }}</p>
            @endif
            <p class="product-card-name">{{ $product->name }}</p>

            @php
                // Prefer eager-loaded aggregates when the controller provides them
                // (withCount/withAvg on approvedReviews); fall back to the model helpers.
                $ratingCount = $product->approved_reviews_count ?? $product->reviewsCount();
                $ratingAvg = $ratingCount > 0
                    ? (float) ($product->approved_reviews_avg_rating ?? $product->averageRating())
                    : 0.0;
            @endphp
            @if ($ratingCount > 0)
                @include('site.partials._stars', ['rating' => $ratingAvg, 'count' => $ratingCount])
            @endif
        </a>

        @php
            $shortPrice = $shortPrice ?? false;
            if (! $shortPrice) {
                [$priceWhole, $priceFrac] = explode('.', number_format($product->sale_price, 2, '.', ''));
            }
        @endphp
        <div class="product-card-footer">
            <div class="pc-price">
                <span class="price">
                    @if ($shortPrice)
                        <span class="price-cur">₹</span><span class="price-whole">{{ number_format($product->sale_price) }}</span>
                    @else
                        <span class="price-cur">₹</span><span class="price-whole">{{ number_format((int) $priceWhole) }}</span><span class="price-frac">{{ $priceFrac }}</span>
                    @endif
                </span>
                @if ($product->discountPercentage() > 0)
                    <span class="price-list"><span class="strike">₹{{ number_format($product->mrp, 2) }}</span></span>
                @endif
            </div>

            <div class="product-card-footer-actions">
                @if ($product->stock > 0)
                    <button type="button" class="quickview-btn" data-action="quick-view" data-product-slug="{{ $product->slug }}" aria-label="Quick view" title="Quick view">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
