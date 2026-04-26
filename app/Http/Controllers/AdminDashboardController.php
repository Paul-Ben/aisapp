<?php

namespace App\Http\Controllers;

use App\Models\AcademicCalendar;
use App\Models\GirlsHairstyle;
use App\Models\Newsletter;
use App\Models\AcademicSession;
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
        $activeSession = AcademicSession::getActive();
        $allSessions = AcademicSession::orderBy('created_at', 'desc')->get();
        
        return view('dashboards.admin', compact('currentCalendar', 'currentHairstyle', 'currentNewsletter', 'activeSession', 'allSessions'));
    }

    /**
     * Store a new academic session
     */
    public function storeSession(Request $request)
    {
        $request->validate([
            'session' => 'required|string|max:20',
            'term' => 'required|in:first,second,third',
        ]);

        // Create new session (not active by default)
        AcademicSession::create([
            'session' => $request->session,
            'term' => $request->term,
            'is_active' => false,
        ]);

        return redirect()->back()->with('success', 'Academic session added successfully. Set it as active to use.');
    }

    /**
     * Set an academic session as active
     */
    public function setActiveSession($id)
    {
        $session = AcademicSession::findOrFail($id);
        $session->setActive();

        return redirect()->back()->with('success', "Session {$session->session} ({$session->term}) set as active.");
    }

    /**
     * Delete an academic session
     */
    public function deleteSession($id)
    {
        $session = AcademicSession::findOrFail($id);
        
        // Prevent deleting active session
        if ($session->is_active) {
            return redirect()->back()->with('error', 'Cannot delete the active session. Set another session as active first.');
        }

        $session->delete();
        return redirect()->back()->with('success', 'Academic session deleted successfully.');
    }
}
