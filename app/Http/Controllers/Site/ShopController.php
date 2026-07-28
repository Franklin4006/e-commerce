<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Size;
use App\Support\ProductSorter;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShopController extends Controller
{
    public function index(Request $request): View
    {
        $categories = Category::where('status', true)->orderBy('priority')->get();

        $activeCategory = $request->integer('category') ?: null;
        $min = $request->filled('min') ? (float) $request->input('min') : null;
        $max = $request->filled('max') ? (float) $request->input('max') : null;
        $sizes = array_values(array_intersect($request->input('sizes', []), Size::names()));
        $sort = $request->input('sort', 'featured');

        $query = Product::with('category')->where('status', true);

        if ($activeCategory) {
            $query->where('category_id', $activeCategory);
        }

        if ($min !== null) {
            $query->where('sale_price', '>=', $min);
        }

        if ($max !== null) {
            $query->where('sale_price', '<=', $max);
        }

        if (! empty($sizes)) {
            $query->whereHas('sizes', function ($q) use ($sizes) {
                $q->whereIn('size', $sizes)->where('stock', '>', 0);
            });
        }

        ProductSorter::apply($query, $sort);

        $products = $query->paginate(12)->withQueryString();

        return view('site.shop', [
            'products' => $products,
            'categories' => $categories,
            'activeCategory' => $activeCategory,
            'min' => $min,
            'max' => $max,
            'sizes' => $sizes,
            'sort' => $sort,
            'filterAction' => route('shop.index'),
        ]);
    }
}
