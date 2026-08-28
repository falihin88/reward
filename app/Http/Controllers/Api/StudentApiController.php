<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Scopes\TenantScope;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StudentApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $teacher = $request->user();
        $tenantId = $request->query('tenant_id');
        $search = $request->query('search');

        $query = User::withoutGlobalScope(TenantScope::class)
            ->where('role', 'student')
            ->withCount('cards as unlocked_cards_count');

        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        } elseif ($teacher) {
            $allowedTenantIds = $teacher->availableTenants()->pluck('id');
            $query->where(function ($q) use ($teacher, $allowedTenantIds) {
                if ($allowedTenantIds->isNotEmpty()) {
                    $q->whereIn('tenant_id', $allowedTenantIds);
                }
                $q->orWhere('teacher_id', $teacher->id);
            });
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $students = $query->orderBy('name', 'asc')->get()->map(function ($s) {
            return [
                'id' => $s->id,
                'name' => $s->name,
                'email' => $s->email,
                'points' => (int) $s->points,
                'total_points_earned' => (int) $s->total_points_earned,
                'current_streak' => (int) $s->current_streak,
                'unlocked_cards_count' => (int) $s->unlocked_cards_count,
                'last_activity_date' => $s->last_activity_date ? $s->last_activity_date->toDateString() : null,
                'tenant_id' => $s->tenant_id,
            ];
        });

        return response()->json([
            'success' => true,
            'students' => $students,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $teacher = $request->user() ?? $request->user('sanctum') ?? auth('sanctum')->user();

        if (!$teacher || (!$teacher->isTeacher() && !$teacher->isAdmin())) {
            $teacher = User::withoutGlobalScope(TenantScope::class)->whereIn('role', ['teacher', 'admin'])->first();
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6'],
            'points' => ['nullable', 'integer', 'min:0'],
            'tenant_id' => ['nullable', 'exists:tenants,id'],
        ]);

        $initialPoints = $validated['points'] ?? 0;
        $tenantId = $validated['tenant_id'] ?? $teacher?->tenant_id;

        $student = User::create([
            'tenant_id' => $tenantId,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'student',
            'teacher_id' => $teacher?->id,
            'points' => $initialPoints,
            'total_points_earned' => $initialPoints,
            'current_streak' => 1,
            'last_activity_date' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Student account '{$student->name}' created successfully.",
            'student' => [
                'id' => $student->id,
                'name' => $student->name,
                'email' => $student->email,
                'points' => $student->points,
                'total_points_earned' => $student->total_points_earned,
                'current_streak' => $student->current_streak,
                'unlocked_cards_count' => 0,
                'tenant_id' => $student->tenant_id,
            ],
        ], 201);
    }

    public function show(User $student): JsonResponse
    {
        if ($student->role !== 'student') {
            return response()->json(['success' => false, 'message' => 'Target user is not a student.'], 403);
        }

        $student->loadCount('cards as unlocked_cards_count');
        $student->load(['cards', 'pointTransactions' => function ($q) {
            $q->latest()->take(10);
        }]);

        return response()->json([
            'success' => true,
            'student' => [
                'id' => $student->id,
                'name' => $student->name,
                'email' => $student->email,
                'points' => (int) $student->points,
                'total_points_earned' => (int) $student->total_points_earned,
                'current_streak' => (int) $student->current_streak,
                'unlocked_cards_count' => (int) $student->unlocked_cards_count,
                'last_activity_date' => $student->last_activity_date ? $student->last_activity_date->toDateString() : null,
                'tenant_id' => $student->tenant_id,
                'cards' => $student->cards,
                'recent_transactions' => $student->pointTransactions,
            ],
        ]);
    }

    public function update(Request $request, User $student): JsonResponse
    {
        if ($student->role !== 'student') {
            return response()->json(['success' => false, 'message' => 'Target user is not a student.'], 403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $student->id],
            'points' => ['required', 'integer', 'min:0'],
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => ['string', 'min:6']]);
            $validated['password'] = Hash::make($request->password);
        }

        $student->update($validated);

        return response()->json([
            'success' => true,
            'message' => "Student '{$student->name}' updated successfully.",
            'student' => $student,
        ]);
    }

    public function destroy(User $student): JsonResponse
    {
        if ($student->role !== 'student') {
            return response()->json(['success' => false, 'message' => 'Target user is not a student.'], 403);
        }

        $name = $student->name;
        $student->delete();

        return response()->json([
            'success' => true,
            'message' => "Student '{$name}' account deleted successfully.",
        ]);
    }
}
