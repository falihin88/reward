<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['teacher', 'managedTenants'])
            ->orderBy('role')
            ->orderBy('name')
            ->paginate(15)
            ->through(fn ($user) => $user->setAttribute('managed_tenant_ids', $user->managedTenants->pluck('id')->values()->toArray()));

        $teachers = User::where('role', 'teacher')->get(['id', 'name']);
        $tenants = \App\Models\Tenant::orderBy('name')->get(['id', 'name', 'code']);

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'teachers' => $teachers,
            'tenants' => $tenants,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', 'in:admin,teacher,student'],
            'teacher_id' => ['nullable', 'exists:users,id'],
            'tenant_ids' => ['nullable', 'array'],
            'tenant_ids.*' => ['exists:tenants,id'],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $tenantIds = $request->input('tenant_ids', []);
        unset($validated['tenant_ids']);

        $user = User::create($validated);

        $user->managedTenants()->sync($user->isTeacher() ? $tenantIds : []);

        return back()->with('success', 'User created successfully.');
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role' => ['required', 'in:admin,teacher,student'],
            'teacher_id' => ['nullable', 'exists:users,id'],
            'tenant_ids' => ['nullable', 'array'],
            'tenant_ids.*' => ['exists:tenants,id'],
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        }

        $tenantIds = $request->input('tenant_ids', []);
        unset($validated['tenant_ids']);

        $user->update($validated);

        $user->managedTenants()->sync($user->isTeacher() ? $tenantIds : []);

        return back()->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete yourself.');
        }

        $user->delete();

        return back()->with('success', 'User deleted successfully.');
    }
}
