<x-layouts.site :title="$product->name">
    <p class="breadcrumb">
        <a href="{{ route('home') }}">Home</a> /
        <a href="{{ route('category.show', $product->category) }}">{{ $product->category->name }}</a> /
        {{ $product->name }}
    </p>

    @php
        $ratingCount = $product->reviewsCount();
        $ratingAvg = $ratingCount > 0 ? $product->averageRating() : 0;
        $avgRoundedTop = round($ratingAvg);
        [$priceWhole, $priceFrac] = explode('.', number_format($product->sale_price, 2, '.', ''));
        $deliveryDate = now()->addDays(4);
        $inWishlist = \App\Support\Wishlist::has($product->id);
        $shareUrl = route('product.show', $product);
        $shareText = $product->name;
    @endphp

    <div class="pdp">
        {{-- Gallery: shows the product thumbnail by default; once a color is
             picked below, the whole section re-renders to that color's own
             photo gallery (see cart.js). --}}
        <div class="pdp-gallery">
            @if ($product->thumbnail)
                <div class="pdp-thumbs" id="pdp-thumbs">
                    <img src="{{ asset('storage/' . $product->thumbnail) }}" alt="{{ $product->name }}"
                        class="pdp-thumb active" onmouseover="document.getElementById('main-image').src = this.src"
                        onclick="document.getElementById('main-image').src = this.src">
                </div>
            @else
                <div class="pdp-thumbs" id="pdp-thumbs"></div>
            @endif

            <div class="pdp-main">
                <button type="button" class="wishlist-btn {{ $inWishlist ? 'active' : '' }}"
                        data-action="toggle-wishlist" data-product-id="{{ $product->id }}"
                        aria-pressed="{{ $inWishlist ? 'true' : 'false' }}"
                        aria-label="{{ $inWishlist ? 'Remove from wishlist' : 'Add to wishlist' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                </button>

                @if ($product->thumbnail)
                    <img src="{{ asset('storage/' . $product->thumbnail) }}" alt="{{ $product->name }}"
                        class="pdp-main-image" id="main-image">
                @else
                    <div class="pdp-main-image"></div>
                @endif
            </div>
        </div>

        {{-- Info + purchase: one flowing column, sections separated by thin
             rules — no separate "buy box" card. --}}
        <div class="pdp-info">
            <a href="{{ route('category.show', $product->category) }}" class="pdp-category">{{ $product->category->name }}</a>
            <h1 class="pdp-title">{{ $product->name }}</h1>

            @if ($ratingCount > 0)
                <a href="#reviews" class="pdp-rating">
                    <span class="rating-stars">
                        @for ($i = 1; $i <= 5; $i++)
                            <span class="{{ $i <= $avgRoundedTop ? 'star-filled' : 'star-empty' }}">&#9733;</span>
                        @endfor
                    </span>
                    <span class="pdp-rating-count">{{ number_format($ratingCount) }} {{ Str::plural('review', $ratingCount) }}</span>
                </a>
            @endif

            <hr class="pdp-divider">

            <div class="pdp-price-block">
                <div class="pdp-price">
                    @if ($product->discountPercentage() > 0)
                        <span class="pdp-mrp-inline">₹{{ number_format($product->mrp, 2) }}</span>
                    @endif
                    <span class="price price-lg">
                        <span class="price-cur">₹</span><span class="price-whole">{{ number_format((int) $priceWhole) }}</span><span class="price-frac">{{ $priceFrac }}</span>
                    </span>
                    @if ($product->discountPercentage() > 0)
                        <span class="pdp-price-off">{{ $product->discountPercentage() }}% off</span>
                    @endif
                </div>
                <p class="pdp-tax-note">Inclusive of all taxes</p>
            </div>

            @if ($product->specifications->isNotEmpty())
                <ul class="pdp-highlights">
                    @foreach ($product->specifications->take(4) as $spec)
                        <li><span class="pdp-highlights-key">{{ $spec->key }}</span> {{ $spec->value }}</li>
                    @endforeach
                </ul>
            @endif

            <div id="buybox" class="buybox {{ $product->stock > 0 ? 'product-detail-purchase' : '' }}"
                @if ($product->stock > 0) data-product-id="{{ $product->id }}" @endif>

                @if ($product->stock > 0)
                    <hr class="pdp-divider">
                    @include('site.partials._color-selector', ['product' => $product])

                    <hr class="pdp-divider">
                    @include('site.partials._size-selector', ['product' => $product])

                    <p class="buybox-stock" data-role="stock-text">Select a size to check availability</p>

                    <hr class="pdp-divider">

                    <div class="buybox-purchase-row">
                        <div class="qty-stepper">
                            <button type="button" class="qty-btn" data-action="qty-decrease" aria-label="Decrease quantity">&minus;</button>
                            <input type="text" class="qty-input" value="1" inputmode="numeric" aria-label="Quantity">
                            <button type="button" class="qty-btn" data-action="qty-increase" aria-label="Increase quantity">+</button>
                        </div>
                    </div>

                    <button type="button" class="btn btn-primary btn-lg add-to-cart-btn" data-product-name="{{ $product->name }}" disabled>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                        Add to Cart
                    </button>
                    <button type="button" class="btn btn-outline btn-lg buy-now-btn" data-product-name="{{ $product->name }}" disabled>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                        Buy Now
                    </button>
                @else
                    <hr class="pdp-divider">
                    <p class="buybox-stock out-of-stock">Currently unavailable</p>
                @endif
            </div>

            <hr class="pdp-divider">

            <div class="buybox-delivery">
                <p><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                    <span><strong>FREE delivery</strong> {{ $deliveryDate->format('l, d M') }}</span></p>
                <p><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
                    <span>Easy 7-day returns &amp; exchange</span></p>
                <p><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    <span>Secure transaction</span></p>
            </div>

            @if ($activeCoupons->isNotEmpty())
                <div class="buybox-offers">
                    <p class="buybox-offers-title">Available Offers</p>
                    <ul class="buybox-offers-list">
                        @foreach ($activeCoupons as $coupon)
                            <li>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41 11 22 2 13l8.59-8.59A2 2 0 0 1 12 4h8a2 2 0 0 1 2 2v8a2 2 0 0 1-.41 1.41z"/><circle cx="7.5" cy="7.5" r="1"/></svg>
                                <span>
                                    <strong>{{ $coupon->code }}</strong>
                                    &mdash;
                                    {{ $coupon->type === 'percentage' ? number_format((float) $coupon->value, 0).'% off' : '₹'.number_format((float) $coupon->value, 0).' off' }}
                                    @if ($coupon->min_order_amount) on orders above ₹{{ number_format((float) $coupon->min_order_amount, 0) }} @endif
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <hr class="pdp-divider">

            <div class="pdp-share">
                <span class="pdp-share-label">Share:</span>
                <a href="mailto:?subject={{ urlencode($shareText) }}&body={{ urlencode($shareUrl) }}" aria-label="Share by email" title="Share by email">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16v16H4z" opacity="0"/><path d="M22 6 12 13 2 6"/><rect x="2" y="4" width="20" height="16" rx="2"/></svg>
                </a>
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($shareUrl) }}" target="_blank" rel="noopener" aria-label="Share on Facebook" title="Share on Facebook">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M22 12a10 10 0 1 0-11.56 9.88v-6.99H7.9V12h2.54V9.8c0-2.5 1.49-3.89 3.77-3.89 1.09 0 2.24.2 2.24.2v2.46h-1.26c-1.24 0-1.63.77-1.63 1.56V12h2.77l-.44 2.89h-2.33v6.99A10 10 0 0 0 22 12z"/></svg>
                </a>
                <a href="https://twitter.com/intent/tweet?url={{ urlencode($shareUrl) }}&text={{ urlencode($shareText) }}" target="_blank" rel="noopener" aria-label="Share on Twitter" title="Share on Twitter">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M22 5.9c-.77.35-1.6.58-2.46.68a4.3 4.3 0 0 0 1.88-2.37 8.6 8.6 0 0 1-2.72 1.04 4.28 4.28 0 0 0-7.29 3.9A12.14 12.14 0 0 1 2.9 4.9a4.28 4.28 0 0 0 1.32 5.71c-.7-.02-1.36-.22-1.94-.53v.05a4.28 4.28 0 0 0 3.43 4.2c-.65.18-1.34.2-1.99.08a4.29 4.29 0 0 0 4 2.98A8.6 8.6 0 0 1 1 19.08a12.1 12.1 0 0 0 6.56 1.92c7.88 0 12.2-6.53 12.2-12.2 0-.19 0-.37-.01-.56A8.7 8.7 0 0 0 22 5.9z"/></svg>
                </a>
                <a href="https://pinterest.com/pin/create/button/?url={{ urlencode($shareUrl) }}&media={{ urlencode($product->thumbnail ? asset('storage/'.$product->thumbnail) : '') }}&description={{ urlencode($shareText) }}" target="_blank" rel="noopener" aria-label="Share on Pinterest" title="Share on Pinterest">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12c0 4.24 2.63 7.86 6.35 9.33-.09-.79-.17-2.01.03-2.88.18-.78 1.17-4.97 1.17-4.97s-.3-.6-.3-1.48c0-1.39.8-2.43 1.81-2.43.85 0 1.26.64 1.26 1.4 0 .85-.55 2.13-.83 3.31-.24 1 .5 1.81 1.48 1.81 1.78 0 3.15-1.88 3.15-4.58 0-2.4-1.72-4.07-4.18-4.07-2.85 0-4.52 2.14-4.52 4.35 0 .86.33 1.79.75 2.29a.3.3 0 0 1 .07.29c-.08.32-.25 1-.28 1.15-.05.19-.15.24-.35.14-1.3-.6-2.11-2.5-2.11-4.02 0-3.27 2.38-6.28 6.85-6.28 3.6 0 6.4 2.56 6.4 5.99 0 3.57-2.25 6.45-5.38 6.45-1.05 0-2.04-.55-2.38-1.19l-.65 2.46c-.24.9-.87 2.04-1.3 2.73.98.3 2.02.47 3.1.47 5.52 0 10-4.48 10-10S17.52 2 12 2z"/></svg>
                </a>
            </div>
        </div>
    </div>

    @php
        $hasDescription = (bool) $product->description;
        $hasSpecs = $product->specifications->isNotEmpty();
    @endphp

    @if ($hasDescription || $hasSpecs)
        <section class="card card-flush pdp-panel">
            @if ($hasDescription && $hasSpecs)
                <div class="pdp-tabs" data-role="pdp-tabs">
                    <button type="button" class="pdp-tab active" data-tab="description">Description</button>
                    <button type="button" class="pdp-tab" data-tab="specifications">Specifications</button>
                </div>
            @else
                <div class="card-header"><h2 class="card-title">{{ $hasDescription ? 'Description' : 'Specifications' }}</h2></div>
            @endif

            <div class="card-body">
                @if ($hasDescription)
                    <div class="pdp-tab-panel {{ $hasSpecs ? '' : 'active' }}" data-panel="description" style="{{ $hasSpecs ? 'display:none;' : '' }}">
                        <div class="pdp-description rich-content">{!! $product->description !!}</div>
                    </div>
                @endif

                @if ($hasSpecs)
                    <div class="pdp-tab-panel {{ $hasDescription ? '' : 'active' }}" data-panel="specifications" style="{{ $hasDescription ? 'display:none;' : '' }}">
                        <div class="table-wrap" style="box-shadow: none;">
                            <table class="spec-table">
                                <tbody>
                                    @foreach ($product->specifications as $spec)
                                        <tr>
                                            <th>{{ $spec->key }}</th>
                                            <td>{{ $spec->value }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        </section>
    @endif

    <section class="card card-flush pdp-panel" id="reviews">
        <div class="card-header"><h2 class="card-title">Ratings &amp; Reviews</h2></div>
        <div class="card-body">
        @if ($ratingCount > 0)
            @php $avgRounded = round($ratingAvg); @endphp
            <div class="review-summary">
                <div class="review-summary-score">
                    <span class="review-summary-number">{{ number_format($ratingAvg, 1) }}</span>
                    <span class="rating-stars rating-stars-lg">
                        @for ($i = 1; $i <= 5; $i++)
                            <span class="{{ $i <= $avgRounded ? 'star-filled' : 'star-empty' }}">&#9733;</span>
                        @endfor
                    </span>
                    <span class="review-summary-count">{{ $ratingCount }} {{ Str::plural('review', $ratingCount) }}</span>
                </div>

                <div class="review-summary-bars">
                    @for ($star = 5; $star >= 1; $star--)
                        @php $starCount = $product->approvedReviews->where('rating', $star)->count(); @endphp
                        <div class="review-summary-bar-row">
                            <span class="review-summary-bar-label">{{ $star }}&#9733;</span>
                            <span class="review-summary-bar-track">
                                <span class="review-summary-bar-fill" style="width: {{ $ratingCount ? round(($starCount / $ratingCount) * 100) : 0 }}%"></span>
                            </span>
                            <span class="review-summary-bar-count">{{ $starCount }}</span>
                        </div>
                    @endfor
                </div>
            </div>
        @endif

        @if ($product->approvedReviews->isNotEmpty())
            <div class="review-list">
                @foreach ($product->approvedReviews as $review)
                    <div class="review-item">
                        <div class="review-item-avatar">{{ Str::of($review->user->name)->substr(0, 1)->upper() }}</div>
                        <div class="review-item-body">
                            <div class="review-item-header">
                                <strong>{{ $review->user->name }}</strong>
                                <span class="review-item-date">{{ $review->created_at->format('d M Y') }}</span>
                            </div>
                            <span class="rating-stars">
                                @for ($i = 1; $i <= 5; $i++)
                                    <span class="{{ $i <= $review->rating ? 'star-filled' : 'star-empty' }}">&#9733;</span>
                                @endfor
                            </span>
                            <p class="review-item-comment">{{ $review->comment }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="muted">No reviews yet. Be the first to share your thoughts!</p>
        @endif

        @if (session('status'))
            <div class="alert alert-success"><p>{{ session('status') }}</p></div>
        @endif

        @if ($errors->any())
            <div class="alert alert-error">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        @auth
            @if ($canReview)
                <div class="review-form-box">
                    <h3 class="review-form-title">Write a Review</h3>
                    <form method="POST" action="{{ route('reviews.store', $product) }}" class="review-form">
                        @csrf
                        <div class="form-group">
                            <label class="form-label">Your Rating</label>
                            <div class="star-rating-input">
                                <input type="radio" id="star5" name="rating" value="5" required {{ old('rating') == 5 ? 'checked' : '' }}>
                                <label for="star5" title="5 stars">&#9733;</label>
                                <input type="radio" id="star4" name="rating" value="4" {{ old('rating') == 4 ? 'checked' : '' }}>
                                <label for="star4" title="4 stars">&#9733;</label>
                                <input type="radio" id="star3" name="rating" value="3" {{ old('rating') == 3 ? 'checked' : '' }}>
                                <label for="star3" title="3 stars">&#9733;</label>
                                <input type="radio" id="star2" name="rating" value="2" {{ old('rating') == 2 ? 'checked' : '' }}>
                                <label for="star2" title="2 stars">&#9733;</label>
                                <input type="radio" id="star1" name="rating" value="1" {{ old('rating') == 1 ? 'checked' : '' }}>
                                <label for="star1" title="1 star">&#9733;</label>
                            </div>
                            @error('rating')<span class="field-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group">
                            <label for="comment" class="form-label">Your Review</label>
                            <textarea id="comment" name="comment" rows="4" class="form-control" required placeholder="Share your experience with this product&hellip;">{{ old('comment') }}</textarea>
                            @error('comment')<span class="field-error">{{ $message }}</span>@enderror
                        </div>

                        <button type="submit" class="btn btn-primary">Submit Review</button>
                    </form>
                </div>
            @elseif ($alreadyReviewed)
                <p class="muted">You've already reviewed this product. Thank you!</p>
            @else
                <p class="muted">Only customers who have received this product can leave a review.</p>
            @endif
        @else
            <p class="muted"><a href="{{ route('login') }}">Log in</a> to write a review.</p>
        @endauth
        </div>
    </section>

    @if ($relatedProducts->isNotEmpty())
        <section class="related-products-section">
            <h2 class="page-title" data-aos="fade-up">Similar Items</h2>
            <div class="product-grid">
                @foreach ($relatedProducts as $relatedProduct)
                    @include('site.partials._product-card', ['product' => $relatedProduct, 'aosDelay' => $loop->index % 4 * 70])
                @endforeach
            </div>
        </section>
    @endif

    <script src="{{ asset('js/cart.js') }}"></script>
    <script src="{{ asset('js/wishlist.js') }}"></script>
    <script src="{{ asset('js/pdp-zoom.js') }}"></script>
    <script src="{{ asset('js/pdp-tabs.js') }}"></script>
</x-layouts.site>
