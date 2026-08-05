@if ($errors->any())
    <div class="alert alert-error">
        @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif

<div class="form-group">
    <label for="label" class="form-label">Label</label>
    <input id="label" type="text" name="label" value="{{ old('label', $address->label ?? '') }}" placeholder="Home, Work, etc." required class="form-control">
</div>

<div class="form-group">
    <label for="name" class="form-label">Recipient Name</label>
    <input id="name" type="text" name="name" value="{{ old('name', $address->name ?? '') }}" required class="form-control">
</div>

<div class="form-group">
    <label for="phone" class="form-label">Phone</label>
    <input id="phone" type="text" name="phone" value="{{ old('phone', $address->phone ?? '') }}" required class="form-control">
</div>

<div class="form-group">
    <label for="address_line1" class="form-label">Address Line 1</label>
    <input id="address_line1" type="text" name="address_line1" value="{{ old('address_line1', $address->address_line1 ?? '') }}" required class="form-control">
</div>

<div class="form-group">
    <label for="address_line2" class="form-label">Address Line 2</label>
    <input id="address_line2" type="text" name="address_line2" value="{{ old('address_line2', $address->address_line2 ?? '') }}" class="form-control">
</div>

<div class="form-row">
    <div class="form-group">
        <label for="city" class="form-label">City</label>
        <input id="city" type="text" name="city" value="{{ old('city', $address->city ?? '') }}" required class="form-control">
    </div>

    <div class="form-group">
        <label for="state" class="form-label">State</label>
        <select id="state" name="state" required class="form-control">
            <option value="">Select a state</option>
            @foreach ($states as $stateOption)
                <option value="{{ $stateOption->name }}" {{ old('state', $address->state ?? '') === $stateOption->name ? 'selected' : '' }}>{{ $stateOption->name }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="form-row">
    <div class="form-group">
        <label for="postal_code" class="form-label">Postal Code</label>
        <input id="postal_code" type="text" name="postal_code" value="{{ old('postal_code', $address->postal_code ?? '') }}" required class="form-control">
    </div>

    <div class="form-group">
        <label for="country" class="form-label">Country</label>
        <input id="country" type="text" name="country" value="{{ old('country', $address->country ?? '') }}" required class="form-control">
    </div>
</div>

<div class="form-group form-check">
    <input id="is_default" type="checkbox" name="is_default" value="1" {{ old('is_default', $address->is_default ?? false) ? 'checked' : '' }}>
    <label for="is_default">Set as default address</label>
</div>
