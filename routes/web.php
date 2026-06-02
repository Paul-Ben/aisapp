<?php

use App\Http\Controllers\AcademicCalendarController;
use App\Http\Controllers\Admin\ClassManagementController;
use App\Http\Controllers\Admin\GraduateController;
use App\Http\Controllers\Admin\ResultConfigController;
use App\Http\Controllers\Admin\StaffManagementController;
use App\Http\Controllers\Admin\SubjectManagementController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Finance\FeeCollectionController;
use App\Http\Controllers\Finance\FeeManagementController;
use App\Http\Controllers\Finance\RevenueTrackingController;
use App\Http\Controllers\GirlsHairstyleController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\OnlinePaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StaffClassesController;
use App\Http\Controllers\StaffResultEntryController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/newsletter', [NewsletterController::class, 'show'])->name('newsletter.show');
Route::get('/academic-calendar', [AcademicCalendarController::class, 'show'])->name('calendar.show');
Route::get('/girls-hairstyles', [GirlsHairstyleController::class, 'show'])->name('hairstyles.show');

// Public Paystack Online Payment Flow
Route::prefix('pay-online')->name('pay-online.')->group(function () {
    Route::get('/', [OnlinePaymentController::class, 'search'])->name('search');
    Route::get('/{student}/fees', [OnlinePaymentController::class, 'fees'])->name('fees');
    Route::post('/{student}/{fee}/initialize', [OnlinePaymentController::class, 'initialize'])->name('initialize');
    Route::get('/callback', [OnlinePaymentController::class, 'callback'])->name('callback');
    Route::get('/receipt/{payment}', [OnlinePaymentController::class, 'receipt'])->name('receipt');
    Route::get('/receipt/{payment}/pdf', [OnlinePaymentController::class, 'receiptPdf'])->name('receipt.pdf');
});

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
    Route::post('/staff/{id}/toggle-status', [StaffManagementController::class, 'toggleStatus'])->name('staff.toggle-status');

    // Class Management
    Route::get('/classes', [ClassManagementController::class, 'index'])->name('classes.index');
    Route::get('/classes/create', [ClassManagementController::class, 'create'])->name('classes.create');
    Route::post('/classes', [ClassManagementController::class, 'store'])->name('classes.store');
    Route::get('/classes/{class}/edit', [ClassManagementController::class, 'edit'])->name('classes.edit');
    Route::put('/classes/{class}', [ClassManagementController::class, 'update'])->name('classes.update');
    Route::delete('/classes/{class}', [ClassManagementController::class, 'destroy'])->name('classes.destroy');
    Route::post('/classes/{class}/assign-staff', [ClassManagementController::class, 'assignStaff'])->name('classes.assign-staff');
    Route::delete('/classes/{class}/remove-staff/{staff}', [ClassManagementController::class, 'removeStaff'])->name('classes.remove-staff');

    // Class Categories
    Route::post('/class-categories', [ClassManagementController::class, 'storeCategory'])->name('class-categories.store');
    Route::put('/class-categories/{id}', [ClassManagementController::class, 'updateCategory'])->name('class-categories.update');
    Route::delete('/class-categories/{id}', [ClassManagementController::class, 'destroyCategory'])->name('class-categories.destroy');

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
    Route::post('/subjects/assign-to-class/{classId}', [SubjectManagementController::class, 'assignToClass'])->name('subjects.assign-to-class');
    Route::get('/subjects/class/{classId}', [SubjectManagementController::class, 'getSubjectsForClass'])->name('subjects.class');
    Route::delete('/subjects/{classId}/remove/{subjectId}', [SubjectManagementController::class, 'removeFromClass'])->name('subjects.remove');

    // Student Management
    Route::get('/students/upload', [StudentController::class, 'showUploadForm'])->name('students.upload');
    Route::post('/students/process-upload', [StudentController::class, 'processUpload'])->name('students.process-upload');
    Route::get('/students/download-template', [StudentController::class, 'downloadTemplate'])->name('students.download-template');
    Route::get('/students/promote', [StudentController::class, 'showPromoteForm'])->name('students.promote');
    Route::post('/students/bulk-promote', [StudentController::class, 'bulkPromote'])->name('students.bulk-promote');
    Route::post('/students/bulk-demote', [StudentController::class, 'bulkDemote'])->name('students.bulk-demote');
    Route::post('/students/bulk-graduate', [StudentController::class, 'bulkGraduate'])->name('students.bulk-graduate');
    Route::resource('students', StudentController::class);

    // Graduates
    Route::get('/graduates', [GraduateController::class, 'index'])->name('students.graduates');
    Route::post('/students/{student}/graduate', [GraduateController::class, 'store'])->name('graduates.store');

    // Academic Calendar
    Route::post('/calendar/upload', [AcademicCalendarController::class, 'upload'])->name('calendar.upload');
    Route::get('/calendar/delete', [AcademicCalendarController::class, 'destroy'])->name('calendar.delete');

    // Girls Hairstyle
    Route::post('/hairstyles/upload', [GirlsHairstyleController::class, 'upload'])->name('hairstyles.upload');
    Route::get('/hairstyles/delete', [GirlsHairstyleController::class, 'destroy'])->name('hairstyles.delete');

    // Newsletter
    Route::post('/newsletter/upload', [NewsletterController::class, 'upload'])->name('newsletter.upload');
    Route::get('/newsletter/delete', [NewsletterController::class, 'destroy'])->name('newsletter.delete');

    // Academic Sessions
    Route::post('/sessions', [AdminDashboardController::class, 'storeSession'])->name('session.store');
    Route::post('/sessions/{id}/set-active', [AdminDashboardController::class, 'setActiveSession'])->name('session.set-active');
    Route::delete('/sessions/{id}', [AdminDashboardController::class, 'deleteSession'])->name('session.delete');
});

