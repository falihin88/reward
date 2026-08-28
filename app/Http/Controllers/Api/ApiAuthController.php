<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ApiAuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::withoutGlobalScopes()->where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid login credentials.',
            ], 401);
        }

        if (!$user->isTeacher() && !$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Account is not a registered teacher or admin.',
            ], 403);
        }

        Auth::login($user);

        if ($user->tenant_id) {
            if ($request->hasSession()) {
                session(['active_tenant_id' => $user->tenant_id]);
            }
            $tenantObj = \App\Models\Tenant::find($user->tenant_id);
            if ($tenantObj) {
                app()->instance('tenant', $tenantObj);
            }
        }

        // Generate Sanctum token
        $token = $user->createToken('teacher-mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'tenant_id' => $user->tenant_id,
            ],
            'tenant' => $user->tenant ? [
                'id' => $user->tenant->id,
                'name' => $user->tenant->name,
                'code' => $user->tenant->code,
                'accent_color' => $user->tenant->accent_color,
            ] : null,
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user() ?? Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'tenant_id' => $user->tenant_id,
            ],
            'tenant' => $user->tenant ? [
                'id' => $user->tenant->id,
                'name' => $user->tenant->name,
                'code' => $user->tenant->code,
                'accent_color' => $user->tenant->accent_color,
            ] : null,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        if (Auth::check()) {
            Auth::logout();
        }

        return response()->json([
            'success' => true,
            'message' => 'Successfully logged out.',
        ]);
    }
}
