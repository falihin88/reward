<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_can_save_attendance_and_award_points(): void
    {
        $tenant = Tenant::create([
            'name' => 'Test Campus',
            'slug' => 'test-campus',
            'code' => 'TEST-01',
            'is_active' => true,
        ]);
        app()->instance('tenant', $tenant);

        $teacher = User::factory()->create([
            'tenant_id' => $tenant->id,
            'role' => 'teacher',
        ]);

        $student = User::factory()->create([
            'tenant_id' => $tenant->id,
            'role' => 'student',
            'teacher_id' => $teacher->id,
            'points' => 0,
        ]);

        $response = $this->actingAs($teacher)->post(route('teacher.attendance.store'), [
            'date' => now()->toDateString(),
            'records' => [
                [
                    'student_id' => $student->id,
                    'status' => 'present',
                    'notes' => 'On time',
                ],
            ],
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertEquals(100, $student->fresh()->points);
        $this->assertDatabaseHas('attendances', [
            'tenant_id' => $tenant->id,
            'student_id' => $student->id,
            'teacher_id' => $teacher->id,
            'status' => 'present',
            'points_awarded' => 100,
        ]);
    }

    public function test_updating_attendance_adjusts_student_points_correctly(): void
    {
        $tenant = Tenant::create([
            'name' => 'Test Campus 2',
            'slug' => 'test-campus-2',
            'code' => 'TEST-02',
            'is_active' => true,
        ]);
        app()->instance('tenant', $tenant);

        $teacher = User::factory()->create([
            'tenant_id' => $tenant->id,
            'role' => 'teacher',
        ]);

        $student = User::factory()->create([
            'tenant_id' => $tenant->id,
            'role' => 'student',
            'teacher_id' => $teacher->id,
            'points' => 0,
        ]);

        $today = now()->toDateString();

        // 1. Mark Present (+100 Pts)
        $this->actingAs($teacher)->post(route('teacher.attendance.store'), [
            'date' => $today,
            'records' => [
                ['student_id' => $student->id, 'status' => 'present', 'notes' => ''],
            ],
        ]);
        $this->assertEquals(100, $student->fresh()->points);

        // 2. Change to Late (+50 Pts -> Net -50 Adjustment)
        $this->actingAs($teacher)->post(route('teacher.attendance.store'), [
            'date' => $today,
            'records' => [
                ['student_id' => $student->id, 'status' => 'late', 'notes' => 'Arrived 10 mins late'],
            ],
        ]);

        $this->assertEquals(50, $student->fresh()->points);
        $this->assertDatabaseHas('attendances', [
            'student_id' => $student->id,
            'status' => 'late',
            'points_awarded' => 50,
        ]);
    }
}
