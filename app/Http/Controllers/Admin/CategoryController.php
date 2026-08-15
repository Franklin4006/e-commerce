<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $categories = Category::orderBy('priority')->latest()->paginate(10);

        if ($request->ajax()) {
            return view('admin.categories._table', compact('categories'));
        }

        return view('admin.categories.index', compact('categories'));
    }

    public function show(Category $category): JsonResponse
    {
        return response()->json([
            'id' => $category->id,
            'name' => $category->name,
            'status' => $category->status,
            'priority' => $category->priority,
            'image_url' => $category->image ? asset('uploads/'.$category->image) : null,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $this->validateCategory($request);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('categories', 'public');
        }

        Category::create($validated);

        return response()->json(['message' => 'Category created successfully.']);
    }

    public function update(Request $request, Category $category): JsonResponse
    {
        $validated = $this->validateCategory($request, $category);

        if ($request->hasFile('image')) {
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }

            $validated['image'] = $request->file('image')->store('categories', 'public');
        }

        $category->update($validated);

        return response()->json(['message' => 'Category updated successfully.']);
    }

    public function destroy(Category $category): JsonResponse
    {
        try {
            $category->delete();
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'This category has products assigned to it and cannot be deleted.',
            ], 409);
        }

        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        return response()->json(['message' => 'Category deleted successfully.']);
    }

    private function validateCategory(Request $request, ?Category $category = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('categories')->ignore($category)],
            'image' => ['nullable', 'image', 'max:2048'],
            'status' => ['sometimes', 'boolean'],
            'priority' => ['nullable', 'integer', 'min:0'],
        ]) + ['status' => $request->boolean('status')];
    }
}
