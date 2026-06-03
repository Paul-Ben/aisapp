<?php

use App\Models\AcademicSession;
use App\Models\ClassCategory;
use App\Models\GradeScale;
use App\Models\Result;
use App\Models\ResultConfig;
use App\Models\SchoolClass;
use App\Models\Staff;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;

function resultPreviewSetup(bool $activateSession = true): array
{
    $user = User::factory()->create([
        'role' => 'staff',
        'is_active' => true,
    ]);

    $staff = Staff::create([
        'user_id' => $user->id,
        'staff_id' => 'STF-PREVIEW-1',
        'first_name' => 'Preview',
        'last_name' => 'Teacher',
        'email' => 'preview.teacher@example.com',
        'position' => 'Subject Teacher',
        'is_active' => true,
    ]);

    $category = ClassCategory::firstOrCreate(['name' => 'Preview Test Category']);
    $schoolClass = SchoolClass::firstOrCreate(
        ['name' => 'Preview Test Class', 'arm' => 'A'],
        ['class_category_id' => $category->id, 'is_active' => true],
    );

    $schoolClass->staff()->syncWithoutDetaching([$staff->id => ['role' => 'subject_teacher']]);

    $subject = Subject::firstOrCreate(
        ['code' => 'PRV101'],
        ['name' => 'Preview Subject', 'is_active' => true],
    );

    $schoolClass->subjects()->syncWithoutDetaching([$subject->id]);

    $config = ResultConfig::firstOrCreate(
        ['class_id' => $schoolClass->id],
        [
            'max_ca_score' => 40,
            'max_project_score' => 20,
            'max_exam_score' => 100,
            'project_enabled' => true,
        ],
    );

    GradeScale::firstOrCreate(
        ['result_config_id' => $config->id, 'grade' => 'A'],
        ['min_percentage' => 75, 'max_percentage' => 100],
    );
    GradeScale::firstOrCreate(
        ['result_config_id' => $config->id, 'grade' => 'B'],
        ['min_percentage' => 65, 'max_percentage' => 74],
    );
    GradeScale::firstOrCreate(
        ['result_config_id' => $config->id, 'grade' => 'C'],
        ['min_percentage' => 55, 'max_percentage' => 64],
    );
    GradeScale::firstOrCreate(
        ['result_config_id' => $config->id, 'grade' => 'D'],
        ['min_percentage' => 45, 'max_percentage' => 54],
    );
    GradeScale::firstOrCreate(
        ['result_config_id' => $config->id, 'grade' => 'F'],
        ['min_percentage' => 0, 'max_percentage' => 44],
    );

    $session = AcademicSession::firstOrCreate(
        ['session' => '2026/2027', 'term' => 'first'],
        ['is_active' => false],
    );

    if ($activateSession) {
        $session->setActive();
    } else {
        AcademicSession::query()->update(['is_active' => false]);
    }

    return [
        'user' => $user,
        'staff' => $staff,
        'category' => $category,
        'schoolClass' => $schoolClass,
        'subject' => $subject,
        'config' => $config,
        'session' => $session,
    ];
}

function resultPreviewMakeStudent(string $adm, string $first, string $last, int $classId, string $status = 'active'): Student
{
    return Student::create([
        'admission_number' => $adm,
        'first_name' => $first,
        'last_name' => $last,
        'date_of_birth' => '2015-01-01',
        'gender' => 'male',
        'class_id' => $classId,
        'status' => $status,
        'admission_date' => '2024-09-01',
    ]);
}

function resultPreviewMakeResult(int $studentId, int $classId, int $subjectId, int $sessionId, string $term, float $ca, ?float $project, float $exam, string $grade, string $remark): Result
{
    $total = $ca + ($project ?? 0) + $exam;

    return Result::create([
        'student_id' => $studentId,
        'class_id' => $classId,
        'subject_id' => $subjectId,
        'academic_year_id' => $sessionId,
        'term' => $term,
        'ca_score' => $ca,
        'project_score' => $project,
        'exam_score' => $exam,
        'total_score' => $total,
        'grade' => $grade,
        'remark' => $remark,
        'entered_by' => null,
    ]);
}

beforeEach(function () {
    $this->setup = resultPreviewSetup();
    $this->actingAs($this->setup['user']);
});

it('renders three tabs including the new Preview tab', function () {
    $this->get(route('staff.results.upload', [
        'classId' => $this->setup['schoolClass']->id,
        'subjectId' => $this->setup['subject']->id,
    ]))
        ->assertOk()
        ->assertSee('Excel Upload')
        ->assertSee('Manual Entry')
        ->assertSee('Preview')
        ->assertSee('id="preview-tab"', false);
});

it('shows the empty state when no results have been uploaded for the active term', function () {
    resultPreviewMakeStudent('AIS-PREV-1', 'Empty', 'Student', $this->setup['schoolClass']->id);

    $this->get(route('staff.results.upload', [
        'classId' => $this->setup['schoolClass']->id,
        'subjectId' => $this->setup['subject']->id,
    ]))
        ->assertOk()
        ->assertSee('No results uploaded yet', false);
});

it('lists uploaded results for the active term with grade and remark', function () {
    $student = resultPreviewMakeStudent('AIS-PREV-2', 'Listed', 'Student', $this->setup['schoolClass']->id);

    resultPreviewMakeResult(
        $student->id,
        $this->setup['schoolClass']->id,
        $this->setup['subject']->id,
        $this->setup['session']->id,
        'first',
        35.00,
        18.00,
        80.00,
        'A',
        'Excellent',
    );

    $this->get(route('staff.results.upload', [
        'classId' => $this->setup['schoolClass']->id,
        'subjectId' => $this->setup['subject']->id,
    ]))
        ->assertOk()
        ->assertSee('Listed')
        ->assertSee('Student')
        ->assertSee('Excellent')
        ->assertSee('133.00');
});

