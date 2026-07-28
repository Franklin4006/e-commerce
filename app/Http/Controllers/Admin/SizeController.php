<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Size;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SizeController extends Controller
{
    public function index(): View
    {
        $sizes = Size::ordered()->paginate(15);

        return view('admin.sizes.index', compact('sizes'));
    }

    public function create(): View
    {
        return view('admin.sizes.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateSize($request);

        Size::create($validated);

        return redirect()->route('admin.sizes.index')->with('status', 'Size created successfully.');
    }

    public function edit(Size $size): View
    {
        return view('admin.sizes.edit', compact('size'));
    }

    public function update(Request $request, Size $size): RedirectResponse
    {
        $validated = $this->validateSize($request, $size);

        $size->update($validated);

        return redirect()->route('admin.sizes.index')->with('status', 'Size updated successfully.');
    }

    public function destroy(Size $size): RedirectResponse
    {
        $size->delete();

        return redirect()->route('admin.sizes.index')->with('status', 'Size deleted successfully.');
    }

    private function validateSize(Request $request, ?Size $size = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:20', Rule::unique('sizes')->ignore($size)],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
    }
}
