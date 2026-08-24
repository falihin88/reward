<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IdentifyTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = null;

        try {
            // 1. Check if user is authenticated
            if (auth()->check()) {
                $user = auth()->user();

                if ($user->isAdmin()) {
                    // Admins can switch active campus via session, header, or default to their assigned tenant
                    if (session()->has('active_tenant_id')) {
                        $tenant = Tenant::where('is_active', true)->find(session('active_tenant_id'));
                    }
                    if (!$tenant && $request->hasHeader('X-Tenant-ID')) {
                        $tenant = Tenant::where('is_active', true)->find($request->header('X-Tenant-ID'));
                    }
                    if (!$tenant && $user->tenant_id) {
                        $tenant = Tenant::where('is_active', true)->find($user->tenant_id);
                    }
                } else {
                    // Teachers & Students are strictly locked to their assigned campus/tenant
                    if ($user->tenant_id) {
                        $tenant = Tenant::where('is_active', true)->find($user->tenant_id);
                    }
                }
            }

            // 2. Unauthenticated requests or fallback resolution
            if (!$tenant) {
                if (session()->has('active_tenant_id')) {
                    $tenant = Tenant::where('is_active', true)->find(session('active_tenant_id'));
                }
                if (!$tenant && $request->hasHeader('X-Tenant-ID')) {
                    $tenant = Tenant::where('is_active', true)->find($request->header('X-Tenant-ID'));
                }
                if (!$tenant && $request->hasHeader('X-Tenant-Slug')) {
                    $tenant = Tenant::where('is_active', true)->where('slug', $request->header('X-Tenant-Slug'))->first();
                }
                if (!$tenant && $request->route('tenant_slug')) {
                    $tenant = Tenant::where('is_active', true)->where('slug', $request->route('tenant_slug'))->first();
                }
                if (!$tenant) {
                    $tenant = Tenant::where('is_active', true)->first();
                }
            }

            if ($tenant) {
                app()->instance('tenant', $tenant);
                session(['active_tenant_id' => $tenant->id]);
            }
        } catch (\Throwable $e) {
            // Gracefully ignore database exceptions during initial boot/migrations
        }

        return $next($request);
    }
}
