<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\PointTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StudentDetailController extends Controller
{
    public function show(User $student, Request $request)
    {
        if (!$student->isStudent()) {
            abort(404, 'User is not a student.');
        }

        $student->load(['teacher']);

        $unlockedCardsMap = $student->cards()
            ->get()
            ->keyBy('id');

        $cards = Card::where('is_active', true)
            ->orderBy('order', 'asc')
            ->get()
            ->map(function ($card) use ($unlockedCardsMap) {
                $isUnlocked = $unlockedCardsMap->has($card->id);
                $unlockedAt = $isUnlocked ? $unlockedCardsMap->get($card->id)->pivot->unlocked_at : null;

                return [
                    'id' => $card->id,
                    'name' => $card->name,
                    'title' => $card->title,
                    'era' => $card->era,
                    'rarity' => $card->rarity,
                    'unlock_cost' => $card->unlock_cost,
                    'bio' => $card->bio,
                    'quote' => $card->quote,
                    'accent_color' => $card->accent_color,
                    'image_url' => $card->image_url,
                    'is_unlocked' => $isUnlocked,
                    'unlocked_at' => $unlockedAt,
                ];
            });

        $transactions = PointTransaction::with('teacher')
            ->where('user_id', $student->id)
            ->latest()
            ->paginate(15);

        return Inertia::render('Teacher/StudentDetail', [
            'student' => $student,
            'cards' => $cards,
            'transactions' => $transactions,
            'unlockedCount' => $unlockedCardsMap->count(),
            'totalCardsCount' => $cards->count(),
        ]);
    }
}
