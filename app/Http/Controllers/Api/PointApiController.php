<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PointTransaction;
use App\Models\User;
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

        $teacher = $request->user() ?? User::where('role', 'teacher')->first() ?? User::first();
        $student = User::where('role', 'student')->findOrFail($request->student_id);

        try {
            $reason = $request->reason ?? ($request->points > 0 ? 'teacher_award' : 'teacher_deduction');
            $transaction = $pointService->awardOrDeductPointsByTeacher(
                $student,
                $teacher ?? $student,
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
        $tenantId = $request->query('tenant_id');

        $query = PointTransaction::with('user:id,name,email')->latest();

        if ($tenantId) {
            $query->whereHas('user', function ($q) use ($tenantId) {
                $q->where('tenant_id', $tenantId);
            });
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
