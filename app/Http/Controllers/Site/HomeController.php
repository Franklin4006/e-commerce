<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $banners = Banner::where('status', true)->orderBy('priority')->get();
        $categories = Category::where('status', true)->orderBy('priority')->get();
        $products = Product::with('category')
            ->where('status', true)
            ->orderBy('priority')
            ->latest()
            ->take(10)
            ->get();

        // Most recently added products for the "New Arrivals" strip.
        $newArrivals = Product::with('category')
            ->where('status', true)
            ->latest()
            ->take(10)
            ->get();

        // Top discounted, in-stock products for the "Today's Deals" strip.
        $deals = Product::with('category')
            ->where('status', true)
            ->where('stock', '>', 0)
            ->whereColumn('sale_price', '<', 'mrp')
            ->orderByRaw('(mrp - sale_price) / NULLIF(mrp, 0) DESC')
            ->take(10)
            ->get();

        return view('site.home', compact('banners', 'categories', 'products', 'newArrivals', 'deals'));
    }
}
