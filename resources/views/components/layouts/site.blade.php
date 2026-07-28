@php
    $siteName = $siteSettings['site_name'] ?? config('app.name');
    $metaDescription = $siteSettings['meta_description'] ?? null;
    $metaKeywords = $siteSettings['meta_keywords'] ?? null;
    $primaryColor = $siteSettings['primary_color'] ?? '#be185d';
    $secondaryColor = $siteSettings['secondary_color'] ?? '#f6a623';
    $hasContactBar = ! empty($siteSettings['email']) || ! empty($siteSettings['phone']) || ! empty($siteSettings['address']);
    $hasFooterContact = ! empty($siteSettings['email']) || ! empty($siteSettings['phone']) || ! empty($siteSettings['address']);
    $whatsappNumber = ! empty($siteSettings['whatsapp_number']) ? preg_replace('/\D/', '', $siteSettings['whatsapp_number']) : null;
    $hasSocial = ! empty($siteSettings['social_facebook']) || ! empty($siteSettings['social_instagram']) || ! empty($siteSettings['social_twitter']) || ! empty($siteSettings['social_youtube']) || $whatsappNumber;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if ($metaDescription)
        <meta name="description" content="{{ $metaDescription }}">
    @endif
    @if ($metaKeywords)
        <meta name="keywords" content="{{ $metaKeywords }}">
    @endif
    <title>{{ isset($title) ? $title.' - '.$siteName : $siteName }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Onest:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/site-theme.css') }}">
    <link rel="stylesheet" href="{{ asset('css/aos.css') }}">
</head>
<body style="--site-primary: {{ $primaryColor }}; --site-secondary: {{ $secondaryColor }};">
    @if ($hasContactBar)
        <div class="site-topbar">
            <div class="site-topbar-inner">
                <div class="site-topbar-contact">
                    @if (! empty($siteSettings['phone']))
                        <span>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            {{ $siteSettings['phone'] }}
                        </span>
                    @endif
                    @if (! empty($siteSettings['email']))
                        <span>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16v16H4z" opacity="0"/><path d="M22 6 12 13 2 6"/><rect x="2" y="4" width="20" height="16" rx="2"/></svg>
                            {{ $siteSettings['email'] }}
                        </span>
                    @endif
                    @if (! empty($siteSettings['address']))
                        <span>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 6-9 12-9 12s-9-6-9-12a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            {{ $siteSettings['address'] }}
                        </span>
                    @endif
                </div>
                <div class="site-topbar-extra">
                    <span class="site-topbar-tag">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                        Pan-India Delivery
                    </span>
                    @if ($hasSocial)
                        <div class="site-topbar-social">
                            @if ($whatsappNumber)
                                <a href="https://wa.me/{{ $whatsappNumber }}" target="_blank" rel="noopener" aria-label="WhatsApp">
                                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.29-1.39a9.9 9.9 0 0 0 4.75 1.21h.01c5.46 0 9.9-4.45 9.9-9.91 0-2.65-1.03-5.14-2.9-7.01A9.86 9.86 0 0 0 12.04 2zm0 18.13h-.01a8.2 8.2 0 0 1-4.19-1.15l-.3-.18-3.14.82.84-3.06-.2-.32a8.2 8.2 0 0 1-1.26-4.37c0-4.54 3.7-8.24 8.26-8.24 2.2 0 4.27.86 5.83 2.42a8.18 8.18 0 0 1 2.41 5.83c0 4.55-3.7 8.25-8.24 8.25zm4.52-6.17c-.25-.12-1.47-.72-1.7-.81-.23-.08-.39-.12-.56.13-.17.25-.64.81-.78.97-.14.17-.29.19-.53.06-.25-.12-1.05-.39-2-1.23-.74-.66-1.24-1.48-1.39-1.73-.14-.25-.02-.38.11-.51.11-.11.25-.29.37-.43.12-.15.16-.25.25-.42.08-.17.04-.31-.02-.44-.06-.12-.56-1.35-.77-1.85-.2-.48-.4-.42-.56-.43-.14-.01-.31-.01-.48-.01-.16 0-.43.06-.66.31-.23.25-.86.85-.86 2.06 0 1.22.88 2.4 1.01 2.56.12.17 1.73 2.64 4.19 3.7.59.25 1.04.4 1.4.52.59.19 1.12.16 1.54.1.47-.07 1.47-.6 1.67-1.18.21-.58.21-1.07.14-1.18-.06-.1-.23-.16-.48-.29z"/></svg>
                                </a>
                            @endif
                            @if (! empty($siteSettings['social_facebook']))
                                <a href="{{ $siteSettings['social_facebook'] }}" target="_blank" rel="noopener" aria-label="Facebook">
                                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M22 12a10 10 0 1 0-11.56 9.88v-6.99H7.9V12h2.54V9.8c0-2.5 1.49-3.89 3.77-3.89 1.09 0 2.24.2 2.24.2v2.46h-1.26c-1.24 0-1.63.77-1.63 1.56V12h2.77l-.44 2.89h-2.33v6.99A10 10 0 0 0 22 12z"/></svg>
                                </a>
                            @endif
                            @if (! empty($siteSettings['social_instagram']))
                                <a href="{{ $siteSettings['social_instagram'] }}" target="_blank" rel="noopener" aria-label="Instagram">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1"/></svg>
                                </a>
                            @endif
                            @if (! empty($siteSettings['social_twitter']))
                                <a href="{{ $siteSettings['social_twitter'] }}" target="_blank" rel="noopener" aria-label="Twitter">
                                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M22 5.9c-.77.35-1.6.58-2.46.68a4.3 4.3 0 0 0 1.88-2.37 8.6 8.6 0 0 1-2.72 1.04 4.28 4.28 0 0 0-7.29 3.9A12.14 12.14 0 0 1 2.9 4.9a4.28 4.28 0 0 0 1.32 5.71c-.7-.02-1.36-.22-1.94-.53v.05a4.28 4.28 0 0 0 3.43 4.2c-.65.18-1.34.2-1.99.08a4.29 4.29 0 0 0 4 2.98A8.6 8.6 0 0 1 1 19.08a12.1 12.1 0 0 0 6.56 1.92c7.88 0 12.2-6.53 12.2-12.2 0-.19 0-.37-.01-.56A8.7 8.7 0 0 0 22 5.9z"/></svg>
                                </a>
                            @endif
                            @if (! empty($siteSettings['social_youtube']))
                                <a href="{{ $siteSettings['social_youtube'] }}" target="_blank" rel="noopener" aria-label="YouTube">
                                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M23 12s0-3.6-.46-5.32a3 3 0 0 0-2.1-2.13C18.7 4 12 4 12 4s-6.7 0-8.44.55a3 3 0 0 0-2.1 2.13C1 8.4 1 12 1 12s0 3.6.46 5.32a3 3 0 0 0 2.1 2.13C5.3 20 12 20 12 20s6.7 0 8.44-.55a3 3 0 0 0 2.1-2.13C23 15.6 23 12 23 12z"/><path d="M9.75 15.5v-7l6 3.5-6 3.5z" fill="#111827"/></svg>
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <header class="site-header">
        <div class="site-navbar-inner">
            <button type="button" class="mobile-menu-toggle" id="mobile-menu-toggle" aria-label="Menu" aria-expanded="false" aria-controls="site-nav">
                <svg class="icon-menu" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                <svg class="icon-close" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>

            <a href="{{ route('home') }}" class="site-brand">
                @if (! empty($siteSettings['logo']))
                    <img src="{{ asset('storage/'.$siteSettings['logo']) }}" alt="{{ $siteName }}" class="site-logo">
                @else
                    <span class="site-brand-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 7h-3V6a4 4 0 0 0-8 0v1H6a1 1 0 0 0-1 .93L4 21h16l-1-13.07A1 1 0 0 0 20 7zM10 6a2 2 0 0 1 4 0v1h-4z"/></svg>
                    </span>
                    {{ $siteName }}
                @endif
            </a>

            <nav class="site-nav" id="site-nav">
                <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
                <a href="{{ route('shop.index') }}" class="{{ request()->routeIs('shop.index') ? 'active' : '' }}">Shop</a>
                <a href="{{ route('about.index') }}" class="{{ request()->routeIs('about.index') ? 'active' : '' }}">About Us</a>
                <a href="{{ route('faqs.index') }}" class="{{ request()->routeIs('faqs.index') ? 'active' : '' }}">FAQs</a>
                <a href="{{ route('contact.index') }}" class="{{ request()->routeIs('contact.index') ? 'active' : '' }}">Contact Us</a>
            </nav>

            <div class="site-header-actions">
                <button type="button" class="nav-icon-btn" id="nav-search-toggle" aria-label="Search" aria-expanded="false" aria-controls="nav-search-panel">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                </button>

                <a href="{{ route('wishlist.index') }}" class="site-header-wishlist nav-icon-btn" aria-label="Wishlist">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                    <span class="site-icon-count wishlist-count-badge" id="wishlist-count" style="{{ $wishlistCount > 0 ? '' : 'display: none;' }}">{{ $wishlistCount }}</span>
                </a>

                <a href="{{ route('cart.index') }}" class="site-header-cart nav-icon-btn" aria-label="Cart">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                    <span class="site-icon-count" id="cart-count" style="{{ $cartCount > 0 ? '' : 'display: none;' }}">{{ $cartCount }}</span>
                </a>

                @auth
                    <div class="user-menu" id="user-menu">
                        <button type="button" class="user-menu-trigger nav-icon-btn" id="user-menu-trigger" aria-label="Account menu">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg>
                        </button>

                        <div class="user-menu-dropdown" id="user-menu-dropdown">
                            <a href="{{ route('profile.edit') }}" class="user-menu-item">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg>
                                My Profile
                            </a>
                            <a href="{{ route('orders.index') }}" class="user-menu-item">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 8v13H3V8"/><path d="M1 3h22v5H1z"/><path d="M10 12h4"/></svg>
                                My Orders
                            </a>
                            <a href="{{ route('addresses.index') }}" class="user-menu-item">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 6-9 12-9 12s-9-6-9-12a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                My Address
                            </a>
                            <a href="{{ route('reviews.index') }}" class="user-menu-item">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 17.27 18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                                My Reviews
                            </a>
                            <a href="{{ route('profile.password.edit') }}" class="user-menu-item">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                Change Password
                            </a>

                            <div class="user-menu-divider"></div>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="user-menu-item danger">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5"/><path d="M21 12H9"/></svg>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="account-link nav-icon-btn" aria-label="Sign in">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg>
                    </a>
                @endauth
            </div>
        </div>

        <div class="nav-search-panel" id="nav-search-panel">
            <form method="GET" action="{{ route('search.index') }}" class="nav-search-form" role="search">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search products..." aria-label="Search products" id="nav-search-input">
                <button type="submit" aria-label="Search">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                </button>
            </form>
        </div>
    </header>

    <main class="site-main">
        {{ $slot }}
    </main>

    <footer class="site-footer">
        <div class="site-footer-inner">
            <div class="site-footer-col site-footer-brand">
                <a href="{{ route('home') }}" class="site-footer-brand-name">{{ $siteName }}</a>

                @if ($hasSocial)
                    <div class="site-footer-social">
                        @if ($whatsappNumber)
                            <a href="https://wa.me/{{ $whatsappNumber }}" target="_blank" rel="noopener" aria-label="WhatsApp">
                                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.29-1.39a9.9 9.9 0 0 0 4.75 1.21h.01c5.46 0 9.9-4.45 9.9-9.91 0-2.65-1.03-5.14-2.9-7.01A9.86 9.86 0 0 0 12.04 2zm0 18.13h-.01a8.2 8.2 0 0 1-4.19-1.15l-.3-.18-3.14.82.84-3.06-.2-.32a8.2 8.2 0 0 1-1.26-4.37c0-4.54 3.7-8.24 8.26-8.24 2.2 0 4.27.86 5.83 2.42a8.18 8.18 0 0 1 2.41 5.83c0 4.55-3.7 8.25-8.24 8.25zm4.52-6.17c-.25-.12-1.47-.72-1.7-.81-.23-.08-.39-.12-.56.13-.17.25-.64.81-.78.97-.14.17-.29.19-.53.06-.25-.12-1.05-.39-2-1.23-.74-.66-1.24-1.48-1.39-1.73-.14-.25-.02-.38.11-.51.11-.11.25-.29.37-.43.12-.15.16-.25.25-.42.08-.17.04-.31-.02-.44-.06-.12-.56-1.35-.77-1.85-.2-.48-.4-.42-.56-.43-.14-.01-.31-.01-.48-.01-.16 0-.43.06-.66.31-.23.25-.86.85-.86 2.06 0 1.22.88 2.4 1.01 2.56.12.17 1.73 2.64 4.19 3.7.59.25 1.04.4 1.4.52.59.19 1.12.16 1.54.1.47-.07 1.47-.6 1.67-1.18.21-.58.21-1.07.14-1.18-.06-.1-.23-.16-.48-.29z"/></svg>
                            </a>
                        @endif
                        @if (! empty($siteSettings['social_facebook']))
                            <a href="{{ $siteSettings['social_facebook'] }}" target="_blank" rel="noopener" aria-label="Facebook">
                                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M22 12a10 10 0 1 0-11.56 9.88v-6.99H7.9V12h2.54V9.8c0-2.5 1.49-3.89 3.77-3.89 1.09 0 2.24.2 2.24.2v2.46h-1.26c-1.24 0-1.63.77-1.63 1.56V12h2.77l-.44 2.89h-2.33v6.99A10 10 0 0 0 22 12z"/></svg>
                            </a>
                        @endif
                        @if (! empty($siteSettings['social_instagram']))
                            <a href="{{ $siteSettings['social_instagram'] }}" target="_blank" rel="noopener" aria-label="Instagram">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1"/></svg>
                            </a>
                        @endif
                        @if (! empty($siteSettings['social_twitter']))
                            <a href="{{ $siteSettings['social_twitter'] }}" target="_blank" rel="noopener" aria-label="Twitter">
                                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M22 5.9c-.77.35-1.6.58-2.46.68a4.3 4.3 0 0 0 1.88-2.37 8.6 8.6 0 0 1-2.72 1.04 4.28 4.28 0 0 0-7.29 3.9A12.14 12.14 0 0 1 2.9 4.9a4.28 4.28 0 0 0 1.32 5.71c-.7-.02-1.36-.22-1.94-.53v.05a4.28 4.28 0 0 0 3.43 4.2c-.65.18-1.34.2-1.99.08a4.29 4.29 0 0 0 4 2.98A8.6 8.6 0 0 1 1 19.08a12.1 12.1 0 0 0 6.56 1.92c7.88 0 12.2-6.53 12.2-12.2 0-.19 0-.37-.01-.56A8.7 8.7 0 0 0 22 5.9z"/></svg>
                            </a>
                        @endif
                        @if (! empty($siteSettings['social_youtube']))
                            <a href="{{ $siteSettings['social_youtube'] }}" target="_blank" rel="noopener" aria-label="YouTube">
                                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M23 12s0-3.6-.46-5.32a3 3 0 0 0-2.1-2.13C18.7 4 12 4 12 4s-6.7 0-8.44.55a3 3 0 0 0-2.1 2.13C1 8.4 1 12 1 12s0 3.6.46 5.32a3 3 0 0 0 2.1 2.13C5.3 20 12 20 12 20s6.7 0 8.44-.55a3 3 0 0 0 2.1-2.13C23 15.6 23 12 23 12z"/><path d="M9.75 15.5v-7l6 3.5-6 3.5z" fill="#111827"/></svg>
                            </a>
                        @endif
                    </div>
                @endif
            </div>

            @if ($navCategories->isNotEmpty())
                <div class="site-footer-col">
                    <h3 class="site-footer-heading">Shop</h3>
                    @foreach ($navCategories as $navCategory)
                        <a href="{{ route('category.show', $navCategory) }}" class="site-footer-link">{{ $navCategory->name }}</a>
                    @endforeach
                </div>
            @endif

            @if ($hasFooterContact)
                <div class="site-footer-col">
                    <h3 class="site-footer-heading">Contact</h3>
                    @if (! empty($siteSettings['address']))
                        <p class="site-footer-text">{{ $siteSettings['address'] }}</p>
                    @endif
                    @if (! empty($siteSettings['phone']))
                        <p class="site-footer-text">{{ $siteSettings['phone'] }}</p>
                    @endif
                    @if (! empty($siteSettings['email']))
                        <p class="site-footer-text">{{ $siteSettings['email'] }}</p>
                    @endif
                </div>
            @endif

            <div class="site-footer-col">
                <h3 class="site-footer-heading">Legal</h3>
                <a href="{{ route('terms.index') }}" class="site-footer-link">Terms of Service</a>
                <a href="{{ route('privacy.index') }}" class="site-footer-link">Privacy Policy</a>
            </div>
        </div>

        <div class="site-footer-bottom">
            <span>&copy; {{ now()->year }} {{ $siteName }}. All rights reserved.</span>
        </div>
    </footer>

    <div class="toast-stack" id="toast-stack"></div>

    <div class="modal-overlay" id="quick-view-modal">
        <div class="modal modal-lg" id="quick-view-dialog"></div>
    </div>

    <script src="{{ asset('js/user-menu.js') }}"></script>
    <script src="{{ asset('js/mobile-menu.js') }}"></script>
    <script src="{{ asset('js/header-search.js') }}"></script>
    <script src="{{ asset('js/aos.js') }}"></script>
    <script src="{{ asset('js/aos-init.js') }}"></script>
</body>
</html>
