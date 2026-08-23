<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        // Only include quick switcher accounts in local/development environment
        $demoUsers = app()->environment('production') 
            ? collect() 
            : User::select('id', 'name', 'email', 'role')->get();

        return inertia('Auth/Login', [
            'demoUsers' => $demoUsers,
        ]);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return $this->redirectBasedOnRole(Auth::user());
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function switchUser(User $user, Request $request)
    {
        if (app()->environment('production')) {
            abort(403, 'Quick user switching is disabled in production.');
        }

        $currentUser = Auth::user();
        if (!$currentUser || (!$currentUser->isTeacher() && !$currentUser->isAdmin())) {
            abort(403, 'Unauthorized user switch attempt.');
        }

        // If switching user manually via quick switcher while impersonating, clear impersonator session
        $request->session()->forget('impersonator_id');
        Auth::login($user);
        $request->session()->regenerate();

        return $this->redirectBasedOnRole($user);
    }

    public function impersonate(User $user, Request $request)
    {
        $currentUser = Auth::user();
        if (!$currentUser || (!$currentUser->isTeacher() && !$currentUser->isAdmin())) {
            abort(403, 'Only teachers or administrators can log in as students.');
        }

        // Store original teacher ID if not already impersonating
        if (!$request->session()->has('impersonator_id')) {
            $request->session()->put('impersonator_id', $currentUser->id);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return Inertia::location(route('student.dashboard'));
    }

    public function stopImpersonating(Request $request)
    {
        if ($request->session()->has('impersonator_id')) {
            $impersonatorId = $request->session()->get('impersonator_id');
            $originalUser = User::find($impersonatorId);
            $request->session()->forget('impersonator_id');

            if ($originalUser) {
                Auth::login($originalUser);
                $request->session()->regenerate();
                return $this->redirectBasedOnRole($originalUser);
            }
        }

        return Inertia::location(route('login'));
    }

    public function logout(Request $request)
    {
        $request->session()->forget('impersonator_id');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Inertia::location(route('login'));
    }

    protected function redirectBasedOnRole(User $user)
    {
        $targetUrl = route('student.dashboard');
        if ($user->isAdmin()) {
            $targetUrl = route('admin.dashboard');
        } elseif ($user->isTeacher()) {
            $targetUrl = route('teacher.dashboard');
        }

        return Inertia::location($targetUrl);
    }
}

