<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\SchoolClass;
use App\Models\ClassCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubjectManagementController extends Controller
{
    /**
     * Display a listing of subjects
     */
    public function index()
    {
        $subjects = Subject::withCount('classes')->orderBy('name')->get();
        $classes = SchoolClass::with('category')->orderBy('name')->orderBy('arm')->get();
        
        return view('admin.subjects.index', compact('subjects', 'classes'));
    }

    /**
     * Store a new subject
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:subjects,code',
            'description' => 'nullable|string',
        ]);

        Subject::create($request->only(['name', 'code', 'description']));

        return redirect()->back()->with('success', 'Subject created successfully.');
    }

    /**
     * Update an existing subject
     */
    public function update(Request $request, $id)
    {
        $subject = Subject::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:subjects,code,' . $id,
            'description' => 'nullable|string',
        ]);

        $subject->update($request->only(['name', 'code', 'description']));

        return redirect()->back()->with('success', 'Subject updated successfully.');
    }

    /**
     * Delete a subject
     */
    public function destroy($id)
    {
        $subject = Subject::findOrFail($id);
        
        // Check if subject is assigned to any class
        if ($subject->classes()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete subject. It is assigned to one or more classes.');
        }

        $subject->delete();
        return redirect()->back()->with('success', 'Subject deleted successfully.');
    }

    /**
     * Assign subjects to a class
     */
    public function assignToClass(Request $request, $classId)
    {
        $schoolClass = SchoolClass::findOrFail($classId);

        $request->validate([
            'subjects' => 'required|array',
            'subjects.*' => 'exists:subjects,id',
            'is_compulsory' => 'array',
        ]);

        DB::beginTransaction();
        try {
            // Sync subjects with the class
            $syncData = [];
            foreach ($request->subjects as $subjectId) {
                $syncData[$subjectId] = [
                    'is_compulsory' => in_array($subjectId, $request->is_compulsory ?? []),
                ];
            }

            $schoolClass->subjects()->sync($syncData);

            DB::commit();

            return redirect()->back()->with('success', 'Subjects assigned to class successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to assign subjects: ' . $e->getMessage());
        }
    }

    /**
     * Remove a subject from a class
     */
    public function removeFromClass($classId, $subjectId)
    {
        $schoolClass = SchoolClass::findOrFail($classId);
        $schoolClass->subjects()->detach($subjectId);

        return redirect()->back()->with('success', 'Subject removed from class successfully.');
    }

    /**
     * Get subjects for a specific class (AJAX)
     */
    public function getSubjectsForClass($classId)
    {
        $schoolClass = SchoolClass::with(['subjects' => function($query) {
            $query->withPivot('is_compulsory');
        }])->findOrFail($classId);

        return response()->json([
            'subjects' => $schoolClass->subjects,
        ]);
    }
}
