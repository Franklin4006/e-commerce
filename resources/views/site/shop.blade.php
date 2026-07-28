<x-layouts.site title="Shop">
    <p class="breadcrumb"><a href="{{ route('home') }}">Home</a> / Shop</p>
    <h1 class="page-title" data-aos="fade-up">Shop</h1>

    @include('site.partials._listing', ['listingMode' => 'shop'])

    <script src="{{ asset('js/cart.js') }}"></script>
    <script src="{{ asset('js/wishlist.js') }}"></script>
</x-layouts.site>
