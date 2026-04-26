<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

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
    Route::get('/dashboard', function () {
        return view('dashboards.admin');
    })->name('dashboard');
    
    // Student Management Routes
    Route::resource('students', \App\Http\Controllers\StudentController::class);
    Route::post('students/bulk-promote', [\App\Http\Controllers\StudentController::class, 'bulkPromote'])->name('students.bulk-promote');
    Route::post('students/bulk-demote', [\App\Http\Controllers\StudentController::class, 'bulkDemote'])->name('students.bulk-demote');
    Route::post('students/bulk-graduate', [\App\Http\Controllers\StudentController::class, 'bulkGraduate'])->name('students.bulk-graduate');
    Route::get('students/upload', [\App\Http\Controllers\StudentController::class, 'showUploadForm'])->name('students.upload');
    Route::post('students/process-upload', [\App\Http\Controllers\StudentController::class, 'processUpload'])->name('students.process-upload');
    Route::get('students/download-template', [\App\Http\Controllers\StudentController::class, 'downloadTemplate'])->name('students.download-template');
    Route::get('students/promote', [\App\Http\Controllers\StudentController::class, 'showPromoteForm'])->name('students.promote');
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
