<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImpersonationTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_can_impersonate_student_and_restore_self(): void
    {
        $tenant = Tenant::create([
            'name' => 'Main Campus',
            'slug' => 'main-campus',
            'code' => 'MAIN-01',
            'is_active' => true,
        ]);

        $teacher = User::factory()->create(['tenant_id' => $tenant->id, 'role' => 'teacher']);
        $student = User::factory()->create([
            'tenant_id' => $tenant->id,
            'role' => 'student',
            'teacher_id' => $teacher->id,
        ]);

        $this->actingAs($teacher)->post(route('impersonate', $student));

        $this->assertEquals($student->id, auth()->id());
        $this->assertEquals($teacher->id, session('impersonator_id'));

        $this->post(route('impersonate.stop'));

        $this->assertEquals($teacher->id, auth()->id());
        $this->assertNull(session('impersonator_id'));
    }

    public function test_stop_impersonation_restores_teacher_across_tenants(): void
    {
        $tenantA = Tenant::create([
            'name' => 'Campus A',
            'slug' => 'campus-a',
            'code' => 'A-01',
            'is_active' => true,
        ]);
        $tenantB = Tenant::create([
            'name' => 'Campus B',
            'slug' => 'campus-b',
            'code' => 'B-01',
            'is_active' => true,
        ]);

        $teacher = User::factory()->create(['tenant_id' => $tenantA->id, 'role' => 'teacher']);
        $student = User::factory()->create(['tenant_id' => $tenantB->id, 'role' => 'student']);

        // Teacher is impersonating the student (who lives in a different tenant).
        $this->actingAs($student);
        session(['impersonator_id' => $teacher->id]);

        $this->post(route('impersonate.stop'));

        $this->assertEquals($teacher->id, auth()->id());
        $this->assertNull(session('impersonator_id'));
    }
}
