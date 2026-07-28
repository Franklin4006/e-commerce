<x-layouts.site title="FAQs">
    <p class="breadcrumb"><a href="{{ route('home') }}">Home</a> / FAQs</p>
    <h1 class="page-title" data-aos="fade-up">Frequently Asked Questions</h1>

    <div class="faq-list">
        @forelse ($faqs as $faq)
            <details class="faq-item" data-aos="fade-up" data-aos-delay="{{ $loop->index % 4 * 70 }}">
                <summary class="faq-question">{{ $faq->question }}</summary>
                <div class="faq-answer">{!! $faq->answer !!}</div>
            </details>
        @empty
            <p>No FAQs available yet.</p>
        @endforelse
    </div>
</x-layouts.site>
