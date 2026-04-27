<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ResultConfig;
use App\Models\GradeScale;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResultConfigController extends Controller
{
    /**
     * Display a listing of result configurations
     */
    public function index()
    {
        $classes = SchoolClass::with(['category', 'resultConfig.gradeScales'])
            ->orderBy('name')
            ->orderBy('arm')
            ->get();

        return view('admin.result-config.index', compact('classes'));
    }

    /**
     * Show the form for editing result configuration for a specific class
     */
    public function edit($classId)
    {
        $schoolClass = SchoolClass::findOrFail($classId);
        $resultConfig = ResultConfig::getOrCreateForClass($classId);
        
        return view('admin.result-config.edit', compact('schoolClass', 'resultConfig'));
    }

    /**
     * Update result configuration for a class
     */
    public function update(Request $request, $classId)
    {
        $request->validate([
            'max_ca_score' => 'required|integer|min:0|max:100',
            'max_project_score' => 'nullable|integer|min:0|max:100',
            'max_exam_score' => 'required|integer|min:0|max:100',
            'project_enabled' => 'boolean',
            'grade_scales' => 'required|array|min:6',
            'grade_scales.*.grade' => 'required|string|size:1',
            'grade_scales.*.min_percentage' => 'required|integer|min:0|max:100',
            'grade_scales.*.max_percentage' => 'required|integer|min:0|max:100',
        ]);

        DB::beginTransaction();
        try {
            $resultConfig = ResultConfig::getOrCreateForClass($classId);
            
            // Update result config
            $resultConfig->update([
                'max_ca_score' => $request->max_ca_score,
                'max_project_score' => $request->max_project_score ?? 0,
                'max_exam_score' => $request->max_exam_score,
                'project_enabled' => $request->has('project_enabled'),
            ]);

            // Delete existing grade scales
            $resultConfig->gradeScales()->delete();

            // Create new grade scales
            foreach ($request->grade_scales as $gradeData) {
                $resultConfig->gradeScales()->create([
                    'grade' => strtoupper($gradeData['grade']),
                    'min_percentage' => $gradeData['min_percentage'],
                    'max_percentage' => $gradeData['max_percentage'],
                ]);
            }

            DB::commit();

            return redirect()->route('admin.result-config.index')
                ->with('success', 'Result configuration updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to update result configuration: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Get grade for a percentage based on class configuration
     */
    public function getGrade($classId, $percentage)
    {
        $resultConfig = ResultConfig::where('class_id', $classId)->first();
        
        if (!$resultConfig) {
            return response()->json(['grade' => null]);
        }

        $grade = GradeScale::getGradeForPercentage($resultConfig->id, $percentage);
        
        return response()->json(['grade' => $grade]);
    }
}
