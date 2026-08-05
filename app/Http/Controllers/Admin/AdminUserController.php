<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AdminRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function index(Request $request): View
    {
        $adminUsers = User::where('is_admin', true)
            ->when($request->filled('search'), fn ($query) => $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->input('search').'%')
                    ->orWhere('email', 'like', '%'.$request->input('search').'%');
            }))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.admin-users.index', compact('adminUsers'));
    }

    public function create(): View
    {
        return view('admin.admin-users.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateAdminUser($request);

        $adminUser = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => $validated['password'],
            'role' => $validated['role'],
            'is_admin' => true,
        ]);

        $adminUser->forceFill(['email_verified_at' => now()])->save();

        return redirect()->route('admin.admin-users.index')->with('status', 'Admin user created successfully.');
    }

    public function edit(User $adminUser): View
    {
        abort_if(! $adminUser->is_admin, 404);

        return view('admin.admin-users.edit', compact('adminUser'));
    }

    public function update(Request $request, User $adminUser): RedirectResponse
    {
        abort_if(! $adminUser->is_admin, 404);

        $validated = $this->validateAdminUser($request, $adminUser);

        if (! $request->filled('password')) {
            unset($validated['password']);
        }

        if ($adminUser->role === AdminRole::SuperAdmin
            && $validated['role'] !== AdminRole::SuperAdmin->value
            && ! $this->hasAnotherSuperAdmin($adminUser)) {
            return back()->withErrors(['role' => 'You cannot change the role of the last remaining Super Admin.'])->withInput();
        }

        $adminUser->update($validated);

        return redirect()->route('admin.admin-users.edit', $adminUser)->with('status', 'Admin user updated successfully.');
    }

    public function destroy(User $adminUser): RedirectResponse
    {
        abort_if(! $adminUser->is_admin, 404);
        abort_if($adminUser->id === auth()->id(), 403, 'You cannot delete your own account.');

        if ($adminUser->role === AdminRole::SuperAdmin && ! $this->hasAnotherSuperAdmin($adminUser)) {
            return back()->withErrors(['role' => 'You cannot delete the last remaining Super Admin.']);
        }

        $adminUser->delete();

        return redirect()->route('admin.admin-users.index')->with('status', 'Admin user deleted successfully.');
    }

    private function hasAnotherSuperAdmin(User $exclude): bool
    {
        return User::where('is_admin', true)
            ->where('role', AdminRole::SuperAdmin->value)
            ->where('id', '!=', $exclude->id)
            ->exists();
    }

    private function validateAdminUser(Request $request, ?User $adminUser = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($adminUser)],
            'phone' => ['nullable', 'string', 'max:20'],
            'role' => ['required', Rule::in(array_column(AdminRole::cases(), 'value'))],
            'password' => [$adminUser ? 'nullable' : 'required', 'confirmed', Password::defaults()],
        ]);
    }
}
