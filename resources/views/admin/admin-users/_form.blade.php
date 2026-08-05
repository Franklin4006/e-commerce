@if ($errors->any())
    <div class="alert alert-error">
        @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif

<div class="form-group">
    <label for="name" class="form-label">Name</label>
    <input id="name" type="text" name="name" value="{{ old('name', $adminUser->name ?? '') }}" required class="form-control">
</div>

<div class="form-group">
    <label for="email" class="form-label">Email</label>
    <input id="email" type="email" name="email" value="{{ old('email', $adminUser->email ?? '') }}" required class="form-control">
</div>

<div class="form-group">
    <label for="phone" class="form-label">Phone</label>
    <input id="phone" type="text" name="phone" value="{{ old('phone', $adminUser->phone ?? '') }}" class="form-control">
</div>

<div class="form-group">
    <label for="role" class="form-label">Role</label>
    <select id="role" name="role" required class="form-control">
        @foreach (\App\Enums\AdminRole::cases() as $roleOption)
            <option value="{{ $roleOption->value }}" {{ old('role', $adminUser->role?->value ?? '') === $roleOption->value ? 'selected' : '' }}>{{ $roleOption->label() }}</option>
        @endforeach
    </select>
    @error('role') <span class="field-error">{{ $message }}</span> @enderror
</div>

<div class="form-group">
    <label for="password" class="form-label">{{ isset($adminUser) ? 'New Password' : 'Password' }}</label>
    <input id="password" type="password" name="password" {{ isset($adminUser) ? '' : 'required' }} class="form-control">
    @if (isset($adminUser))
        <small style="color: var(--text-muted);">Leave blank to keep the current password.</small>
    @endif
</div>

<div class="form-group">
    <label for="password_confirmation" class="form-label">Confirm {{ isset($adminUser) ? 'New ' : '' }}Password</label>
    <input id="password_confirmation" type="password" name="password_confirmation" {{ isset($adminUser) ? '' : 'required' }} class="form-control">
</div>
