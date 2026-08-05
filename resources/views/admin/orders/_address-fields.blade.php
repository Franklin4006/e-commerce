<div class="form-group">
    <label for="{{ $prefix }}_label" class="form-label">Label</label>
    <input id="{{ $prefix }}_label" type="text" name="{{ $prefix }}_label" placeholder="Home, Work, etc." class="form-control">
</div>

<div class="form-group">
    <label for="{{ $prefix }}_name" class="form-label">Recipient Name</label>
    <input id="{{ $prefix }}_name" type="text" name="{{ $prefix }}_name" class="form-control">
</div>

<div class="form-group">
    <label for="{{ $prefix }}_phone" class="form-label">Phone</label>
    <input id="{{ $prefix }}_phone" type="text" name="{{ $prefix }}_phone" class="form-control">
</div>

<div class="form-group">
    <label for="{{ $prefix }}_address_line1" class="form-label">Address Line 1</label>
    <input id="{{ $prefix }}_address_line1" type="text" name="{{ $prefix }}_address_line1" class="form-control">
</div>

<div class="form-group">
    <label for="{{ $prefix }}_address_line2" class="form-label">Address Line 2</label>
    <input id="{{ $prefix }}_address_line2" type="text" name="{{ $prefix }}_address_line2" class="form-control">
</div>

<div class="form-row">
    <div class="form-group">
        <label for="{{ $prefix }}_city" class="form-label">City</label>
        <input id="{{ $prefix }}_city" type="text" name="{{ $prefix }}_city" class="form-control">
    </div>

    <div class="form-group">
        <label for="{{ $prefix }}_state" class="form-label">State</label>
        <select id="{{ $prefix }}_state" name="{{ $prefix }}_state" class="form-control">
            <option value="">Select a state</option>
            @foreach ($states as $stateOption)
                <option value="{{ $stateOption->name }}">{{ $stateOption->name }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="form-row">
    <div class="form-group">
        <label for="{{ $prefix }}_postal_code" class="form-label">Postal Code</label>
        <input id="{{ $prefix }}_postal_code" type="text" name="{{ $prefix }}_postal_code" class="form-control">
    </div>

    <div class="form-group">
        <label for="{{ $prefix }}_country" class="form-label">Country</label>
        <input id="{{ $prefix }}_country" type="text" name="{{ $prefix }}_country" class="form-control">
    </div>
</div>
