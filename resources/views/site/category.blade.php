<x-layouts.site :title="$category->name">
    <p class="breadcrumb"><a href="{{ route('home') }}">Home</a> / {{ $category->name }}</p>
    <h1 class="page-title" data-aos="fade-up">{{ $category->name }}</h1>

    @include('site.partials._listing', ['listingMode' => 'category'])
</x-layouts.site>
