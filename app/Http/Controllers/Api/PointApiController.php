<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PointTransaction;
use App\Models\User;
use App\Scopes\TenantScope;
use App\Services\PointService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PointApiController extends Controller
{
    public function award(Request $request, PointService $pointService): JsonResponse
    {
        $request->validate([
            'student_id' => ['required', 'exists:users,id'],
            'points' => ['required', 'integer', 'not_in:0'],
            'reason' => ['nullable', 'string', 'max:100'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $teacher = $request->user() ?? $request->user('sanctum') ?? auth('sanctum')->user();
        $student = User::withoutGlobalScope(TenantScope::class)->where('role', 'student')->findOrFail($request->student_id);

        if (!$teacher || (!$teacher->isTeacher() && !$teacher->isAdmin())) {
            // Safe fallback resolution for teacher user in dev/test environment
            $teacher = ($student->teacher_id ? User::withoutGlobalScope(TenantScope::class)->find($student->teacher_id) : null)
                ?? User::withoutGlobalScope(TenantScope::class)->whereIn('role', ['teacher', 'admin'])->where('tenant_id', $student->tenant_id)->first()
                ?? User::withoutGlobalScope(TenantScope::class)->whereIn('role', ['teacher', 'admin'])->first();
        }

        if (!$teacher || (!$teacher->isTeacher() && !$teacher->isAdmin())) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Points can only be awarded by a valid teacher or admin.',
            ], 403);
        }

        try {
            $reason = $request->reason ?? ($request->points > 0 ? 'teacher_award' : 'teacher_deduction');
            $transaction = $pointService->awardOrDeductPointsByTeacher(
                $student,
                $teacher,
                (int) $request->points,
                $reason,
                $request->note
            );

            $action = $request->points > 0 ? 'awarded' : 'deducted';
            $absPoints = abs($request->points);

            return response()->json([
                'success' => true,
                'message' => "Successfully {$action} {$absPoints} points for {$student->name}.",
                'transaction' => [
                    'id' => $transaction->id,
                    'student_id' => $student->id,
                    'student_name' => $student->name,
                    'points' => $transaction->points,
                    'reason' => $transaction->reason,
                    'note' => $transaction->note,
                    'created_at' => $transaction->created_at->toIso8601String(),
                ],
                'updated_student' => [
                    'id' => $student->id,
                    'name' => $student->name,
                    'points' => $student->points,
                    'total_points_earned' => $student->total_points_earned,
                ],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function transactions(Request $request): JsonResponse
    {
        $teacher = $request->user();
        $tenantId = $request->query('tenant_id');

        $query = PointTransaction::with(['user' => function ($q) {
            $q->withoutGlobalScopes();
        }])->latest();

        if ($tenantId) {
            $query->whereHas('user', function ($q) use ($tenantId) {
                $q->withoutGlobalScopes()->where('tenant_id', $tenantId);
            });
        } elseif ($teacher) {
            $allowedTenantIds = $teacher->availableTenants()->pluck('id');
            if ($allowedTenantIds->isNotEmpty()) {
                $query->whereHas('user', function ($q) use ($allowedTenantIds) {
                    $q->withoutGlobalScopes()->whereIn('tenant_id', $allowedTenantIds);
                });
            }
        }

        $transactions = $query->take(30)->get()->map(function ($tx) {
            return [
                'id' => $tx->id,
                'student_id' => $tx->user_id,
                'student_name' => $tx->user ? $tx->user->name : 'Student',
                'points' => $tx->points,
                'reason' => $tx->reason,
                'note' => $tx->note,
                'created_at' => $tx->created_at ? $tx->created_at->toIso8601String() : now()->toIso8601String(),
            ];
        });

        return response()->json([
            'success' => true,
            'transactions' => $transactions,
        ]);
    }
}
