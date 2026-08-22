<?php

namespace Tests\Feature;

use App\Models\Card;
use App\Models\User;
use App\Services\PointService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GamificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_daily_streak_awarded_and_prevents_double_claim_on_same_day(): void
    {
        $student = User::factory()->create([
            'role' => 'student',
            'points' => 0,
            'current_streak' => 0,
            'last_activity_date' => null,
        ]);

        $service = new PointService();
        $result = $service->checkAndAwardDailyStreak($student);

        $this->assertTrue($result['awarded']);
        $this->assertEquals(1, $student->fresh()->current_streak);
        $this->assertEquals(15, $student->fresh()->points);

        // Attempting second claim on the same day should fail
        $secondResult = $service->checkAndAwardDailyStreak($student);
        $this->assertFalse($secondResult['awarded']);
        $this->assertEquals(15, $student->fresh()->points);
    }

    public function test_teacher_can_award_and_deduct_points_with_comment(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $student = User::factory()->create([
            'role' => 'student',
            'teacher_id' => $teacher->id,
            'points' => 50,
            'total_points_earned' => 50,
        ]);

        $service = new PointService();

        // Award points
        $tx = $service->awardOrDeductPointsByTeacher($student, $teacher, 25, 'teacher_award', 'Great job in class!');

        $this->assertEquals(75, $student->fresh()->points);
        $this->assertEquals(75, $student->fresh()->total_points_earned);
        $this->assertEquals('Great job in class!', $tx->note);

        // Deduct points
        $deductionTx = $service->awardOrDeductPointsByTeacher($student, $teacher, -10, 'teacher_deduction', 'Late assignment');

        $this->assertEquals(65, $student->fresh()->points);
        $this->assertEquals(75, $student->fresh()->total_points_earned); // Total lifetime points should NOT decrease
        $this->assertEquals('Late assignment', $deductionTx->note);
    }

    public function test_student_can_unlock_card_if_points_sufficient(): void
    {
        $student = User::factory()->create([
            'role' => 'student',
            'points' => 200,
        ]);

        $card = Card::create([
            'name' => 'Test Scholar',
            'title' => 'Title',
            'era' => 'Era',
            'rarity' => 'common',
            'unlock_cost' => 100,
            'bio' => 'Bio',
            'quote' => 'Quote',
            'is_active' => true,
        ]);

        $service = new PointService();
        $result = $service->unlockCard($student, $card);

        $this->assertTrue($result['success']);
        $this->assertEquals(100, $student->fresh()->points);
        $this->assertTrue($student->fresh()->cards()->where('card_id', $card->id)->exists());
    }

    public function test_unlock_fails_if_points_insufficient(): void
    {
        $student = User::factory()->create([
            'role' => 'student',
            'points' => 30,
        ]);

        $card = Card::create([
            'name' => 'Expensive Scholar',
            'title' => 'Title',
            'era' => 'Era',
            'rarity' => 'legendary',
            'unlock_cost' => 250,
            'bio' => 'Bio',
            'quote' => 'Quote',
            'is_active' => true,
        ]);

        $this->expectException(\Exception::class);

        $service = new PointService();
        $service->unlockCard($student, $card);
    }
}
