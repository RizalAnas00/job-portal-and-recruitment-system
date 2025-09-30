<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

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

Route::middleware(['auth', 'role:admin'])->group(function () {
    // Admin-only routes
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
    // Add more admin routes here
});

Route::middleware(['auth', 'role:user'])->group(function () {
    // User-only routes
    Route::get('/user/dashboard', function () {
        return view('user.dashboard');
    })->name('user.dashboard');
    // Add more user routes here
});

Route::middleware(['auth', 'role:company'])->group(function () {
    // Company-only routes
    Route::get('/company/dashboard', function () {
        return view('company.dashboard');
    })->name('company.dashboard');
    // Add more company routes here
});

require __DIR__.'/auth.php';
