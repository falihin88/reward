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

                $requestedTenantId = session('active_tenant_id')
                    ?? $request->header('X-Tenant-ID')
                    ?? $request->query('tenant_id')
                    ?? $request->input('tenant_id');

                if ($requestedTenantId) {
                    $candidate = Tenant::where('is_active', true)->find($requestedTenantId);
                    if ($candidate && $user->canAccessTenant($candidate)) {
                        $tenant = $candidate;
                    }
                }

                if (!$tenant && $user->tenant_id) {
                    $tenant = Tenant::where('is_active', true)->find($user->tenant_id);
                }
            }

            // 2. Fallback resolution for unauthenticated API requests or default tenant
            if (!$tenant) {
                $requestedTenantId = session('active_tenant_id')
                    ?? $request->header('X-Tenant-ID')
                    ?? $request->query('tenant_id')
                    ?? $request->input('tenant_id');

                if ($requestedTenantId) {
                    $tenant = Tenant::where('is_active', true)->find($requestedTenantId);
                }

                if (!$tenant && $request->header('X-Tenant-Slug')) {
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
