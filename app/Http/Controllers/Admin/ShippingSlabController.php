<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingSlab;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class ShippingSlabController extends Controller
{
    public function index(): View
    {
        $shippingSlabs = ShippingSlab::query()->orderBy('min_amount')->paginate(15);

        return view('admin.shipping-slabs.index', compact('shippingSlabs'));
    }

    public function create(): View
    {
        return view('admin.shipping-slabs.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateSlab($request);

        ShippingSlab::create($validated);

        return redirect()->route('admin.shipping-slabs.index')->with('status', 'Shipping slab created successfully.');
    }

    public function edit(ShippingSlab $shippingSlab): View
    {
        return view('admin.shipping-slabs.edit', ['shippingSlab' => $shippingSlab]);
    }

    public function update(Request $request, ShippingSlab $shippingSlab): RedirectResponse
    {
        $validated = $this->validateSlab($request, $shippingSlab);

        $shippingSlab->update($validated);

        return redirect()->route('admin.shipping-slabs.index')->with('status', 'Shipping slab updated successfully.');
    }

    public function destroy(ShippingSlab $shippingSlab): RedirectResponse
    {
        $shippingSlab->delete();

        return redirect()->route('admin.shipping-slabs.index')->with('status', 'Shipping slab deleted successfully.');
    }

    private function validateSlab(Request $request, ?ShippingSlab $shippingSlab = null): array
    {
        $validator = Validator::make($request->all(), [
            'min_amount' => ['required', 'numeric', 'min:0'],
            'max_amount' => ['nullable', 'numeric', 'gt:min_amount'],
            'shipping_charge' => ['required', 'numeric', 'min:0'],
        ]);

        $validator->after(function ($validator) use ($request, $shippingSlab) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $minAmount = (float) $request->input('min_amount');
            $maxAmount = $request->filled('max_amount') ? (float) $request->input('max_amount') : null;

            $overlaps = ShippingSlab::query()
                ->when($shippingSlab, fn ($query) => $query->where('id', '!=', $shippingSlab->id))
                ->where(function ($query) use ($minAmount, $maxAmount) {
                    $query->where('min_amount', '<=', $maxAmount ?? PHP_FLOAT_MAX)
                        ->where(fn ($q) => $q->whereNull('max_amount')->orWhere('max_amount', '>=', $minAmount));
                })
                ->exists();

            if ($overlaps) {
                $validator->errors()->add('min_amount', 'This range overlaps with an existing shipping slab.');
            }
        });

        return $validator->validate();
    }
}
