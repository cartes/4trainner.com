<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminDashboardController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

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

Route::middleware(['auth', 'role:profesor|super-admin'])->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/students', [\App\Http\Controllers\Teacher\StudentController::class, 'index'])->name('students.index');
    Route::get('/students/{id}', [\App\Http\Controllers\Teacher\StudentController::class, 'show'])->name('students.show');
});

require __DIR__.'/auth.php';
