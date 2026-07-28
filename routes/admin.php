<?php

use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ColorController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EmailTemplateController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SizeController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [LoginController::class, 'create'])->name('login');
        Route::post('/login', [LoginController::class, 'store']);
    });

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

        Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('categories.show');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

        Route::get('/sizes', [SizeController::class, 'index'])->name('sizes.index');
        Route::get('/sizes/create', [SizeController::class, 'create'])->name('sizes.create');
        Route::post('/sizes', [SizeController::class, 'store'])->name('sizes.store');
        Route::get('/sizes/{size}/edit', [SizeController::class, 'edit'])->name('sizes.edit');
        Route::put('/sizes/{size}', [SizeController::class, 'update'])->name('sizes.update');
        Route::delete('/sizes/{size}', [SizeController::class, 'destroy'])->name('sizes.destroy');

        Route::get('/colors', [ColorController::class, 'index'])->name('colors.index');
        Route::get('/colors/create', [ColorController::class, 'create'])->name('colors.create');
        Route::post('/colors', [ColorController::class, 'store'])->name('colors.store');
        Route::get('/colors/{color}/edit', [ColorController::class, 'edit'])->name('colors.edit');
        Route::put('/colors/{color}', [ColorController::class, 'update'])->name('colors.update');
        Route::delete('/colors/{color}', [ColorController::class, 'destroy'])->name('colors.destroy');

        Route::get('/products', [ProductController::class, 'index'])->name('products.index');
        Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
        Route::delete('/products/{product}/color-images/{image}', [ProductController::class, 'destroyColorImage'])->name('products.color-images.destroy');

        Route::get('/banners', [BannerController::class, 'index'])->name('banners.index');
        Route::get('/banners/{banner}', [BannerController::class, 'show'])->name('banners.show');
        Route::post('/banners', [BannerController::class, 'store'])->name('banners.store');
        Route::put('/banners/{banner}', [BannerController::class, 'update'])->name('banners.update');
        Route::delete('/banners/{banner}', [BannerController::class, 'destroy'])->name('banners.destroy');

        Route::get('/settings', [SettingController::class, 'edit'])->name('settings.edit');
        Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');

        Route::get('/email-templates', [EmailTemplateController::class, 'index'])->name('email-templates.index');
        Route::get('/email-templates/create', [EmailTemplateController::class, 'create'])->name('email-templates.create');
        Route::post('/email-templates', [EmailTemplateController::class, 'store'])->name('email-templates.store');
        Route::get('/email-templates/{emailTemplate}/edit', [EmailTemplateController::class, 'edit'])->name('email-templates.edit');
        Route::put('/email-templates/{emailTemplate}', [EmailTemplateController::class, 'update'])->name('email-templates.update');
        Route::delete('/email-templates/{emailTemplate}', [EmailTemplateController::class, 'destroy'])->name('email-templates.destroy');

        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
        Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::put('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status.update');
        Route::put('/orders/{order}/payment-status', [OrderController::class, 'updatePaymentStatus'])->name('orders.payment-status.update');
        Route::get('/orders/{order}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');

        Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
        Route::get('/customers/create', [CustomerController::class, 'create'])->name('customers.create');
        Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
        Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
        Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');

        Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
        Route::get('/inventory/{product}', [InventoryController::class, 'show'])->name('inventory.show');
        Route::post('/inventory/{product}/adjust', [InventoryController::class, 'adjust'])->name('inventory.adjust');

        Route::get('/faqs', [FaqController::class, 'index'])->name('faqs.index');
        Route::get('/faqs/create', [FaqController::class, 'create'])->name('faqs.create');
        Route::post('/faqs', [FaqController::class, 'store'])->name('faqs.store');
        Route::get('/faqs/{faq}/edit', [FaqController::class, 'edit'])->name('faqs.edit');
        Route::put('/faqs/{faq}', [FaqController::class, 'update'])->name('faqs.update');
        Route::delete('/faqs/{faq}', [FaqController::class, 'destroy'])->name('faqs.destroy');

        Route::get('/coupons', [CouponController::class, 'index'])->name('coupons.index');
        Route::get('/coupons/create', [CouponController::class, 'create'])->name('coupons.create');
        Route::post('/coupons', [CouponController::class, 'store'])->name('coupons.store');
        Route::get('/coupons/{coupon}/edit', [CouponController::class, 'edit'])->name('coupons.edit');
        Route::put('/coupons/{coupon}', [CouponController::class, 'update'])->name('coupons.update');
        Route::delete('/coupons/{coupon}', [CouponController::class, 'destroy'])->name('coupons.destroy');

        Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');
        Route::put('/reviews/{review}/status', [ReviewController::class, 'updateStatus'])->name('reviews.status.update');
        Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
    });
});