// Staff Routes
Route::middleware(['auth', 'role:staff'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('/dashboard', function () {
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
    // Staff Classes Routes
    Route::get('/my-classes', [StaffClassesController::class, 'index'])->name('classes.index');
    Route::get('/my-classes/{classId}/students', [StaffClassesController::class, 'showStudents'])->name('classes.students');
});

// Student Routes
Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboards.student');
    })->name('dashboard');

    Route::get('/results', function () {
        return view('student.results');
    })->name('results');
});

// Parent Routes
Route::middleware(['auth', 'role:parent'])->prefix('parent')->name('parent.')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboards.parent');
    })->name('dashboard');
});

Route::get('/dashboard', [DashboardController::class, 'redirect'])
    ->middleware(['auth'])
    ->name('dashboard');

// Finance Routes
Route::middleware(['auth', 'role:finance_officer'])->prefix('finance')->name('finance.')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboards.finance');
    })->name('dashboard');

    Route::resource('fees', FeeManagementController::class)->except(['show']);

    Route::get('/payments', [FeeCollectionController::class, 'index'])->name('payments.index');
    Route::get('/payments/{student}', [FeeCollectionController::class, 'student'])->name('payments.student');
    Route::get('/payments/{student}/{fee}', [FeeCollectionController::class, 'form'])->name('payments.form');
    Route::put('/payments/{student}/{fee}', [FeeCollectionController::class, 'save'])->name('payments.save');

    Route::get('/revenue', [RevenueTrackingController::class, 'index'])->name('revenue.index');
});

// Exam Officer Routes
Route::middleware(['auth', 'role:exam_officer'])->prefix('exam')->name('exam.')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboards.exam');
    })->name('dashboard');
});

// Super Admin Routes
Route::middleware(['auth', 'role:superadmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboards.superadmin');
    })->name('dashboard');

    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
    Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
    Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');
    Route::post('/users/{user}/toggle-status', [UserManagementController::class, 'toggleStatus'])->name('users.toggle-status');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
