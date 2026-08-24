<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use App\Services\PointService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttendanceApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $date = $request->query('date', now()->toDateString());
        $tenantId = $request->query('tenant_id');

        $query = User::where('role', 'student')->orderBy('name', 'asc');
        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        $students = $query->get(['id', 'name', 'email', 'points', 'current_streak', 'tenant_id']);

        $attendances = Attendance::where('date', $date)
            ->whereIn('student_id', $students->pluck('id'))
            ->get()
            ->keyBy('student_id');

        $records = $students->map(function ($student) use ($attendances, $date) {
            $att = $attendances->get($student->id);
            return [
                'student_id' => $student->id,
                'student_name' => $student->name,
                'date' => $date,
                'status' => $att ? $att->status : 'present',
                'points_awarded' => $att ? $att->points_awarded : 100,
                'notes' => $att ? $att->notes : null,
            ];
        });

        return response()->json([
            'success' => true,
            'date' => $date,
            'attendance' => $records,
        ]);
    }

    public function store(Request $request, PointService $pointService): JsonResponse
    {
        $request->validate([
            'date' => ['required', 'date'],
            'records' => ['required', 'array'],
            'records.*.student_id' => ['required', 'exists:users,id'],
            'records.*.status' => ['required', 'in:present,absent,late,excused'],
            'records.*.notes' => ['nullable', 'string', 'max:255'],
        ]);

        $teacher = $request->user() ?? User::where('role', 'teacher')->first() ?? User::first();
        $date = $request->date;
        $records = $request->records;

        $awardedCount = 0;
        $totalPointsAwarded = 0;

        foreach ($records as $item) {
            $studentId = $item['student_id'];
            $status = $item['status'];
            $notes = $item['notes'] ?? null;

            $targetPoints = match ($status) {
                'present' => 100,
                'late' => 50,
                default => 0,
            };

            $existing = Attendance::where('student_id', $studentId)
                ->where('date', $date)
                ->first();

            $prevPoints = $existing ? (int) $existing->points_awarded : 0;
            $pointsDiff = $targetPoints - $prevPoints;

            Attendance::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'date' => $date,
                ],
                [
                    'tenant_id' => $teacher?->tenant_id,
                    'teacher_id' => $teacher?->id,
                    'status' => $status,
                    'points_awarded' => $targetPoints,
                    'notes' => $notes,
                ]
            );

            if ($pointsDiff !== 0) {
                $student = User::find($studentId);
                if ($student) {
                    try {
                        $reason = match (true) {
                            $pointsDiff > 0 && $status === 'present' => 'attendance_present',
                            $pointsDiff > 0 && $status === 'late' => 'attendance_late',
                            $pointsDiff < 0 => 'attendance_deduction',
                            default => 'attendance_update',
                        };

                        $sign = $pointsDiff > 0 ? "+{$pointsDiff}" : "{$pointsDiff}";
                        $noteText = "Attendance on {$date} (" . strtoupper($status) . "): {$sign} Pts";

                        $pointService->awardOrDeductPointsByTeacher(
                            $student,
                            $teacher ?? $student,
                            $pointsDiff,
                            $reason,
                            $noteText
                        );

                        if ($pointsDiff > 0) {
                            $awardedCount++;
                            $totalPointsAwarded += $pointsDiff;
                        }
                    } catch (Exception $e) {
                        // Continue processing remaining student records
                    }
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Attendance saved for {$date}! Total +{$totalPointsAwarded} points awarded.",
            'total_points_awarded' => $totalPointsAwarded,
            'awarded_students_count' => $awardedCount,
        ]);
    }
}
