<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StreamingController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/planes', [PageController::class, 'pricing'])->name('pricing');

// Central dashboard redirect (role-based)
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Trainer dashboard
Route::middleware(['auth', 'verified', 'role:profesor'])->group(function () {
    Route::get('/trainer/dashboard', function () {
        return view('trainer.dashboard');
    })->name('trainer.dashboard');

    // Live Studio
    Route::get('/trainer/studio', [\App\Http\Controllers\TrainerStudioController::class, 'index'])->name('trainer.studio');
});

// Student dashboard
Route::get('/student/dashboard', [\App\Http\Controllers\StudentDashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'role:alumno|student'])->name('student.dashboard');

// Moderator dashboard
Route::get('/moderator/dashboard', function () {
    return view('moderator.dashboard');
})->middleware(['auth', 'verified', 'role:moderador'])->name('moderator.dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/settings', [SettingsController::class, 'edit'])->name('settings.edit');
    Route::patch('/settings', [SettingsController::class, 'update'])->name('settings.update');

    // Streaming
    Route::get('/channels', [\App\Http\Controllers\StreamingController::class, 'index'])->name('channels.index');
    Route::get('/channels/{channel:slug}', [\App\Http\Controllers\StreamingController::class, 'show'])->name('channels.show');

    // Private Video Streaming VOD
    Route::get('/stream/{video}', [\App\Http\Controllers\PrivateStreamController::class, 'stream'])->name('stream.video');
});

Route::middleware(['auth', 'role:super-admin'])->group(function () {
    Route::get('admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('admin/users', [AdminDashboardController::class, 'users'])->name('admin.users');
    Route::post('admin/users/store', [AdminDashboardController::class, 'store'])->name('admin.users.store');
    Route::get('admin/users/data', [AdminDashboardController::class, 'data'])->name('admin.users.data');
    Route::get('admin/users/{id}/edit', [AdminDashboardController::class, 'edit'])->name('admin.users.edit');
    Route::put('admin/users/{id}', [AdminDashboardController::class, 'update'])->name('admin.users.update');
    Route::delete('admin/users/{id}', [AdminDashboardController::class, 'destroy'])->name('admin.users.destroy');
    Route::resource('admin/categories', \App\Http\Controllers\CategoryController::class)->names('admin.categories');

    // Global Settings
    Route::get('admin/settings', [\App\Http\Controllers\Admin\SystemSettingController::class, 'index'])->name('admin.settings');
    Route::get('admin/settings/data', [\App\Http\Controllers\Admin\SystemSettingController::class, 'getData'])->name('admin.settings.data');
    Route::post('admin/settings/batch', [\App\Http\Controllers\Admin\SystemSettingController::class, 'updateBatch'])->name('admin.settings.batch');

    // Audit and Metrics
    Route::get('admin/audit', [\App\Http\Controllers\Admin\AuditController::class, 'index'])->name('admin.audit');
    Route::get('admin/audit/logs', [\App\Http\Controllers\Admin\AuditController::class, 'logs'])->name('admin.audit.logs');
    Route::get('admin/audit/metrics', [\App\Http\Controllers\Admin\AuditController::class, 'metrics'])->name('admin.audit.metrics');
});


require __DIR__ . '/auth.php';
