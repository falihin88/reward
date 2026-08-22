<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\PointService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $teacher = $request->user();

        // Get students assigned to this teacher (or all students if admin/demo mode)
        $students = User::where('role', 'student')
            ->where(function ($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id)
                      ->orWhereNull('teacher_id');
            })
            ->withCount('cards as unlocked_cards_count')
            ->with(['pointTransactions' => function ($q) {
                $q->latest()->take(3);
            }])
            ->orderBy('name', 'asc')
            ->get();

        return Inertia::render('Teacher/Dashboard', [
            'students' => $students,
            'teacher' => $teacher,
        ]);
    }

    public function storeStudent(Request $request)
    {
        $teacher = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6'],
            'points' => ['nullable', 'integer', 'min:0'],
        ]);

        $initialPoints = $validated['points'] ?? 0;

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'student',
            'teacher_id' => $teacher->id,
            'points' => $initialPoints,
            'total_points_earned' => $initialPoints,
            'current_streak' => 1,
            'last_activity_date' => now(),
        ]);

        return back()->with('success', "Student account '{$validated['name']}' created successfully.");
    }

    public function updateStudent(Request $request, User $student)
    {
        if ($student->role !== 'student') {
            abort(403, 'Target user is not a student.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $student->id],
            'points' => ['required', 'integer', 'min:0'],
        ]);

        if ($request->filled('password')) {
            $request->validate([
                'password' => ['string', 'min:6'],
            ]);
            $validated['password'] = Hash::make($request->password);
        }

        $student->update($validated);

        return back()->with('success', "Student '{$student->name}' updated successfully.");
    }

    public function destroyStudent(User $student)
    {
        if ($student->role !== 'student') {
            abort(403, 'Target user is not a student.');
        }

        $name = $student->name;
        $student->delete();

        return back()->with('success', "Student '{$name}' account deleted successfully.");
    }

    public function awardPoints(Request $request, PointService $pointService)
    {
        $request->validate([
            'student_id' => ['required', 'exists:users,id'],
            'points' => ['required', 'integer', 'not_in:0'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $teacher = $request->user();
        $student = User::where('role', 'student')->findOrFail($request->student_id);

        try {
            $transaction = $pointService->awardOrDeductPointsByTeacher(
                $student,
                $teacher,
                (int) $request->points,
                $request->points > 0 ? 'teacher_award' : 'teacher_deduction',
                $request->note
            );

            $action = $request->points > 0 ? 'awarded' : 'deducted';
            $absPoints = abs($request->points);

            return back()->with('success', "Successfully {$action} {$absPoints} points for {$student->name}.");
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
