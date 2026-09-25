<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

class DashboardController extends Controller
{
    public function redirect(): RedirectResponse
    {
        $user = auth()->user();

        return match ($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'superadmin' => redirect()->route('superadmin.dashboard'),
            'staff' => redirect()->route('staff.dashboard'),
            'student' => redirect()->route('student.dashboard'),
            'parent' => redirect()->route('parent.dashboard'),
            'finance_officer' => redirect()->route('finance.dashboard'),
            'exam_officer' => redirect()->route('exam.dashboard'),
            'proprietor' => redirect()->route('proprietor.dashboard'),
            default => redirect()->route('home'),
        };
    }
}
