<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\PointTransaction;
use App\Services\PointService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request, PointService $pointService)
    {
        $user = $request->user();

        // Check if daily streak check-in action requested or automatic
        $recentTransactions = PointTransaction::with('teacher')
            ->where('user_id', $user->id)
            ->latest()
            ->take(6)
            ->get();

        $unlockedCardsCount = $user->cards()->count();
        $totalActiveCardsCount = Card::where('is_active', true)->count();

        // Find next card user can afford or unlock next
        $unlockedCardIds = $user->cards()->pluck('cards.id');
        $nextCard = Card::where('is_active', true)
            ->whereNotIn('id', $unlockedCardIds)
            ->orderBy('unlock_cost', 'asc')
            ->first();

        return Inertia::render('Student/Dashboard', [
            'recentTransactions' => $recentTransactions,
            'unlockedCardsCount' => $unlockedCardsCount,
            'totalActiveCardsCount' => $totalActiveCardsCount,
            'nextCard' => $nextCard,
        ]);
    }

    public function claimStreak(Request $request, PointService $pointService)
    {
        $user = $request->user();
        $result = $pointService->checkAndAwardDailyStreak($user);

        if ($result['awarded']) {
            return back()->with('success', $result['message'])->with('streak_awarded', $result);
        } else {
            return back()->with('error', $result['message']);
        }
    }
}
