<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PageController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/planes', [PageController::class, 'pricing'])->name('pricing');

// Central dashboard redirect (role-based)
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Trainer dashboard
Route::get('/trainer/dashboard', function () {
    return view('trainer.dashboard');
})->middleware(['auth', 'verified', 'role:profesor'])->name('trainer.dashboard');

// Student dashboard
Route::get('/student/dashboard', function () {
    return view('student.dashboard');
})->middleware(['auth', 'verified', 'role:alumno|student'])->name('student.dashboard');

// Moderator dashboard
Route::get('/moderator/dashboard', function () {
    return view('moderator.dashboard');
})->middleware(['auth', 'verified', 'role:moderador'])->name('moderator.dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:super-admin'])->group(function () {
    Route::get('admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('admin/users', [AdminDashboardController::class, 'users'])->name('admin.users');
    Route::post('admin/users/store', [AdminDashboardController::class, 'store'])->name('admin.users.store');
    Route::get('admin/users/data', [AdminDashboardController::class, 'data'])->name('admin.users.data');
    Route::get('admin/users/{id}/edit', [AdminDashboardController::class, 'edit'])->name('admin.users.edit');
    Route::put('admin/users/{id}', [AdminDashboardController::class, 'update'])->name('admin.users.update');
    Route::get('admin/categories', [AdminDashboardController::class, 'categories'])->name('admin.categories');
});


require __DIR__ . '/auth.php';
