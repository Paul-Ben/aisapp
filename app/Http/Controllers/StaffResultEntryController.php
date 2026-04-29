<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\ResultConfig;
use App\Models\GradeScale;
use App\Models\Result;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ResultUploadTemplate;
use App\Imports\ResultUploadImport;

class StaffResultEntryController extends Controller
{
    public function index()
    {
        $staff = Auth::user()->staff;
        
        if (!$staff) {
            return redirect()->back()->with('error', 'Staff profile not found.');
        }

        // Get classes assigned to this staff
        $classes = $staff->classes()->with(['category', 'academicYear'])->get();

        return view('staff.results.index', compact('classes'));
    }

    public function selectSubject($classId)
    {
        $staff = Auth::user()->staff;
        $schoolClass = SchoolClass::with(['category', 'academicYear', 'students'])->findOrFail($classId);

        // Verify staff is assigned to this class
        if (!$staff->classes()->where('school_classes.id', $classId)->exists()) {
            abort(403, 'You are not assigned to this class.');
        }

        // Get subjects assigned to this class
        $subjects = $schoolClass->subjects()->with('category')->get();

        // Get result config for this class
        $resultConfig = ResultConfig::where('class_id', $classId)->first();

        if (!$resultConfig) {
            return redirect()->route('staff.results.index')
                ->with('error', 'Result configuration not set for this class by Admin.');
        }

        return view('staff.results.select-subject', compact('schoolClass', 'subjects', 'resultConfig'));
    }

    public function uploadForm($classId, $subjectId)
    {
        $staff = Auth::user()->staff;
        $schoolClass = SchoolClass::with('students')->findOrFail($classId);
        $subject = Subject::findOrFail($subjectId);

        // Verify staff assignment
        if (!$staff->classes()->where('school_classes.id', $classId)->exists()) {
            abort(403, 'Unauthorized access.');
        }

        // Verify subject belongs to class
        if (!$schoolClass->subjects()->where('subjects.id', $subjectId)->exists()) {
            abort(403, 'Subject not assigned to this class.');
        }

        $resultConfig = ResultConfig::where('class_id', $classId)->first();
        
        // Get existing results if any
        $existingResults = Result::where('class_id', $classId)
            ->where('subject_id', $subjectId)
            ->where('academic_year_id', $schoolClass->academic_year_id)
            ->pluck('ca_score', 'student_id')
            ->toArray();

        return view('staff.results.upload', compact('schoolClass', 'subject', 'resultConfig', 'existingResults'));
    }

    public function downloadTemplate($classId, $subjectId)
    {
        $staff = Auth::user()->staff;
        $schoolClass = SchoolClass::with('students')->findOrFail($classId);
        $subject = Subject::findOrFail($subjectId);

        // Verify staff assignment
        if (!$staff->classes()->where('school_classes.id', $classId)->exists()) {
            abort(403, 'Unauthorized access.');
        }

        $resultConfig = ResultConfig::where('class_id', $classId)->first();
        
        return Excel::download(new ResultUploadTemplate($schoolClass, $subject, $resultConfig), 
            "result_template_{$subject->code}_{$schoolClass->name}.xlsx");
    }

    public function processUpload(Request $request, $classId, $subjectId)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls,csv',
        ]);

        $staff = Auth::user()->staff;
        $schoolClass = SchoolClass::findOrFail($classId);
        $subject = Subject::findOrFail($subjectId);

        // Verify staff assignment
        if (!$staff->classes()->where('school_classes.id', $classId)->exists()) {
            abort(403, 'Unauthorized access.');
        }

        $resultConfig = ResultConfig::where('class_id', $classId)->first();

        try {
            Excel::import(new ResultUploadImport($schoolClass, $subject, $resultConfig, $staff->id), $request->file('excel_file'));
            
            return redirect()->route('staff.results.subject', ['classId' => $classId])
                ->with('success', 'Results uploaded successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error uploading results: ' . $e->getMessage());
        }
    }

    public function manualSave(Request $request, $classId, $subjectId)
    {
        $request->validate([
            'scores' => 'required|array',
            'scores.*.student_id' => 'required|exists:users,id',
            'scores.*.ca_score' => 'nullable|numeric',
            'scores.*.project_score' => 'nullable|numeric',
            'scores.*.exam_score' => 'nullable|numeric',
        ]);

        $staff = Auth::user()->staff;
        $schoolClass = SchoolClass::findOrFail($classId);
        $subject = Subject::findOrFail($subjectId);
        $resultConfig = ResultConfig::where('class_id', $classId)->first();

        // Verify staff assignment
        if (!$staff->classes()->where('school_classes.id', $classId)->exists()) {
            abort(403, 'Unauthorized access.');
        }

        DB::beginTransaction();
        try {
            foreach ($request->scores as $scoreData) {
                $student = User::findOrFail($scoreData['student_id']);
                
                // Validate scores against config
                $caScore = $scoreData['ca_score'] ?? null;
                $projectScore = $scoreData['project_score'] ?? null;
                $examScore = $scoreData['exam_score'] ?? null;

                if ($caScore !== null && $caScore > $resultConfig->max_ca_score) {
                    throw new \Exception("CA score for {$student->full_name} exceeds maximum allowed ({$resultConfig->max_ca_score})");
                }
                if ($resultConfig->has_project && $projectScore !== null && $projectScore > $resultConfig->max_project_score) {
                    throw new \Exception("Project score for {$student->full_name} exceeds maximum allowed ({$resultConfig->max_project_score})");
                }
                if ($examScore !== null && $examScore > $resultConfig->max_exam_score) {
                    throw new \Exception("Exam score for {$student->full_name} exceeds maximum allowed ({$resultConfig->max_exam_score})");
                }

                $totalScore = ($caScore ?? 0) + ($projectScore ?? 0) + ($examScore ?? 0);
                
                // Determine Grade
                $grade = $this->calculateGrade($totalScore, $resultConfig);

                Result::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'class_id' => $classId,
                        'subject_id' => $subjectId,
                        'academic_year_id' => $schoolClass->academic_year_id,
                        'term' => $request->input('term', 'First'), // Default term
                    ],
                    [
                        'ca_score' => $caScore,
                        'project_score' => $projectScore,
                        'exam_score' => $examScore,
                        'total_score' => $totalScore,
                        'grade' => $grade,
                        'remark' => $this->generateRemark($grade),
                        'entered_by' => $staff->id,
                    ]
                );
            }

            DB::commit();
            return redirect()->back()->with('success', 'Results saved successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    private function calculateGrade($totalScore, $resultConfig)
    {
        $gradeScale = GradeScale::where('result_config_id', $resultConfig->id)
            ->orderBy('min_percentage', 'desc')
            ->get();

        foreach ($gradeScale as $scale) {
            if ($totalScore >= $scale->min_percentage) {
                return $scale->grade;
            }
        }

        return 'F'; // Default fail if no match
    }

    private function generateRemark($grade)
    {
        $remarks = [
            'A' => 'Excellent',
            'B' => 'Very Good',
            'C' => 'Good',
            'D' => 'Fair',
            'E' => 'Pass',
            'F' => 'Fail'
        ];
        return $remarks[$grade] ?? 'No Grade';
    }
}
