@php
    // Shared product-listing layout: left filter rail + sort toolbar + grid.
    // Expects: $products, $categories, $activeCategory, $min, $max, $sizes, $sort, $filterAction
    // and $listingMode ('shop' | 'category').
    $isCategoryMode = ($listingMode ?? 'shop') === 'category';
    $sizes = $sizes ?? [];
    $hasFilters = $activeCategory || $min !== null || $max !== null || ! empty($sizes) || ($sort && $sort !== 'featured');
@endphp

<form method="GET" action="{{ $filterAction }}" class="shop-layout">
    <aside class="shop-rail">
        <div class="shop-rail-group">
            <h3 class="shop-rail-title">Department</h3>
            <ul class="shop-facets">
                @if ($isCategoryMode)
                    <li><a class="shop-facet-link" href="{{ route('shop.index') }}">All Categories</a></li>
                    @foreach ($categories as $c)
                        <li><a class="shop-facet-link {{ $activeCategory == $c->id ? 'active' : '' }}" href="{{ route('category.show', $c) }}">{{ $c->name }}</a></li>
                    @endforeach
                @else
                    <li>
                        <label class="shop-facet">
                            <input type="radio" name="category" value="" @checked(! $activeCategory)>
                            <span>All Categories</span>
                        </label>
                    </li>
                    @foreach ($categories as $c)
                        <li>
                            <label class="shop-facet">
                                <input type="radio" name="category" value="{{ $c->id }}" @checked($activeCategory == $c->id)>
                                <span>{{ $c->name }}</span>
                            </label>
                        </li>
                    @endforeach
                @endif
            </ul>
        </div>

        <div class="shop-rail-group">
            <h3 class="shop-rail-title">Price (₹)</h3>
            <div class="shop-price">
                <input type="number" name="min" value="{{ $min }}" placeholder="Min" min="0" class="form-control" aria-label="Minimum price">
                <span class="shop-price-sep">&ndash;</span>
                <input type="number" name="max" value="{{ $max }}" placeholder="Max" min="0" class="form-control" aria-label="Maximum price">
            </div>
        </div>

        <div class="shop-rail-group">
            <h3 class="shop-rail-title">Size</h3>
            <div class="shop-size-options">
                @foreach (\App\Models\Size::names() as $sizeOption)
                    <label class="shop-size-option">
                        <input type="checkbox" name="sizes[]" value="{{ $sizeOption }}" @checked(in_array($sizeOption, $sizes))>
                        <span>{{ $sizeOption }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="shop-rail-actions">
            <button type="submit" class="btn btn-primary btn-sm">Apply</button>
            @if ($hasFilters)
                <a href="{{ $filterAction }}" class="shop-clear">Clear all</a>
            @endif
        </div>
    </aside>

    <div class="shop-main">
        <div class="shop-toolbar">
            <span class="shop-count">
                <strong>{{ $products->total() }}</strong> {{ Str::plural('result', $products->total()) }}
            </span>
            <label class="shop-sort">
                <span>Sort by</span>
                <select name="sort" class="form-control" onchange="this.form.submit()">
                    @foreach (\App\Support\ProductSorter::OPTIONS as $val => $label)
                        <option value="{{ $val }}" @selected($sort == $val)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>
        </div>

        @if ($products->isEmpty())
            <div class="empty-state">
                <p class="empty-state-title">No products found</p>
                <p class="empty-state-text">Try adjusting your filters or search.</p>
            </div>
        @else
            <div class="product-grid">
                @foreach ($products as $product)
                    @include('site.partials._product-card', ['aosDelay' => $loop->index % 4 * 70, 'shortPrice' => true])
                @endforeach
            </div>

            <div class="shop-pagination">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</form>
