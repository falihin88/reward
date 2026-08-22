<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Services\PointService;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $unlockedCardsMap = $user->cards()
            ->get()
            ->keyBy('id');

        $cards = Card::where('is_active', true)
            ->orderBy('order', 'asc')
            ->orderBy('unlock_cost', 'asc')
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

        return Inertia::render('Student/Cards/Index', [
            'cards' => $cards,
            'userPoints' => $user->points,
        ]);
    }

    public function unlock(Card $card, Request $request, PointService $pointService)
    {
        try {
            $result = $pointService->unlockCard($request->user(), $card);
            return back()->with('success', $result['message']);
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
