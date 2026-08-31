<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function index(Request $request): View
    {
        $query = trim((string) $request->input('q', ''));

        $products = $query === ''
            ? Product::query()->whereRaw('0 = 1')->paginate(12)
            : Product::with('category')
                ->where('status', true)
                ->where(function ($q) use ($query) {
                    $q->where('name', 'like', '%'.$query.'%')
                        ->orWhere('description', 'like', '%'.$query.'%');
                })
                // Name matches rank above description-only matches, so a
                // product whose name contains the search term always
                // outranks one that only happens to mention it in passing.
                ->selectRaw('products.*, case when name like ? then 0 else 1 end as name_match_rank', ['%'.$query.'%'])
                ->orderBy('name_match_rank')
                ->orderBy('priority')
                ->latest()
                ->paginate(12)
                ->withQueryString();

        return view('site.search', [
            'products' => $products,
            'query' => $query,
        ]);
    }
}
