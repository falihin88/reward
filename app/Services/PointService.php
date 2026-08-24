<?php

namespace App\Services;

use App\Models\Card;
use App\Models\PointTransaction;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Exception;

class PointService
{
    public function checkAndAwardDailyStreak(User $user): array
    {
        $today = Carbon::today();
        $lastDate = $user->last_activity_date ? Carbon::parse($user->last_activity_date) : null;

        // Already claimed today
        if ($lastDate && $lastDate->isToday()) {
            return [
                'awarded' => false,
                'streak' => $user->current_streak,
                'points' => 0,
                'message' => 'Daily streak already claimed today!',
            ];
        }

        // Check if yesterday
        if ($lastDate && $lastDate->isYesterday()) {
            $user->current_streak += 1;
        } else {
            $user->current_streak = 1;
        }

        $streakBonus = (int) Setting::getValue('points_daily_streak', 15);

        $user->points += $streakBonus;
        $user->total_points_earned += $streakBonus;
        $user->last_activity_date = $today->toDateString();
        $user->save();

        PointTransaction::create([
            'user_id' => $user->id,
            'points' => $streakBonus,
            'reason' => 'daily_streak',
            'note' => "Daily streak check-in (Day {$user->current_streak})",
        ]);

        return [
            'awarded' => true,
            'streak' => $user->current_streak,
            'points' => $streakBonus,
            'message' => "Awesome! You earned {$streakBonus} points for your Day {$user->current_streak} streak!",
        ];
    }

    public function awardOrDeductPointsByTeacher(User $student, User $teacher, int $points, string $reason = 'teacher_award', ?string $note = null): PointTransaction
    {
        if ($points === 0) {
            throw new Exception('Point value cannot be zero.');
        }

        $student->points = max(0, $student->points + $points);

        if ($points > 0) {
            $student->total_points_earned += $points;
        }

        $student->save();

        return PointTransaction::create([
            'tenant_id' => $student->tenant_id ?? $teacher->tenant_id,
            'user_id' => $student->id,
            'teacher_id' => $teacher->id,
            'points' => $points,
            'reason' => $reason,
            'note' => $note,
        ]);
    }

    public function unlockCard(User $user, Card $card): array
    {
        if (!$card->is_active) {
            throw new Exception('This card is currently not available.');
        }

        if ($user->cards()->where('card_id', $card->id)->exists()) {
            throw new Exception('You have already unlocked this card!');
        }

        if ($user->points < $card->unlock_cost) {
            throw new Exception("Insufficient points! You need {$card->unlock_cost} points to unlock {$card->name}.");
        }

        $user->points -= $card->unlock_cost;
        $user->save();

        $user->cards()->attach($card->id, ['unlocked_at' => now()]);

        PointTransaction::create([
            'user_id' => $user->id,
            'points' => -$card->unlock_cost,
            'reason' => 'card_unlock',
            'note' => "Unlocked Card: {$card->name} ({$card->rarity})",
        ]);

        return [
            'success' => true,
            'card' => $card,
            'remaining_points' => $user->points,
            'message' => "Congratulations! You unlocked {$card->name}!",
        ];
    }
}
