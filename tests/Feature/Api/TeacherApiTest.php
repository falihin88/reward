<?php

namespace Tests\Feature\Api;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeacherApiTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;
    protected User $teacher;
    protected User $student;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::create([
            'name' => 'Main Campus',
            'slug' => 'main-campus',
            'code' => 'MAIN-01',
            'accent_color' => '#10B981',
            'is_active' => true,
        ]);

        $this->teacher = User::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Master Teacher',
            'email' => 'teacher@hikmah.edu',
            'password' => bcrypt('password123'),
            'role' => 'teacher',
        ]);

        $this->student = User::create([
            'tenant_id' => $this->tenant->id,
            'teacher_id' => $this->teacher->id,
            'name' => 'Student Al-Basri',
            'email' => 'student@hikmah.edu',
            'password' => bcrypt('password123'),
            'role' => 'student',
            'points' => 100,
            'total_points_earned' => 100,
            'current_streak' => 5,
        ]);
    }

    public function test_teacher_can_login_via_api(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'teacher@hikmah.edu',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'user' => [
                    'email' => 'teacher@hikmah.edu',
                    'role' => 'teacher',
                ],
            ]);
    }

    public function test_can_fetch_tenants_list(): void
    {
        $response = $this->getJson('/api/v1/tenants');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonFragment([
                'code' => 'MAIN-01',
            ]);
    }

    public function test_can_fetch_students_list(): void
    {
        $response = $this->getJson('/api/v1/students?tenant_id=' . $this->tenant->id);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonFragment([
                'name' => 'Student Al-Basri',
            ]);
    }

    public function test_can_record_attendance_and_award_points(): void
    {
        $response = $this->postJson('/api/v1/attendance', [
            'date' => now()->toDateString(),
            'records' => [
                [
                    'student_id' => $this->student->id,
                    'status' => 'present',
                    'notes' => 'On time for morning halaqah',
                ],
            ],
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'total_points_awarded' => 100,
            ]);

        $this->student->refresh();
        $this->assertEquals(200, $this->student->points);
    }

    public function test_can_award_points_to_student(): void
    {
        $response = $this->postJson('/api/v1/points/award', [
            'student_id' => $this->student->id,
            'points' => 50,
            'reason' => 'teacher_award',
            'note' => 'Great participation in class',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->student->refresh();
        $this->assertEquals(150, $this->student->points);
    }

    public function test_can_create_new_student_account(): void
    {
        $response = $this->postJson('/api/v1/students', [
            'name' => 'New Learner',
            'email' => 'newlearner@hikmah.edu',
            'password' => 'secret123',
            'points' => 50,
            'tenant_id' => $this->tenant->id,
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'student' => [
                    'name' => 'New Learner',
                    'points' => 50,
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'newlearner@hikmah.edu',
            'role' => 'student',
        ]);
    }
}
