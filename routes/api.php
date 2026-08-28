<?php

use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\AttendanceApiController;
use App\Http\Controllers\Api\PointApiController;
use App\Http\Controllers\Api\StudentApiController;
use App\Http\Controllers\Api\TenantApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes for HikmahWay LMS & Teacher Mobile App
|--------------------------------------------------------------------------
*/

// Public Authentication
Route::post('/login', [ApiAuthController::class, 'login']);
Route::post('/v1/login', [ApiAuthController::class, 'login']);

// Authenticated API Routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [ApiAuthController::class, 'logout']);
    Route::get('/me', [ApiAuthController::class, 'me']);

    // Tenants / Campuses
    Route::get('/tenants', [TenantApiController::class, 'index']);
    Route::post('/tenants/{tenant}/switch', [TenantApiController::class, 'switch']);

    // Attendance
    Route::get('/attendance', [AttendanceApiController::class, 'index']);
    Route::post('/attendance', [AttendanceApiController::class, 'store']);

    // Points Management
    Route::post('/points/award', [PointApiController::class, 'award']);
    Route::get('/points/transactions', [PointApiController::class, 'transactions']);

    // Student Management
    Route::get('/students', [StudentApiController::class, 'index']);
    Route::post('/students', [StudentApiController::class, 'store']);
    Route::get('/students/{student}', [StudentApiController::class, 'show']);
    Route::put('/students/{student}', [StudentApiController::class, 'update']);
    Route::delete('/students/{student}', [StudentApiController::class, 'destroy']);

    // Mobile App Versioned API Routes (/v1/...)
    Route::prefix('v1')->group(function () {
        Route::post('/logout', [ApiAuthController::class, 'logout']);
        Route::get('/me', [ApiAuthController::class, 'me']);

        Route::get('/tenants', [TenantApiController::class, 'index']);
        Route::post('/tenants/{tenant}/switch', [TenantApiController::class, 'switch']);

        Route::get('/attendance', [AttendanceApiController::class, 'index']);
        Route::post('/attendance', [AttendanceApiController::class, 'store']);

        Route::post('/points/award', [PointApiController::class, 'award']);
        Route::get('/points/transactions', [PointApiController::class, 'transactions']);

        Route::get('/students', [StudentApiController::class, 'index']);
        Route::post('/students', [StudentApiController::class, 'store']);
        Route::get('/students/{student}', [StudentApiController::class, 'show']);
        Route::put('/students/{student}', [StudentApiController::class, 'update']);
        Route::delete('/students/{student}', [StudentApiController::class, 'destroy']);
    });
});

