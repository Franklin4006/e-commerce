<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BannerController extends Controller
{
    public function index(Request $request): View
    {
        $banners = Banner::orderBy('priority')->latest()->paginate(10);

        if ($request->ajax()) {
            return view('admin.banners._table', compact('banners'));
        }

        return view('admin.banners.index', compact('banners'));
    }

    public function show(Banner $banner): JsonResponse
    {
        return response()->json([
            'id' => $banner->id,
            'title' => $banner->title,
            'sub_title' => $banner->sub_title,
            'title_position' => $banner->title_position,
            'button_text' => $banner->button_text,
            'button_url' => $banner->button_url,
            'button_color' => $banner->button_color,
            'status' => $banner->status,
            'priority' => $banner->priority,
            'image_url' => $banner->image ? asset('uploads/'.$banner->image) : null,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $this->validateBanner($request);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('banners', 'public');
        }

        Banner::create($validated);

        return response()->json(['message' => 'Banner created successfully.']);
    }

    public function update(Request $request, Banner $banner): JsonResponse
    {
        $validated = $this->validateBanner($request, $banner);

        if ($request->hasFile('image')) {
            if ($banner->image) {
                Storage::disk('public')->delete($banner->image);
            }

            $validated['image'] = $request->file('image')->store('banners', 'public');
        }

        $banner->update($validated);

        return response()->json(['message' => 'Banner updated successfully.']);
    }

    public function destroy(Banner $banner): JsonResponse
    {
        if ($banner->image) {
            Storage::disk('public')->delete($banner->image);
        }

        $banner->delete();

        return response()->json(['message' => 'Banner deleted successfully.']);
    }

    private function validateBanner(Request $request, ?Banner $banner = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'sub_title' => ['nullable', 'string', 'max:255'],
            'image' => [$banner ? 'nullable' : 'required', 'image', 'max:2048'],
            'title_position' => ['required', Rule::in(array_keys(Banner::TITLE_POSITIONS))],
            'button_text' => ['nullable', 'string', 'max:100'],
            'button_url' => ['nullable', 'url', 'max:500'],
            'button_color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'status' => ['sometimes', 'boolean'],
            'priority' => ['nullable', 'integer', 'min:0'],
        ]) + ['status' => $request->boolean('status')];
    }
}
