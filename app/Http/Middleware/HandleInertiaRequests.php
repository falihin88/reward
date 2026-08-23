<?php

namespace App\Http\Middleware;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();
        $impersonatorId = $request->session()->get('impersonator_id');
        $impersonator = $impersonatorId ? User::find($impersonatorId) : null;

        $tenant = app()->bound('tenant') ? app('tenant') : null;
        $availableTenants = ($user && in_array($user->role, ['admin', 'teacher']))
            ? \App\Models\Tenant::where('is_active', true)->get(['id', 'name', 'slug', 'code', 'accent_color', 'logo_url'])
            : [];

        return array_merge(parent::share($request), [
            'tenant' => $tenant ? [
                'id' => $tenant->id,
                'name' => $tenant->name,
                'slug' => $tenant->slug,
                'code' => $tenant->code,
                'logo_url' => $tenant->logo_url,
                'accent_color' => $tenant->accent_color,
            ] : null,
            'availableTenants' => $availableTenants,
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'tenant_id' => $user->tenant_id,
                    'points' => $user->points,
                    'total_points_earned' => $user->total_points_earned,
                    'current_streak' => $user->current_streak,
                    'last_activity_date' => $user->last_activity_date ? $user->last_activity_date->toDateString() : null,
                    'teacher' => $user->teacher ? [
                        'id' => $user->teacher->id,
                        'name' => $user->teacher->name,
                    ] : null,
                ] : null,
                'impersonator' => $impersonator ? [
                    'id' => $impersonator->id,
                    'name' => $impersonator->name,
                    'email' => $impersonator->email,
                    'role' => $impersonator->role,
                ] : null,
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
                'streak_awarded' => fn () => $request->session()->get('streak_awarded'),
            ],
        ]);
    }
}
