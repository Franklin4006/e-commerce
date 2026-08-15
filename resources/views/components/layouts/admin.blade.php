<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? ($siteSettings['site_name'] ?? config('app.name')) }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <div class="admin-layout">
        <div class="sidebar-backdrop" id="sidebar-backdrop"></div>

        <aside class="sidebar" id="sidebar">
            <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
                @if (! empty($siteSettings['logo']))
                    <img src="{{ asset('uploads/'.$siteSettings['logo']) }}" alt="" class="sidebar-brand-logo">
                @else
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l1-5h16l1 5"/><path d="M4 9h16v10a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V9z"/><path d="M9 13a3 3 0 0 0 6 0"/></svg>
                @endif
                <span class="sidebar-brand-text">{{ $siteSettings['site_name'] ?? config('app.name') }}</span>
            </a>

            <a href="{{ route('admin.profile.edit') }}" class="sidebar-user {{ request()->routeIs('admin.profile.*') ? 'active' : '' }}">
                @if (auth()->user()->avatar)
                    <img src="{{ asset('uploads/'.auth()->user()->avatar) }}" alt="" class="sidebar-user-avatar">
                @else
                    <span class="sidebar-user-avatar">{{ Str::of(auth()->user()->name)->substr(0, 1)->upper() }}</span>
                @endif
                <span class="sidebar-user-info">
                    <span class="sidebar-user-name">{{ auth()->user()->name }}</span>
                    <span class="sidebar-user-role"><span class="sidebar-user-dot"></span>Administrator</span>
                </span>
            </a>

            <nav class="sidebar-nav">
                <a href="{{ route('admin.dashboard') }}"
                   class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                    Dashboard
                </a>

                <div class="sidebar-nav-label">Sales</div>
                <a href="{{ route('admin.orders.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                    <span class="sidebar-link-text">Orders</span>
                    @if (! empty($pendingOrdersCount))
                        <span class="sidebar-badge sidebar-badge-warning" title="{{ $pendingOrdersCount }} pending">{{ $pendingOrdersCount }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.customers.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    Customers
                </a>
                <a href="{{ route('admin.coupons.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41 11 22 2 13l8.59-8.59A2 2 0 0 1 12 4h8a2 2 0 0 1 2 2v8a2 2 0 0 1-.41 1.41z"/><circle cx="7.5" cy="7.5" r="1"/></svg>
                    Coupons
                </a>

                <div class="sidebar-nav-label">Shipping</div>
                <a href="{{ route('admin.states.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.states.*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    States
                </a>
                <a href="{{ route('admin.shipping-slabs.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.shipping-slabs.*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                    Order Value Charges
                </a>

                <div class="sidebar-nav-label">Catalog</div>
                <a href="{{ route('admin.products.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 8v13H3V8"/><path d="M1 3h22v5H1z"/><path d="M10 12h4"/></svg>
                    Products
                </a>
                <a href="{{ route('admin.categories.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41 11 3.83A2 2 0 0 0 9.59 3.24L4 3a1 1 0 0 0-1 1l.24 5.59a2 2 0 0 0 .58 1.41l9.59 9.59a2 2 0 0 0 2.83 0l4.35-4.35a2 2 0 0 0 0-2.83z"/><circle cx="7.5" cy="7.5" r="1.2"/></svg>
                    Categories
                </a>
                <a href="{{ route('admin.sizes.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.sizes.*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16v4H4z"/><path d="M7 8v12"/><path d="M17 8v12"/></svg>
                    Sizes
                </a>
                <a href="{{ route('admin.colors.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.colors.*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="13.5" cy="6.5" r="2.5"/><circle cx="19" cy="17" r="2.5"/><circle cx="6" cy="17" r="2.5"/></svg>
                    Colors
                </a>
                <a href="{{ route('admin.inventory.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.inventory.*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3h18v18H3z" opacity="0"/><rect x="3" y="7" width="18" height="14" rx="1"/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><path d="M3 12h18"/></svg>
                    <span class="sidebar-link-text">Inventory</span>
                    @if (! empty($lowStockCount))
                        <span class="sidebar-badge sidebar-badge-danger" title="{{ $lowStockCount }} low on stock">{{ $lowStockCount }}</span>
                    @endif
                </a>

                <div class="sidebar-nav-label">Content</div>
                <a href="{{ route('admin.banners.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.banners.*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 9h18"/><circle cx="7" cy="14" r="1"/></svg>
                    Banners
                </a>
                <a href="{{ route('admin.reviews.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 17.27 18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                    <span class="sidebar-link-text">Reviews</span>
                    @if (! empty($pendingReviewsCount))
                        <span class="sidebar-badge sidebar-badge-info" title="{{ $pendingReviewsCount }} pending">{{ $pendingReviewsCount }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.faqs.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.faqs.*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    FAQs
                </a>

                <div class="sidebar-nav-label">System</div>
                <a href="{{ route('admin.email-templates.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.email-templates.*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16v16H4z" opacity="0"/><path d="M22 6 12 13 2 6"/><rect x="2" y="4" width="20" height="16" rx="2"/></svg>
                    Email Templates
                </a>
                @can('super-admin')
                    <a href="{{ route('admin.settings.edit') }}"
                       class="sidebar-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                        Settings
                    </a>
                    <a href="{{ route('admin.admin-users.index') }}"
                       class="sidebar-link {{ request()->routeIs('admin.admin-users.*') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        Admin Users
                    </a>
                @endcan
                <a href="{{ route('admin.profile.edit') }}"
                   class="sidebar-link {{ request()->routeIs('admin.profile.*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg>
                    Profile
                </a>
            </nav>

            <div class="sidebar-footer">
                <span class="sidebar-footer-label">Version</span>
                <span class="sidebar-footer-value">1.0.0</span>
            </div>
        </aside>

        <div class="admin-content">
            <header class="topbar">
                <button type="button" class="sidebar-toggle" id="sidebar-toggle" aria-label="Toggle menu">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 6h16"/><path d="M4 12h16"/><path d="M4 18h16"/></svg>
                </button>

                <a href="{{ route('home') }}" target="_blank" rel="noopener" class="topbar-link" title="View store">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l1-5h16l1 5"/><path d="M4 9h16v10a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V9z"/><path d="M9 13a3 3 0 0 0 6 0"/></svg>
                    <span>View store</span>
                </a>

                <div class="topbar-right">
                    <span class="topbar-user">
                        <span class="avatar">{{ Str::of(auth()->user()->name)->substr(0, 1)->upper() }}</span>
                        <span class="topbar-user-name">{{ auth()->user()->name }}</span>
                    </span>

                    <form method="POST" action="{{ route('admin.logout') }}" class="topbar-logout">
                        @csrf
                        <button type="submit" class="btn-icon danger" title="Log out">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5"/><path d="M21 12H9"/></svg>
                        </button>
                    </form>
                </div>
            </header>

            <main class="admin-main">
                {{ $slot }}
            </main>
        </div>
    </div>

    <div class="toast-stack" id="toast-stack"></div>

    @if (session('status'))
        <div id="flash-status" data-message="{{ session('status') }}" hidden></div>
    @endif

    <script src="{{ asset('js/admin-layout.js') }}"></script>
</body>
</html>
