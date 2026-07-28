<x-layouts.site :title="$title">
    <p class="breadcrumb"><a href="{{ route('home') }}">Home</a> / {{ $title }}</p>
    <h1 class="page-title" data-aos="fade-up">{{ $title }}</h1>

    <div class="card" style="max-width: 800px; margin: 0 auto;" data-aos="fade-up">
        {!! $content !!}
    </div>
</x-layouts.site>
