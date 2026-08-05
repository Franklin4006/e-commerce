@if ($errors->any())
    <div class="alert alert-error">
        @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif

<div class="form-group">
    <label for="min_amount" class="form-label">Order Value From (₹)</label>
    <input id="min_amount" type="number" step="0.01" min="0" name="min_amount" value="{{ old('min_amount', $shippingSlab->min_amount ?? '') }}" required class="form-control">
    @error('min_amount') <span class="field-error">{{ $message }}</span> @enderror
</div>

<div class="form-group">
    <label for="max_amount" class="form-label">Order Value To (₹)</label>
    <input id="max_amount" type="number" step="0.01" min="0" name="max_amount" value="{{ old('max_amount', $shippingSlab->max_amount ?? '') }}" class="form-control">
    @error('max_amount') <span class="field-error">{{ $message }}</span> @enderror
    <small style="color: var(--text-muted);">Leave blank for no upper limit (e.g. the top tier).</small>
</div>

<div class="form-group">
    <label for="shipping_charge" class="form-label">Shipping Charge (₹)</label>
    <input id="shipping_charge" type="number" step="0.01" min="0" name="shipping_charge" value="{{ old('shipping_charge', $shippingSlab->shipping_charge ?? '') }}" required class="form-control">
    @error('shipping_charge') <span class="field-error">{{ $message }}</span> @enderror
    <small style="color: var(--text-muted);">Added on top of the destination state's shipping charge for orders in this value range.</small>
</div>
