<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AcademicCalendarController;
use App\Http\Controllers\GirlsHairstyleController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Public route to view academic calendar on landing page
Route::get('/term-calendar', [AcademicCalendarController::class, 'show'])->name('calendar.show');

// Public route to view girls hairstyles on landing page
Route::get('/girls-hairstyles', [GirlsHairstyleController::class, 'show'])->name('hairstyles.show');

// Redirect authenticated users to their role-based dashboard
Route::get('/dashboard', function () {
    $user = Auth::user();

    if ($user->isSuperAdmin()) {
        return redirect()->route('superadmin.dashboard');
    } elseif ($user->isFinanceOfficer()) {
        return redirect()->route('finance.dashboard');
    } elseif ($user->isAdmin()) {
        return redirect()->route('admin.dashboard');
    } elseif ($user->isExamOfficer()) {
        return redirect()->route('exam.dashboard');
    } elseif ($user->isProprietor()) {
        return redirect()->route('proprietor.dashboard');
    } else {
        return redirect()->route('staff.dashboard');
    }
})->middleware(['auth', 'verified'])->name('dashboard');

// Super Admin Dashboard Routes
Route::middleware(['auth', 'role:superadmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboards.superadmin');
    })->name('dashboard');
});

// Finance Officer Dashboard Routes
Route::middleware(['auth', 'role:finance_officer'])->prefix('finance')->name('finance.')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboards.finance');
    })->name('dashboard');
});

// Admin Dashboard Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::post('/calendar/upload', [AcademicCalendarController::class, 'upload'])->name('calendar.upload');
    Route::delete('/calendar/delete', [AcademicCalendarController::class, 'destroy'])->name('calendar.delete');
    Route::post('/hairstyles/upload', [GirlsHairstyleController::class, 'upload'])->name('hairstyles.upload');
    Route::delete('/hairstyles/delete', [GirlsHairstyleController::class, 'destroy'])->name('hairstyles.delete');
});

// Exam Officer Dashboard Routes
Route::middleware(['auth', 'role:exam_officer'])->prefix('exam')->name('exam.')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboards.exam');
    })->name('dashboard');
});

// Staff Dashboard Routes
Route::middleware(['auth', 'role:staff'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboards.staff');
    })->name('dashboard');
});

// Proprietor Dashboard Routes
Route::middleware(['auth', 'role:proprietor'])->prefix('proprietor')->name('proprietor.')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboards.proprietor');
    })->name('dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
