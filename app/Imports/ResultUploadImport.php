<?php

namespace App\Imports;

use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\ResultConfig;
use App\Models\Result;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Auth;

class ResultUploadImport implements ToModel, WithHeadingRow, WithValidation
{
    protected $schoolClass;
    protected $subject;
    protected $resultConfig;
    protected $enteredBy;

    public function __construct(SchoolClass $schoolClass, Subject $subject, ?ResultConfig $resultConfig, int $enteredBy)
    {
        $this->schoolClass = $schoolClass;
        $this->subject = $subject;
        $this->resultConfig = $resultConfig;
        $this->enteredBy = $enteredBy;
    }

    public function model(array $row)
    {
        // Find student by admission number
        $student = User::where('admission_number', $row['admission_no'])
            ->orWhere('admission_number', $row['admission number'])
            ->first();

        if (!$student) {
            return null; // Skip this row
        }

        $caScore = isset($row['ca_score']) ? (float)$row['ca_score'] : null;
        $projectScore = isset($row['project_score']) ? (float)$row['project_score'] : null;
        $examScore = isset($row['exam_score']) ? (float)$row['exam_score'] : null;

        // Validate scores against config
        if ($caScore !== null && $this->resultConfig && $caScore > $this->resultConfig->max_ca_score) {
            throw new \Exception("CA score for {$student->full_name} exceeds maximum ({$this->resultConfig->max_ca_score})");
        }
        
        if ($projectScore !== null && $this->resultConfig && $this->resultConfig->has_project && $projectScore > $this->resultConfig->max_project_score) {
            throw new \Exception("Project score for {$student->full_name} exceeds maximum ({$this->resultConfig->max_project_score})");
        }
        
        if ($examScore !== null && $this->resultConfig && $examScore > $this->resultConfig->max_exam_score) {
            throw new \Exception("Exam score for {$student->full_name} exceeds maximum ({$this->resultConfig->max_exam_score})");
        }

        $totalScore = ($caScore ?? 0) + ($projectScore ?? 0) + ($examScore ?? 0);
        $grade = $this->calculateGrade($totalScore);
        $remark = $this->generateRemark($grade);

        return new Result([
            'student_id' => $student->id,
            'class_id' => $this->schoolClass->id,
            'subject_id' => $this->subject->id,
            'academic_year_id' => $this->schoolClass->academic_year_id,
            'term' => 'First', // Default term, can be customized
            'ca_score' => $caScore,
            'project_score' => $projectScore,
            'exam_score' => $examScore,
            'total_score' => $totalScore,
            'grade' => $grade,
            'remark' => $remark,
            'entered_by' => $this->enteredBy,
        ]);
    }

    public function rules(): array
    {
        return [
            'admission_no' => 'required|string',
            'ca_score' => 'nullable|numeric',
            'project_score' => 'nullable|numeric',
            'exam_score' => 'nullable|numeric',
        ];
    }

    private function calculateGrade($totalScore)
    {
        if (!$this->resultConfig) {
            return 'F';
        }

        $gradeScale = \App\Models\GradeScale::where('result_config_id', $this->resultConfig->id)
            ->orderBy('min_percentage', 'desc')
            ->get();

        foreach ($gradeScale as $scale) {
            if ($totalScore >= $scale->min_percentage) {
                return $scale->grade;
            }
        }

        return 'F';
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
