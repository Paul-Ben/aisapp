<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\AcademicSession;
use Illuminate\Http\Request;
use Carbon\Carbon;

class GraduateController extends Controller
{
    /**
     * Display a listing of graduated students.
     */
    public function index(Request $request)
    {
        $query = Student::where('status', 'graduated')
            ->with(['class', 'graduationSession']);

        // Filter by graduation session
        if ($request->filled('session_id')) {
            $query->where('graduation_session_id', $request->session_id);
        }

        // Filter by graduation year
        if ($request->filled('year')) {
            $query->whereYear('graduation_date', $request->year);
        }

        // Search by Name, admission number or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('middle_name', 'like', "%{$search}%")
                  ->orWhere('admission_number', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $graduates = $query->orderBy('graduation_date', 'desc')->paginate(15);
        
        $sessions = AcademicSession::orderBy('session', 'desc')->get();
        
        // Get unique graduation years for filtering
        $years = Student::where('status', 'graduated')
            ->whereNotNull('graduation_date')
            ->selectRaw('YEAR(graduation_date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('admin.students.graduates', compact('graduates', 'sessions', 'years'));
    }
}
