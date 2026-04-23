<?php

namespace App\Http\Controllers;

use App\Models\AcademicCalendar;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    /**
     * Display the admin dashboard
     */
    public function index()
    {
        $currentCalendar = AcademicCalendar::getLatest();
        return view('dashboards.admin', compact('currentCalendar'));
    }
}
