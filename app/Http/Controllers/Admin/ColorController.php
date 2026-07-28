<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Color;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ColorController extends Controller
{
    public function index(): View
    {
        $colors = Color::ordered()->paginate(15);

        return view('admin.colors.index', compact('colors'));
    }

    public function create(): View
    {
        return view('admin.colors.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateColor($request);

        Color::create($validated);

        return redirect()->route('admin.colors.index')->with('status', 'Color created successfully.');
    }

    public function edit(Color $color): View
    {
        return view('admin.colors.edit', compact('color'));
    }

    public function update(Request $request, Color $color): RedirectResponse
    {
        $validated = $this->validateColor($request, $color);

        $color->update($validated);

        return redirect()->route('admin.colors.index')->with('status', 'Color updated successfully.');
    }

    public function destroy(Color $color): RedirectResponse
    {
        $color->delete();

        return redirect()->route('admin.colors.index')->with('status', 'Color deleted successfully.');
    }

    private function validateColor(Request $request, ?Color $color = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:60', Rule::unique('colors')->ignore($color)],
            'hex' => ['nullable', 'string', 'max:7'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
    }
}
