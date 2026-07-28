<x-layouts.site title="Search">
    <p class="breadcrumb"><a href="{{ route('home') }}">Home</a> / Search</p>

    @if ($query !== '')
        <h1 class="page-title" data-aos="fade-up">Search results for &ldquo;{{ $query }}&rdquo;</h1>
    @else
        <h1 class="page-title" data-aos="fade-up">Search</h1>
    @endif

    @if ($products->isNotEmpty())
        <div class="product-grid">
            @foreach ($products as $product)
                @include('site.partials._product-card', ['aosDelay' => $loop->index % 4 * 70])
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <span class="empty-state-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            </span>
            @if ($query !== '')
                <p class="empty-state-title">No results found</p>
                <p class="empty-state-text">We couldn't find anything for &ldquo;{{ $query }}&rdquo;. Try a different search term.</p>
            @else
                <p class="empty-state-title">Search for products</p>
                <p class="empty-state-text">Enter a search term above to find products.</p>
            @endif
            <a href="{{ route('shop.index') }}" class="btn btn-primary">Browse Products</a>
        </div>
    @endif

    @if ($products->hasPages())
        <div style="margin-top: 1.5rem;">
            {{ $products->links() }}
        </div>
    @endif

    <script src="{{ asset('js/cart.js') }}"></script>
    <script src="{{ asset('js/wishlist.js') }}"></script>
</x-layouts.site>
