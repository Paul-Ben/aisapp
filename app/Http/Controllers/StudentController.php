<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\StudentsImport;
use App\Exports\StudentsExport;

class StudentController extends Controller
{
    /**
     * Display a listing of students.
     */
    public function index(Request $request)
    {
        $query = Student::with('class', 'previousClass');

        // Filter by status
        $status = $request->get('status', 'active');
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        // Filter by class
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('admission_number', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $students = $query->orderBy('created_at', 'desc')->paginate(15);
        $classes = ClassModel::where('status', 'active')->orderBy('name')->get();

        return view('admin.students.index', compact('students', 'classes', 'status'));
    }

    /**
     * Show the form for creating a new student.
     */
    public function create()
    {
        $classes = ClassModel::where('status', 'active')->orderBy('name')->get();
        return view('admin.students.create', compact('classes'));
    }

    /**
     * Store a newly created student in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'admission_number' => 'required|unique:students,admission_number',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'state' => 'nullable|string|max:100',
            'lga' => 'nullable|string|max:100',
            'nationality' => 'nullable|string|max:100',
            'religion' => 'nullable|string|max:50',
            'blood_group' => 'nullable|string|max:10',
            'genotype' => 'nullable|string|max:10',
            'class_id' => 'nullable|exists:classes,id',
            'admission_date' => 'required|date',
            'parent_info' => 'nullable|array',
            'guardian_info' => 'nullable|array',
            'medical_info' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Student::create($request->all());

        return redirect()->route('admin.students.index')
            ->with('success', 'Student created successfully.');
    }

    /**
     * Display the specified student.
     */
    public function show(Student $student)
    {
        $student->load('class', 'previousClass');
        return view('admin.students.show', compact('student'));
    }

    /**
     * Show the form for editing the specified student.
     */
    public function edit(Student $student)
    {
        $classes = ClassModel::where('status', 'active')->orderBy('name')->get();
        return view('admin.students.edit', compact('student', 'classes'));
    }

    /**
     * Update the specified student in storage.
     */
    public function update(Request $request, Student $student)
    {
        $validator = Validator::make($request->all(), [
            'admission_number' => 'required|unique:students,admission_number,' . $student->id,
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'state' => 'nullable|string|max:100',
            'lga' => 'nullable|string|max:100',
            'nationality' => 'nullable|string|max:100',
            'religion' => 'nullable|string|max:50',
            'blood_group' => 'nullable|string|max:10',
            'genotype' => 'nullable|string|max:10',
            'class_id' => 'nullable|exists:classes,id',
            'admission_date' => 'required|date',
            'status' => 'required|in:active,inactive,graduated,withdrawn',
            'parent_info' => 'nullable|array',
            'guardian_info' => 'nullable|array',
            'medical_info' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $student->update($request->all());

        return redirect()->route('admin.students.index')
            ->with('success', 'Student updated successfully.');
    }

    /**
     * Remove the specified student from storage.
     */
    public function destroy(Student $student)
    {
        $student->delete();

        return redirect()->route('admin.students.index')
            ->with('success', 'Student deleted successfully.');
    }

    /**
     * Show form for bulk promotion.
     */
    public function showPromoteForm()
    {
        $classes = ClassModel::where('status', 'active')->orderBy('name')->get();
        return view('admin.students.promote', compact('classes'));
    }

    /**
     * Bulk promote students.
     */
    public function bulkPromote(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from_class_id' => 'required|exists:classes,id',
            'to_class_id' => 'required|exists:classes,id|different:from_class_id',
            'student_ids' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $query = Student::where('class_id', $request->from_class_id)
            ->where('status', 'active');

        if ($request->filled('student_ids')) {
            $query->whereIn('id', $request->student_ids);
        }

        $students = $query->get();

        DB::beginTransaction();
        try {
            foreach ($students as $student) {
                $student->promote($request->to_class_id);
            }
            DB::commit();

            return redirect()->route('admin.students.index')
                ->with('success', "{$students->count()} student(s) promoted successfully.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error promoting students: ' . $e->getMessage());
        }
    }

    /**
     * Bulk demote students.
     */
    public function bulkDemote(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from_class_id' => 'required|exists:classes,id',
            'to_class_id' => 'required|exists:classes,id|different:from_class_id',
            'student_ids' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $query = Student::where('class_id', $request->from_class_id)
            ->where('status', 'active');

        if ($request->filled('student_ids')) {
            $query->whereIn('id', $request->student_ids);
        }

        $students = $query->get();

        DB::beginTransaction();
        try {
            foreach ($students as $student) {
                $student->demote($request->to_class_id);
            }
            DB::commit();

            return redirect()->route('admin.students.index')
                ->with('success', "{$students->count()} student(s) demoted successfully.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error demoting students: ' . $e->getMessage());
        }
    }

    /**
     * Bulk graduate students.
     */
    public function bulkGraduate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator);
        }

        DB::beginTransaction();
        try {
            $students = Student::whereIn('id', $request->student_ids)
                ->where('status', 'active')
                ->get();

            foreach ($students as $student) {
                $student->graduate();
            }

            DB::commit();

            return redirect()->route('admin.students.index')
                ->with('success', "{$students->count()} student(s) graduated successfully.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error graduating students: ' . $e->getMessage());
        }
    }

    /**
     * Show bulk upload form.
     */
    public function showUploadForm()
    {
        $classes = ClassModel::where('status', 'active')->orderBy('name')->get();
        return view('admin.students.upload', compact('classes'));
    }

    /**
     * Process bulk upload.
     */
    public function processUpload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx,xls,csv|max:10240',
            'class_id' => 'nullable|exists:classes,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $path = $request->file('file')->getRealPath();
            $data = array_map('str_getcsv', file($path));
            $header = array_shift($data);

            $successCount = 0;
            $errorCount = 0;
            $errors = [];

            DB::beginTransaction();
            try {
                foreach ($data as $index => $row) {
                    if (empty($row[0])) continue; // Skip empty rows

                    $row = array_combine($header, $row);

                    try {
                        Student::create([
                            'admission_number' => $row['admission_number'] ?? 'AUTO-' . (time() + $index),
                            'first_name' => $row['first_name'] ?? '',
                            'last_name' => $row['last_name'] ?? '',
                            'middle_name' => $row['middle_name'] ?? null,
                            'date_of_birth' => $row['date_of_birth'] ?? now(),
                            'gender' => $row['gender'] ?? 'male',
                            'email' => $row['email'] ?? null,
                            'phone' => $row['phone'] ?? null,
                            'address' => $row['address'] ?? null,
                            'state' => $row['state'] ?? null,
                            'lga' => $row['lga'] ?? null,
                            'nationality' => $row['nationality'] ?? 'Nigerian',
                            'religion' => $row['religion'] ?? null,
                            'blood_group' => $row['blood_group'] ?? null,
                            'genotype' => $row['genotype'] ?? null,
                            'class_id' => $request->class_id ?? null,
                            'admission_date' => $row['admission_date'] ?? now(),
                            'status' => 'active',
                        ]);
                        $successCount++;
                    } catch (\Exception $e) {
                        $errors[] = "Row " . ($index + 2) . ": " . $e->getMessage();
                        $errorCount++;
                    }
                }

                DB::commit();

                $message = "{$successCount} student(s) uploaded successfully.";
                if ($errorCount > 0) {
                    $message .= " {$errorCount} failed.";
                }

                return redirect()->route('admin.students.index')
                    ->with('success', $message);
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error uploading file: ' . $e->getMessage());
        }
    }

    /**
     * Download sample CSV template.
     */
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="student_template.csv"',
        ];

        $columns = [
            'admission_number',
            'first_name',
            'last_name',
            'middle_name',
            'date_of_birth',
            'gender',
            'email',
            'phone',
            'address',
            'state',
            'lga',
            'nationality',
            'religion',
            'blood_group',
            'genotype',
            'admission_date',
        ];

        return response()->download(
            tempnam(sys_get_temp_dir(), 'template'),
            'student_template.csv',
            $headers
        )->setContent(implode("\n", [
            implode(',', $columns),
            'ADM001,John,Doe,Smith,2010-05-15,male,john@example.com,08012345678,"Lagos Street",Lagos,Ikeja,Nigerian,Christian,A+,AA,2024-01-15',
        ]));
    }
}
