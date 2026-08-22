<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\PointTransaction;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user()->load('teacher');

        $transactions = PointTransaction::with('teacher')
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(15);

        $teacherComments = PointTransaction::with('teacher')
            ->where('user_id', $user->id)
            ->whereNotNull('teacher_id')
            ->whereNotNull('note')
            ->latest()
            ->get();

        $unlockedCardsCount = $user->cards()->count();

        return Inertia::render('Student/Profile', [
            'student' => $user,
            'transactions' => $transactions,
            'teacherComments' => $teacherComments,
            'unlockedCardsCount' => $unlockedCardsCount,
        ]);
    }
}
