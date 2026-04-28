<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AcademicCalendarController;
use App\Http\Controllers\GirlsHairstyleController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\Admin\StaffManagementController;
use App\Http\Controllers\Admin\ClassManagementController;
use App\Http\Controllers\Admin\GraduateController;
use App\Http\Controllers\Admin\ResultConfigController;
use App\Http\Controllers\Admin\SubjectManagementController;
use App\Http\Controllers\StaffClassesController;
use App\Http\Controllers\StaffResultEntryController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Auth::routes();

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // User Management
    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserManagementController::class, 'create'])->name('users.create');
    Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');
    
    // Staff Management
    Route::get('/staff', [StaffManagementController::class, 'index'])->name('staff.index');
    Route::get('/staff/create', [StaffManagementController::class, 'create'])->name('staff.create');
    Route::post('/staff', [StaffManagementController::class, 'store'])->name('staff.store');
    Route::get('/staff/{staff}/edit', [StaffManagementController::class, 'edit'])->name('staff.edit');
    Route::put('/staff/{staff}', [StaffManagementController::class, 'update'])->name('staff.update');
    Route::delete('/staff/{staff}', [StaffManagementController::class, 'destroy'])->name('staff.destroy');
    
    // Class Management
    Route::get('/classes', [ClassManagementController::class, 'index'])->name('classes.index');
    Route::get('/classes/create', [ClassManagementController::class, 'create'])->name('classes.create');
    Route::post('/classes', [ClassManagementController::class, 'store'])->name('classes.store');
    Route::get('/classes/{class}/edit', [ClassManagementController::class, 'edit'])->name('classes.edit');
    Route::put('/classes/{class}', [ClassManagementController::class, 'update'])->name('classes.update');
    Route::delete('/classes/{class}', [ClassManagementController::class, 'destroy'])->name('classes.destroy');
    
    // Result Configuration
    Route::get('/result-config', [ResultConfigController::class, 'index'])->name('result-config.index');
    Route::get('/result-config/create', [ResultConfigController::class, 'create'])->name('result-config.create');
    Route::post('/result-config', [ResultConfigController::class, 'store'])->name('result-config.store');
    Route::get('/result-config/{config}/edit', [ResultConfigController::class, 'edit'])->name('result-config.edit');
    Route::put('/result-config/{config}', [ResultConfigController::class, 'update'])->name('result-config.update');
    Route::delete('/result-config/{config}', [ResultConfigController::class, 'destroy'])->name('result-config.destroy');
    
    // Subject Management
    Route::get('/subjects', [SubjectManagementController::class, 'index'])->name('subjects.index');
    Route::get('/subjects/create', [SubjectManagementController::class, 'create'])->name('subjects.create');
    Route::post('/subjects', [SubjectManagementController::class, 'store'])->name('subjects.store');
    Route::get('/subjects/{subject}/edit', [SubjectManagementController::class, 'edit'])->name('subjects.edit');
    Route::put('/subjects/{subject}', [SubjectManagementController::class, 'update'])->name('subjects.update');
    Route::delete('/subjects/{subject}', [SubjectManagementController::class, 'destroy'])->name('subjects.destroy');
    
    // Student Management
    Route::resource('students', StudentController::class);
    Route::post('/students/{student}/promote', [StudentController::class, 'promote'])->name('students.promote');
    Route::post('/students/bulk-promote', [StudentController::class, 'bulkPromote'])->name('students.bulk-promote');
    
    // Graduates
    Route::get('/graduates', [GraduateController::class, 'index'])->name('graduates.index');
    Route::post('/students/{student}/graduate', [GraduateController::class, 'store'])->name('graduates.store');
    
    // Academic Calendar
    Route::resource('academic-calendars', AcademicCalendarController::class);
    
    // Girls Hairstyle
    Route::get('/hairstyles', [GirlsHairstyleController::class, 'index'])->name('hairstyles.index');
    Route::post('/hairstyles', [GirlsHairstyleController::class, 'store'])->name('hairstyles.store');
    Route::delete('/hairstyles/{hairstyle}', [GirlsHairstyleController::class, 'destroy'])->name('hairstyles.destroy');
    
    // Newsletter
    Route::get('/newsletter', [NewsletterController::class, 'index'])->name('newsletter.index');
    Route::post('/newsletter', [NewsletterController::class, 'store'])->name('newsletter.store');
});

// Staff Routes
Route::middleware(['auth', 'role:staff'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('/dashboard', function() {
        return view('dashboards.staff');
    })->name('dashboard');
    
    // My Classes
    Route::get('/my-classes', [StaffClassesController::class, 'index'])->name('classes.index');
    Route::get('/my-classes/{classId}/students', [StaffClassesController::class, 'showStudents'])->name('classes.students');
    
    // Result Entry
    Route::prefix('results')->name('results.')->group(function () {
        Route::get('/', [StaffResultEntryController::class, 'index'])->name('index');
        Route::get('/{classId}/subjects', [StaffResultEntryController::class, 'selectSubject'])->name('subjects');
        Route::get('/{classId}/{subjectId}/upload', [StaffResultEntryController::class, 'uploadForm'])->name('upload');
        Route::get('/{classId}/{subjectId}/download-template', [StaffResultEntryController::class, 'downloadTemplate'])->name('download-template');
        Route::post('/{classId}/{subjectId}/process-upload', [StaffResultEntryController::class, 'processUpload'])->name('process-upload');
        Route::post('/{classId}/{subjectId}/manual-save', [StaffResultEntryController::class, 'manualSave'])->name('manual-save');
    });
});

// Student Routes
Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', function() {
        return view('dashboards.student');
    })->name('dashboard');
    
    Route::get('/results', function() {
        return view('student.results');
    })->name('results');
});

// Parent Routes
Route::middleware(['auth', 'role:parent'])->prefix('parent')->name('parent.')->group(function () {
    Route::get('/dashboard', function() {
        return view('dashboards.parent');
    })->name('dashboard');
});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
