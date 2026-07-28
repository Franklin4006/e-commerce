<x-layouts.site title="My Wishlist">
    <h1 class="page-title" data-aos="fade-up">My Wishlist</h1>

    <div id="wishlist-items">
        @include('site.partials._wishlist-items')
    </div>

    <script src="{{ asset('js/cart.js') }}"></script>
    <script src="{{ asset('js/wishlist.js') }}"></script>
</x-layouts.site>
