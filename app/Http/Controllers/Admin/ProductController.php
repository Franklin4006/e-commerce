<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Models\ProductColorImage;
use App\Models\ProductSize;
use App\Models\Size;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $products = Product::with('category')
            ->when($request->filled('search'), fn ($query) => $query->where('name', 'like', '%'.$request->input('search').'%'))
            ->when($request->filled('category_id'), fn ($query) => $query->where('category_id', $request->input('category_id')))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->input('status')))
            ->orderBy('priority')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        if ($request->ajax()) {
            return view('admin.products._table', compact('products'));
        }

        $categories = Category::orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();
        $allProducts = Product::orderBy('name')->get(['id', 'name']);

        return view('admin.products.create', compact('categories', 'allProducts'));
    }

    public function edit(Product $product): View
    {
        $product->load('relatedProducts', 'specifications', 'sizes', 'colors.sizes', 'colors.images');
        $categories = Category::orderBy('name')->get();
        $allProducts = Product::orderBy('name')->get(['id', 'name']);

        return view('admin.products.edit', compact('product', 'categories', 'allProducts'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateProduct($request);

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('products', 'public');
        }

        $product = Product::create($validated);

        $this->syncRelatedProducts($request, $product);
        $this->syncSpecifications($request, $product);
        $this->syncColors($request, $product);

        return redirect()->route('admin.products.index')->with('status', 'Product created successfully.');
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $this->validateProduct($request, $product);

        if ($request->hasFile('thumbnail')) {
            if ($product->thumbnail) {
                Storage::disk('public')->delete($product->thumbnail);
            }

            $validated['thumbnail'] = $request->file('thumbnail')->store('products', 'public');
        }

        $product->update($validated);

        $this->syncRelatedProducts($request, $product);
        $this->syncSpecifications($request, $product);
        $this->syncColors($request, $product);

        return redirect()->route('admin.products.index')->with('status', 'Product updated successfully.');
    }

    public function destroy(Product $product): JsonResponse
    {
        if ($product->thumbnail) {
            Storage::disk('public')->delete($product->thumbnail);
        }

        // Deleting each color through Eloquent (rather than $product->delete()
        // relying on the DB cascade) fires ProductColor's own cleanup of its
        // images/sizes — see ProductColor::booted().
        $product->colors->each->delete();

        $product->delete();

        return response()->json(['message' => 'Product deleted successfully.']);
    }

    public function destroyColorImage(Product $product, ProductColorImage $image): JsonResponse
    {
        abort_if($image->color->product_id !== $product->id, 404);

        Storage::disk('public')->delete($image->image);
        $image->delete();

        return response()->json(['message' => 'Image removed successfully.']);
    }

    private function syncRelatedProducts(Request $request, Product $product): void
    {
        $relatedIds = collect($request->input('related_products', []))
            ->reject(fn ($id) => (int) $id === $product->id)
            ->values()
            ->all();

        $product->relatedProducts()->sync($relatedIds);
    }

    private function syncSpecifications(Request $request, Product $product): void
    {
        $keys = $request->input('specification_keys', []);
        $values = $request->input('specification_values', []);

        $product->specifications()->delete();

        $sortOrder = 0;

        foreach ($keys as $index => $key) {
            $key = trim((string) $key);
            $value = trim((string) ($values[$index] ?? ''));

            if ($key === '' || $value === '') {
                continue;
            }

            $product->specifications()->create([
                'key' => $key,
                'value' => $value,
                'sort_order' => $sortOrder++,
            ]);
        }
    }

    /**
     * Colors are picked from the admin-managed color palette (see
     * ColorController), each product-color row then getting its own photo
     * gallery and its own stock-by-size grid. The row's name/hex are
     * denormalized from the selected palette color at save time, same as
     * product_name on order_items, so the storefront/cart/order code that
     * already reads product_colors.name/hex keeps working untouched.
     * Existing rows keep their id (posted back as colors.*.id) so
     * in-cart/ordered references stay valid; rows dropped from the form are
     * deleted, which cascades to their ProductSize rows and image files (see
     * ProductColor::booted()).
     */
    private function syncColors(Request $request, Product $product): void
    {
        $rows = $request->input('colors', []);
        $keptIds = [];
        $usedNames = [];

        foreach ($rows as $index => $row) {
            $colorId = ! empty($row['color_id']) ? (int) $row['color_id'] : null;

            if (! $colorId) {
                continue;
            }

            $paletteColor = Color::find($colorId);

            if (! $paletteColor || in_array($paletteColor->name, $usedNames, true)) {
                continue;
            }

            $usedNames[] = $paletteColor->name;

            $rowId = ! empty($row['id']) ? (int) $row['id'] : null;
            $existing = $rowId ? $product->colors()->find($rowId) : null;

            $attributes = [
                'color_id' => $paletteColor->id,
                'name' => $paletteColor->name,
                'hex' => $paletteColor->hex,
                'sort_order' => $index,
            ];

            $color = $existing ?: $product->colors()->make();
            $color->fill($attributes);
            $color->save();

            $keptIds[] = $color->id;

            if ($request->hasFile("colors.{$index}.images")) {
                $nextSort = (int) $color->images()->max('sort_order') + 1;

                foreach ($request->file("colors.{$index}.images") as $file) {
                    $color->images()->create([
                        'image' => $file->store('products/colors', 'public'),
                        'sort_order' => $nextSort++,
                    ]);
                }
            }

            $sizes = $row['sizes'] ?? [];

            foreach (Size::names() as $size) {
                ProductSize::updateOrCreate(
                    ['product_id' => $product->id, 'product_color_id' => $color->id, 'size' => $size],
                    ['stock' => max(0, (int) ($sizes[$size] ?? 0))]
                );
            }
        }

        $removedColors = $product->colors()->whereNotIn('id', $keptIds ?: [0])->get();

        // Deleted one at a time (not a bulk query delete) so each color's
        // own cleanup of its images/sizes actually runs.
        $removedColors->each->delete();
    }

    private function validateProduct(Request $request, ?Product $product = null): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255', Rule::unique('products')->ignore($product)],
            'category_id' => ['required', 'exists:categories,id'],
            'description' => ['nullable', 'string'],
            'mrp' => ['required', 'numeric', 'min:0.01'],
            'sale_price' => ['required', 'numeric', 'min:0.01', 'lte:mrp'],
            'colors' => ['required', 'array', 'min:1'],
            'colors.*.id' => ['nullable', 'integer'],
            'colors.*.color_id' => ['required', 'integer', 'exists:colors,id', 'distinct'],
            'colors.*.images' => ['nullable', 'array'],
            'colors.*.images.*' => ['image', 'max:2048'],
            'colors.*.sizes' => ['nullable', 'array'],
            'thumbnail' => [$product ? 'nullable' : 'required', 'image', 'max:2048'],
            'related_products' => ['nullable', 'array'],
            'related_products.*' => ['integer', 'exists:products,id'],
            'specification_keys' => ['nullable', 'array'],
            'specification_keys.*' => ['nullable', 'string', 'max:100', 'required_with:specification_values.*'],
            'specification_values' => ['nullable', 'array'],
            'specification_values.*' => ['nullable', 'string', 'max:255', 'required_with:specification_keys.*'],
            'status' => ['sometimes', 'boolean'],
            'priority' => ['nullable', 'integer', 'min:0'],
        ];

        // Sizes are an admin-managed list (see SizeController), not a fixed
        // set, so the per-size stock inputs are validated dynamically.
        foreach (Size::names() as $sizeName) {
            $rules["colors.*.sizes.{$sizeName}"] = ['nullable', 'integer', 'min:0'];
        }

        $messages = [
            'colors.required' => 'Add at least one color with its stock by size.',
            'colors.*.color_id.required' => 'Select a color for each row.',
            'colors.*.color_id.distinct' => 'This color has already been added — remove the duplicate row.',
            'colors.*.images.*.image' => 'Each photo must be a valid image file.',
            'thumbnail.required' => 'A thumbnail image is required.',
            'specification_keys.*.required_with' => 'Enter a key for every specification value.',
            'specification_values.*.required_with' => 'Enter a value for every specification key.',
        ];

        $attributes = [
            'category_id' => 'category',
            'mrp' => 'MRP',
        ];

        $validated = $request->validate($rules, $messages, $attributes) + ['status' => $request->boolean('status')];

        unset($validated['colors']);

        return $validated;
    }
}
