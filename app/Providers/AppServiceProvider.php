<?php

namespace App\Providers;

use App\Enums\AdminRole;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\Setting;
use App\Models\User;
use App\Support\Cart;
use App\Support\Wishlist;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Authenticate::redirectUsing(fn (Request $request) => $request->is('admin/*') ? route('admin.login') : route('login'));
        RedirectIfAuthenticated::redirectUsing(fn (Request $request) => $request->is('admin/*') ? route('admin.dashboard') : route('home'));

        Paginator::defaultView('vendor.pagination.custom');
        Paginator::defaultSimpleView('vendor.pagination.custom');

        View::composer('components.layouts.site', function ($view) {
            $view->with('siteSettings', Setting::allSettings());
            $view->with('navCategories', Category::where('status', true)->orderBy('priority')->take(8)->get());
            $view->with('cartCount', Cart::count());
            $view->with('wishlistCount', Wishlist::count());
        });

        View::composer(['components.layouts.admin', 'components.layouts.guest'], function ($view) {
            $view->with('siteSettings', Setting::allSettings());
        });

        // Live counts for the admin sidebar notification badges.
        View::composer('components.layouts.admin', function ($view) {
            $lowStockThreshold = (int) Setting::get('low_stock_threshold', 5);

            $view->with('pendingOrdersCount', Order::where('status', 'pending')->paymentConfirmed()->count());
            $view->with('paymentIssuesCount', Order::whereIn('payment_status', ['pending', 'failed'])->count());
            $view->with('pendingReviewsCount', Review::where('status', 'pending')->count());
            $view->with('lowStockCount', Product::where('stock', '<=', $lowStockThreshold)->count());
        });

        Gate::define('content-write', fn (User $user) => $user->is_admin
            && in_array($user->role, [AdminRole::SuperAdmin, AdminRole::Admin], true));

        Gate::define('content-delete', fn (User $user) => $user->is_admin
            && $user->role === AdminRole::SuperAdmin);

        Gate::define('super-admin', fn (User $user) => $user->is_admin
            && $user->role === AdminRole::SuperAdmin);

        $this->configureMailFromSettings();
        $this->configureGoogleFromSettings();
    }

    /**
     * Apply admin-managed SMTP settings (stored in the database) to the runtime mail config.
     */
    private function configureMailFromSettings(): void
    {
        if (! Schema::hasTable('settings')) {
            return;
        }

        $settings = Setting::allSettings();

        if (empty($settings['mail_host'])) {
            return;
        }

        Config::set('mail.default', $settings['mail_mailer'] ?? 'smtp');
        Config::set('mail.mailers.smtp.host', $settings['mail_host']);
        Config::set('mail.mailers.smtp.port', $settings['mail_port'] ?? 587);
        Config::set('mail.mailers.smtp.username', $settings['mail_username'] ?? null);
        Config::set('mail.mailers.smtp.password', $settings['mail_password'] ?? null);
        Config::set('mail.mailers.smtp.encryption', $settings['mail_encryption'] ?: null);

        if (! empty($settings['mail_from_address'])) {
            Config::set('mail.from.address', $settings['mail_from_address']);
            Config::set('mail.from.name', $settings['mail_from_name'] ?? config('mail.from.name'));
        }
    }

    /**
     * Apply admin-managed Google OAuth credentials (stored in the database) to the runtime services config.
     */
    private function configureGoogleFromSettings(): void
    {
        if (! Schema::hasTable('settings')) {
            return;
        }

        $settings = Setting::allSettings();

        if (empty($settings['google_client_id']) || empty($settings['google_client_secret'])) {
            return;
        }

        Config::set('services.google.client_id', $settings['google_client_id']);
        Config::set('services.google.client_secret', $settings['google_client_secret']);
    }
}
