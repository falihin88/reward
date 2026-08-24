<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class TenantController extends Controller
{
    public function index()
    {
        $tenants = Tenant::withCount(['users', 'cards', 'attendances'])->latest()->get();

        return Inertia::render('SuperAdmin/Tenants/Index', [
            'tenants' => $tenants,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:tenants,slug',
            'code' => 'required|string|max:50|unique:tenants,code',
            'domain' => 'nullable|string|max:255|unique:tenants,domain',
            'accent_color' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }
        $validated['accent_color'] = $validated['accent_color'] ?? '#f59e0b';

        $tenant = Tenant::create($validated);

        return back()->with('success', "Tenant organization '{$tenant->name}' created successfully!");
    }

    public function update(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:tenants,slug,' . $tenant->id,
            'code' => 'required|string|max:50|unique:tenants,code,' . $tenant->id,
            'domain' => 'nullable|string|max:255|unique:tenants,domain,' . $tenant->id,
            'accent_color' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        $tenant->update($validated);

        return back()->with('success', "Tenant organization '{$tenant->name}' updated successfully!");
    }

    public function destroy(Tenant $tenant)
    {
        $name = $tenant->name;
        $tenant->delete();

        return back()->with('success', "Tenant '{$name}' deleted successfully.");
    }

    public function switch(Tenant $tenant)
    {
        if (!auth()->user()?->canAccessTenant($tenant)) {
            abort(403, 'You do not have permission to access this campus.');
        }

        if (!$tenant->is_active) {
            return back()->with('error', "Tenant '{$tenant->name}' is inactive.");
        }

        session(['active_tenant_id' => $tenant->id]);
        app()->instance('tenant', $tenant);

        return back()->with('success', "Switched active tenant organization to '{$tenant->name}'!");
    }
}
