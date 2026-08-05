<x-layouts.admin title="Add Customer">
    <h1 class="page-title">Add Customer</h1>

    <div class="max-w-form-lg card">
        <form method="POST" action="{{ route('admin.customers.store') }}">
            @csrf

            @if ($errors->any())
                <div class="alert alert-error">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <div class="form-group">
                <label for="name" class="form-label">Name</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required class="form-control">
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required class="form-control">
            </div>

            <div class="form-group">
                <label for="phone" class="form-label">Phone</label>
                <input id="phone" type="text" name="phone" value="{{ old('phone') }}" class="form-control">
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input id="password" type="password" name="password" required class="form-control">
            </div>

            <div class="form-group">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required class="form-control">
            </div>

            <small style="color: var(--text-muted); display: block; margin-bottom: 1rem;">
                The account is created as verified, so the customer can log in immediately with this password.
            </small>

            <div class="form-actions">
                @can('content-write')
                    <button type="submit" class="btn btn-primary">Save</button>
                @endcan
                <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</x-layouts.admin>
