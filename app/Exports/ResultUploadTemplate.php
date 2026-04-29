<?php

namespace App\Exports;

use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\ResultConfig;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ResultUploadTemplate implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $schoolClass;
    protected $subject;
    protected $resultConfig;

    public function __construct(SchoolClass $schoolClass, Subject $subject, ?ResultConfig $resultConfig)
    {
        $this->schoolClass = $schoolClass;
        $this->subject = $subject;
        $this->resultConfig = $resultConfig;
    }

    public function collection()
    {
        return $this->schoolClass->students()->orderBy('last_name')->orderBy('first_name')->get();
    }

    public function headings(): array
    {
        $headings = ['Admission No', 'Student Name', 'CA Score'];
        
        if ($this->resultConfig && $this->resultConfig->has_project) {
            $headings[] = 'Project Score';
        }
        
        $headings[] = 'Exam Score';
        
        return $headings;
    }

    public function map($student): array
    {
        $row = [
            $student->admission_number ?? '',
            $student->full_name,
            '', // CA Score - empty for input
        ];

        if ($this->resultConfig && $this->resultConfig->has_project) {
            $row[] = ''; // Project Score - empty for input
        }

        $row[] = ''; // Exam Score - empty for input

        return $row;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '4CAF50']]],
        ];
    }
}
