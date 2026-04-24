<?php

namespace App\Http\Controllers;

use App\Models\AcademicCalendar;
use App\Models\GirlsHairstyle;
use App\Models\Newsletter;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    /**
     * Display the admin dashboard
     */
    public function index()
    {
        $currentCalendar = AcademicCalendar::getLatest();
        $currentHairstyle = GirlsHairstyle::getLatest();
        $currentNewsletter = Newsletter::getLatest();
        return view('dashboards.admin', compact('currentCalendar', 'currentHairstyle', 'currentNewsletter'));
    }
}
