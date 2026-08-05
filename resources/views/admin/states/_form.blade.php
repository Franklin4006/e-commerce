@if ($errors->any())
    <div class="alert alert-error">
        @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif

<div class="form-group">
    <label for="name" class="form-label">State Name</label>
    <input id="name" type="text" name="name" value="{{ old('name', $state->name ?? '') }}" required class="form-control">
    @error('name') <span class="field-error">{{ $message }}</span> @enderror
</div>

<div class="form-group">
    <label for="shipping_charge" class="form-label">Shipping Charge (₹)</label>
    <input id="shipping_charge" type="number" step="0.01" min="0" name="shipping_charge" value="{{ old('shipping_charge', $state->shipping_charge ?? '') }}" required class="form-control">
    @error('shipping_charge') <span class="field-error">{{ $message }}</span> @enderror
    <small style="color: var(--text-muted);">Added to every order shipped to this state, on top of the order-value based charge.</small>
</div>
