<x-layouts.site :title="$title">
    <p class="breadcrumb"><a href="{{ route('home') }}">Home</a> / {{ $title }}</p>

    <div class="section-header-desc" style="margin-bottom: 2rem;" data-aos="fade-up">
        <span class="section-eyebrow">Our Story</span>
        <h1 class="page-title page-title-center">{{ $title }}</h1>
    </div>

    <div class="card" style="max-width: 800px; margin: 0 auto 2.5rem; padding: 2rem;" data-aos="fade-up">
        <div class="rich-content">{!! $content !!}</div>
    </div>

    <section class="home-panel" data-aos="fade-up">
        <div class="section-header-desc" style="margin-bottom: 1.5rem;">
            <span class="section-eyebrow">Why Shop With Us</span>
            <h2 class="section-title" style="font-size: 1.4rem;">Made For Every Body, Delivered With Care</h2>
        </div>

        <section class="trust-bar" style="margin-bottom: 0;">
            <div class="trust-item" data-aos="fade-up">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                <div>
                    <p class="trust-item-title">Pan-India Delivery</p>
                    <p class="trust-item-text">Quick order dispatch</p>
                </div>
            </div>
            <div class="trust-item" data-aos="fade-up" data-aos-delay="70">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-6.219-8.56"/><path d="M12 3v9l4 2"/></svg>
                <div>
                    <p class="trust-item-title">7-Day Easy Exchange</p>
                    <p class="trust-item-text">Wrong size? Swap it hassle-free</p>
                </div>
            </div>
            <div class="trust-item" data-aos="fade-up" data-aos-delay="140">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                <div>
                    <p class="trust-item-title">Secure Payment</p>
                    <p class="trust-item-text">100% protected checkout</p>
                </div>
            </div>
            <div class="trust-item" data-aos="fade-up" data-aos-delay="210">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                <div>
                    <p class="trust-item-title">Styling Support</p>
                    <p class="trust-item-text">We're here to help you shop</p>
                </div>
            </div>
        </section>
    </section>
</x-layouts.site>
