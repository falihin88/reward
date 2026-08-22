<?php

use App\Http\Controllers\Admin\CardController as AdminCardController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Student\CardController as StudentCardController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Student\ProfileController as StudentProfileController;
use App\Http\Controllers\Teacher\AttendanceController as TeacherAttendanceController;
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboardController;
use App\Http\Controllers\Teacher\StudentDetailController as TeacherStudentDetailController;
use App\Http\Middleware\EnsureUserRole;
use Illuminate\Support\Facades\Route;

// Auth Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/switch-user/{user}', [AuthController::class, 'switchUser'])->name('switch-user');
Route::post('/impersonate/stop', [AuthController::class, 'stopImpersonating'])->name('impersonate.stop');
Route::post('/impersonate/{user}', [AuthController::class, 'impersonate'])->name('impersonate');

// Redirect root to dashboard based on role or login
Route::get('/', function () {
    if (auth()->check()) {
        $role = auth()->user()->role;
        if ($role === 'admin') return redirect()->route('admin.dashboard');
        if ($role === 'teacher') return redirect()->route('teacher.dashboard');
        return redirect()->route('student.dashboard');
    }
    return redirect()->route('login');
});

// Authenticated Routes
Route::middleware(['auth'])->group(function () {

    // Student Routes
    Route::middleware([EnsureUserRole::class . ':student,admin,teacher'])->group(function () {
        Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('student.dashboard');
        Route::post('/dashboard/streak', [StudentDashboardController::class, 'claimStreak'])->name('student.streak');
        Route::get('/cards', [StudentCardController::class, 'index'])->name('student.cards');
        Route::post('/cards/{card}/unlock', [StudentCardController::class, 'unlock'])->name('student.cards.unlock');
        Route::get('/profile', [StudentProfileController::class, 'show'])->name('student.profile');
    });

    // Teacher Routes
    Route::middleware([EnsureUserRole::class . ':teacher,admin'])->group(function () {
        Route::get('/teacher', [TeacherDashboardController::class, 'index'])->name('teacher.dashboard');
        Route::get('/teacher/attendance', [TeacherAttendanceController::class, 'index'])->name('teacher.attendance.index');
        Route::post('/teacher/attendance', [TeacherAttendanceController::class, 'store'])->name('teacher.attendance.store');
        Route::post('/teacher/award', [TeacherDashboardController::class, 'awardPoints'])->name('teacher.award');
        Route::post('/teacher/students', [TeacherDashboardController::class, 'storeStudent'])->name('teacher.students.store');
        Route::put('/teacher/students/{student}', [TeacherDashboardController::class, 'updateStudent'])->name('teacher.students.update');
        Route::delete('/teacher/students/{student}', [TeacherDashboardController::class, 'destroyStudent'])->name('teacher.students.destroy');
        Route::get('/teacher/students/{student}', [TeacherStudentDetailController::class, 'show'])->name('teacher.students.show');
    });

    // Admin Routes
    Route::middleware([EnsureUserRole::class . ':admin'])->group(function () {
        Route::get('/admin', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
        
        // Users Management
        Route::get('/admin/users', [AdminUserController::class, 'index'])->name('admin.users.index');
        Route::post('/admin/users', [AdminUserController::class, 'store'])->name('admin.users.store');
        Route::put('/admin/users/{user}', [AdminUserController::class, 'update'])->name('admin.users.update');
        Route::delete('/admin/users/{user}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');

        // Cards Management
        Route::get('/admin/cards', [AdminCardController::class, 'index'])->name('admin.cards.index');
        Route::post('/admin/cards', [AdminCardController::class, 'store'])->name('admin.cards.store');
        Route::put('/admin/cards/{card}', [AdminCardController::class, 'update'])->name('admin.cards.update');
        Route::delete('/admin/cards/{card}', [AdminCardController::class, 'destroy'])->name('admin.cards.destroy');
    });
});
