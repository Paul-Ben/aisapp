<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Http\Request;

class StaffClassesController extends Controller
{
    /**
     * Display a list of classes assigned to the logged-in staff
     */
    public function index()
    {
        $user = auth()->user();
        
        // Get the staff record for the logged-in user
        $staff = $user->staff;
        
        if (!$staff) {
            return redirect()->route('staff.dashboard')
                ->with('error', 'Staff profile not found.');
        }
        
        // Get all classes assigned to this staff member
        $classes = $staff->classes()->with(['category', 'staff'])->get();
        
        return view('staff.classes.index', compact('classes'));
    }
    
    /**
     * Display students in a specific class
     */
    public function showStudents($classId)
    {
        $user = auth()->user();
        $staff = $user->staff;
        
        if (!$staff) {
            return redirect()->route('staff.dashboard')
                ->with('error', 'Staff profile not found.');
        }
        
        // Get the class and verify the staff is assigned to it
        $schoolClass = $staff->classes()
            ->where('classes.id', $classId)
            ->with(['category', 'staff'])
            ->first();
        
        if (!$schoolClass) {
            return redirect()->route('staff.classes.index')
                ->with('error', 'You are not assigned to this class.');
        }
        
        // Get all active students in this class
        $students = Student::where('class_id', $classId)
            ->where('status', 'active')
            ->orderBy('last_name', 'asc')
            ->orderBy('first_name', 'asc')
            ->get();
        
        return view('staff.classes.students', compact('schoolClass', 'students'));
    }
}
