<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use App\Services\PointService;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $teacher = $request->user();
        $date = $request->query('date', now()->toDateString());

        $students = User::where('role', 'student')
            ->where(function ($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id)
                      ->orWhereNull('teacher_id');
            })
            ->orderBy('name', 'asc')
            ->get(['id', 'name', 'email', 'points', 'current_streak']);

        $attendances = Attendance::where('date', $date)
            ->whereIn('student_id', $students->pluck('id'))
            ->get()
            ->keyBy('student_id');

        return Inertia::render('Teacher/Attendance/Index', [
            'students' => $students,
            'attendances' => $attendances,
            'selectedDate' => $date,
        ]);
    }

    public function store(Request $request, PointService $pointService)
    {
        $request->validate([
            'date' => ['required', 'date'],
            'records' => ['required', 'array'],
            'records.*.student_id' => ['required', 'exists:users,id'],
            'records.*.status' => ['required', 'in:present,absent,late,excused'],
            'records.*.notes' => ['nullable', 'string', 'max:255'],
        ]);

        $teacher = $request->user();
        $date = $request->date;
        $records = $request->records;

        $awardedCount = 0;
        $totalPointsAwarded = 0;

        foreach ($records as $item) {
            $studentId = $item['student_id'];
            $status = $item['status'];
            $notes = $item['notes'] ?? null;

            // Define points rule: Present = 100 Pts, Late = 50 Pts
            $targetPoints = match ($status) {
                'present' => 100,
                'late' => 50,
                default => 0,
            };

            $existing = Attendance::where('student_id', $studentId)
                ->where('date', $date)
                ->first();

            $prevPoints = $existing ? $existing->points_awarded : 0;
            $pointsDiff = $targetPoints - $prevPoints;

            Attendance::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'date' => $date,
                ],
                [
                    'teacher_id' => $teacher->id,
                    'status' => $status,
                    'points_awarded' => $targetPoints,
                    'notes' => $notes,
                ]
            );

            // Award difference in points to student if positive
            if ($pointsDiff > 0) {
                $student = User::find($studentId);
                if ($student) {
                    try {
                        $reason = $status === 'present' ? 'attendance_present' : 'attendance_late';
                        $noteText = "Attendance on {$date}: " . strtoupper($status) . " (+{$pointsDiff} Pts)";
                        
                        $pointService->awardOrDeductPointsByTeacher(
                            $student,
                            $teacher,
                            $pointsDiff,
                            $reason,
                            $noteText
                        );
                        $awardedCount++;
                        $totalPointsAwarded += $pointsDiff;
                    } catch (Exception $e) {
                        // Continue processing other students
                    }
                }
            }
        }

        return back()->with('success', "Attendance saved for {$date}! Awarded total of +{$totalPointsAwarded} points to attending students.");
    }
}
