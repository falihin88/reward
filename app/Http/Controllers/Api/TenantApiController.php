<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TenantApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user) {
            $tenants = $user->availableTenants();
            $tenants->loadCount(['users as student_count' => function ($q) {
                $q->withoutGlobalScopes()->where('role', 'student');
            }]);
        } else {
            $tenants = Tenant::where('is_active', true)
                ->withCount(['users as student_count' => function ($q) {
                    $q->withoutGlobalScopes()->where('role', 'student');
                }])
                ->latest()
                ->get();
        }

        return response()->json([
            'success' => true,
            'tenants' => $tenants,
        ]);
    }

    public function switch(Request $request, Tenant $tenant): JsonResponse
    {
        $user = $request->user();

        if ($user && !$user->canAccessTenant($tenant)) {
            return response()->json([
                'success' => false,
                'message' => "You do not have permission to access campus '{$tenant->name}'.",
            ], 403);
        }

        if (!$tenant->is_active) {
            return response()->json([
                'success' => false,
                'message' => "Campus organization '{$tenant->name}' is currently inactive.",
            ], 400);
        }

        session(['active_tenant_id' => $tenant->id]);
        app()->instance('tenant', $tenant);

        return response()->json([
            'success' => true,
            'message' => "Switched active campus to '{$tenant->name}'!",
            'tenant' => [
                'id' => $tenant->id,
                'name' => $tenant->name,
                'code' => $tenant->code,
                'slug' => $tenant->slug,
                'accent_color' => $tenant->accent_color,
            ],
        ]);
    }
}