it('excludes results from a different session or term', function () {
    $student = resultPreviewMakeStudent('AIS-PREV-3', 'Excluded', 'Student', $this->setup['schoolClass']->id);

    $otherSession = AcademicSession::firstOrCreate(
        ['session' => '2025/2026', 'term' => 'second'],
        ['is_active' => false],
    );

    resultPreviewMakeResult(
        $student->id,
        $this->setup['schoolClass']->id,
        $this->setup['subject']->id,
        $otherSession->id,
        'second',
        30.00,
        15.00,
        70.00,
        'A',
        'Excellent',
    );

    $this->get(route('staff.results.upload', [
        'classId' => $this->setup['schoolClass']->id,
        'subjectId' => $this->setup['subject']->id,
    ]))
        ->assertOk()
        ->assertSee('No results uploaded yet', false)
        ->assertDontSee('Excluded Student');
});

it('lists students in the class that still do not have results for the active term', function () {
    $uploaded = resultPreviewMakeStudent('AIS-PREV-4A', 'Uploaded', 'Student', $this->setup['schoolClass']->id);
    $missing = resultPreviewMakeStudent('AIS-PREV-4B', 'Missing', 'Student', $this->setup['schoolClass']->id);

    resultPreviewMakeResult(
        $uploaded->id,
        $this->setup['schoolClass']->id,
        $this->setup['subject']->id,
        $this->setup['session']->id,
        'first',
        30.00,
        15.00,
        70.00,
        'B',
        'Very Good',
    );

    $this->get(route('staff.results.upload', [
        'classId' => $this->setup['schoolClass']->id,
        'subjectId' => $this->setup['subject']->id,
    ]))
        ->assertOk()
        ->assertSee('Still Missing', false)
        ->assertSee('Missing')
        ->assertSee('Student');
});

it('computes class average, highest, and lowest correctly', function () {
    $a = resultPreviewMakeStudent('AIS-PREV-5A', 'Alpha', 'Student', $this->setup['schoolClass']->id);
    $b = resultPreviewMakeStudent('AIS-PREV-5B', 'Beta', 'Student', $this->setup['schoolClass']->id);
    $c = resultPreviewMakeStudent('AIS-PREV-5C', 'Gamma', 'Student', $this->setup['schoolClass']->id);

    resultPreviewMakeResult($a->id, $this->setup['schoolClass']->id, $this->setup['subject']->id, $this->setup['session']->id, 'first', 30, 15, 30, 'D', 'Fair');
    resultPreviewMakeResult($b->id, $this->setup['schoolClass']->id, $this->setup['subject']->id, $this->setup['session']->id, 'first', 35, 18, 82, 'A', 'Excellent');
    resultPreviewMakeResult($c->id, $this->setup['schoolClass']->id, $this->setup['subject']->id, $this->setup['session']->id, 'first', 30, 15, 60, 'C', 'Good');

    $response = $this->get(route('staff.results.upload', [
        'classId' => $this->setup['schoolClass']->id,
        'subjectId' => $this->setup['subject']->id,
    ]))->assertOk();

    $stats = $response->viewData('resultStats');

    expect($stats['count'])->toBe(3);
    expect((float) $stats['highest'])->toBe(135.0);
    expect((float) $stats['lowest'])->toBe(75.0);
    expect((float) $stats['average'])->toBe(105.0);
});

it('shows the no-active-session notice when no session is active', function () {
    AcademicSession::query()->update(['is_active' => false]);

    $this->get(route('staff.results.upload', [
        'classId' => $this->setup['schoolClass']->id,
        'subjectId' => $this->setup['subject']->id,
    ]))
        ->assertOk()
        ->assertSee('No active academic session', false);
});

it('displays grade distribution summary', function () {
    $a = resultPreviewMakeStudent('AIS-PREV-6A', 'DistA', 'Student', $this->setup['schoolClass']->id);
    $b = resultPreviewMakeStudent('AIS-PREV-6B', 'DistB', 'Student', $this->setup['schoolClass']->id);

    resultPreviewMakeResult($a->id, $this->setup['schoolClass']->id, $this->setup['subject']->id, $this->setup['session']->id, 'first', 30, 15, 30, 'D', 'Fair');
    resultPreviewMakeResult($b->id, $this->setup['schoolClass']->id, $this->setup['subject']->id, $this->setup['session']->id, 'first', 35, 18, 82, 'A', 'Excellent');

    $this->get(route('staff.results.upload', [
        'classId' => $this->setup['schoolClass']->id,
        'subjectId' => $this->setup['subject']->id,
    ]))
        ->assertOk()
        ->assertSee('Grade Distribution')
        ->assertSee('A: 1')
        ->assertSee('D: 1');
});

it('forbids staff not assigned to the class from accessing the preview tab', function () {
    $intruder = User::factory()->create([
        'role' => 'staff',
        'is_active' => true,
    ]);
    Staff::create([
        'user_id' => $intruder->id,
        'staff_id' => 'STF-PREVIEW-INTRUDER',
        'first_name' => 'Intruder',
        'last_name' => 'Teacher',
        'email' => 'intruder.teacher@example.com',
        'position' => 'Subject Teacher',
        'is_active' => true,
    ]);
    $this->actingAs($intruder);

    $this->get(route('staff.results.upload', [
        'classId' => $this->setup['schoolClass']->id,
        'subjectId' => $this->setup['subject']->id,
    ]))->assertForbidden();
});
