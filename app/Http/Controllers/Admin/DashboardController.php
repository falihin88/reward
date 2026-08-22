<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\PointTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_students' => User::where('role', 'student')->count(),
            'total_teachers' => User::where('role', 'teacher')->count(),
            'total_points_in_circulation' => User::where('role', 'student')->sum('points'),
            'total_points_earned_lifetime' => User::where('role', 'student')->sum('total_points_earned'),
            'total_cards' => Card::count(),
            'total_cards_unlocked' => \DB::table('user_cards')->count(),
        ];

        $recentTransactions = PointTransaction::with(['user', 'teacher'])
            ->latest()
            ->take(10)
            ->get();

        $topStudents = User::where('role', 'student')
            ->orderBy('total_points_earned', 'desc')
            ->take(5)
            ->get();

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'recentTransactions' => $recentTransactions,
            'topStudents' => $topStudents,
        ]);
    }
}
