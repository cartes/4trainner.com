<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::prefix('v1')->group(function () {
    // Authentication
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

// Protected routes
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    // Authentication
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Categories
    Route::apiResource('categories', CategoryController::class);

    // Users (Admin only)
    Route::middleware('role:super-admin')->group(function () {
        Route::apiResource('users', UserController::class);
    });

    // Teacher routes
    Route::middleware(['role:profesor|super-admin'])->prefix('teacher')->group(function () {
        Route::get('/students', [\App\Http\Controllers\Teacher\StudentController::class, 'list']);
        Route::get('/students/{id}', [\App\Http\Controllers\Teacher\StudentController::class, 'details']);
    });
});
