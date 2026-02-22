<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::prefix('v1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

// Protected routes
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    // Authentication
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Trainer routes
    Route::middleware('role:profesor|super-admin')->group(function () {
        Route::get('/trainer/students', [\App\Http\Controllers\Api\V1\Trainer\StudentController::class, 'index']);
        Route::get('/trainer/stats', [\App\Http\Controllers\Api\V1\Trainer\StudentController::class, 'stats']);
        Route::get('/trainer/routines', [\App\Http\Controllers\Api\V1\Trainer\RoutineController::class, 'index']);
        Route::post('/trainer/routines', [\App\Http\Controllers\Api\V1\Trainer\RoutineController::class, 'store']);
        Route::post('/trainer/routines/assign', [\App\Http\Controllers\Api\V1\Trainer\RoutineController::class, 'assign']);
        Route::get('/trainer/exercises', [\App\Http\Controllers\Api\V1\Trainer\RoutineController::class, 'exercises']);
    });

    // Student dashboard
    Route::middleware('role:alumno|student')->group(function () {
        Route::get('/student/dashboard', [\App\Http\Controllers\Api\V1\Student\DashboardController::class, 'index']);
    });

    // Moderator dashboard
    Route::middleware('role:moderador')->group(function () {
        Route::get('/moderator/dashboard', [\App\Http\Controllers\Api\V1\Moderator\DashboardController::class, 'index']);
    });

    // Categories
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{category}', [CategoryController::class, 'show']);
    Route::middleware('role:super-admin')->group(function () {
        Route::post('/categories', [CategoryController::class, 'store']);
        Route::put('/categories/{category}', [CategoryController::class, 'update']);
        Route::patch('/categories/{category}', [CategoryController::class, 'update']);
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);
    });

    // Admin routes (super-admin only)
    Route::middleware('role:super-admin')->group(function () {
        Route::apiResource('users', UserController::class);
        Route::get('/admin/dashboard', [\App\Http\Controllers\Api\V1\Admin\DashboardController::class, 'index']);
    });

});
