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
            // 1. Check Session override (e.g., when Admin switches active tenant)
            if (session()->has('active_tenant_id')) {
                $tenant = Tenant::where('is_active', true)->find(session('active_tenant_id'));
            }

            // 2. Check HTTP Headers
            if (!$tenant && $request->hasHeader('X-Tenant-ID')) {
                $tenant = Tenant::where('is_active', true)->find($request->header('X-Tenant-ID'));
            }
            if (!$tenant && $request->hasHeader('X-Tenant-Slug')) {
                $tenant = Tenant::where('is_active', true)->where('slug', $request->header('X-Tenant-Slug'))->first();
            }

            // 3. Check Subdomain / Route
            if (!$tenant && $request->route('tenant_slug')) {
                $tenant = Tenant::where('is_active', true)->where('slug', $request->route('tenant_slug'))->first();
            }

            // 4. Fallback to Authenticated User's tenant
            if (!$tenant && auth()->check() && auth()->user()->tenant_id) {
                $tenant = Tenant::where('is_active', true)->find(auth()->user()->tenant_id);
            }

            // 5. Default fallback to first active tenant if none identified
            if (!$tenant) {
                $tenant = Tenant::where('is_active', true)->first();
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
