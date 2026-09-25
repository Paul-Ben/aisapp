<?php

use App\Models\AcademicSession;
use App\Models\ClassCategory;
use App\Models\FeeItem;
use App\Models\Payment;
use App\Models\Result;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;

function propStudent(string $adm, ?int $classId, string $gender = 'male', string $status = 'active'): Student
{
    return Student::create([
        'admission_number' => $adm,
        'first_name' => 'Fn'.$adm,
        'last_name' => 'Ln'.$adm,
        'date_of_birth' => '2015-01-01',
        'gender' => $gender,
        'class_id' => $classId,
        'status' => $status,
        'admission_date' => '2025-09-01',
    ]);
}

beforeEach(function () {
    $this->session = AcademicSession::create(['session' => '2026/2027', 'term' => 'first', 'is_active' => true]);
    $category = ClassCategory::create(['name' => 'Primary']);
    $this->class = SchoolClass::create(['class_category_id' => $category->id, 'name' => 'Primary 1', 'is_active' => true]);
    $this->subject = Subject::create(['name' => 'Maths', 'code' => 'MTH', 'is_active' => true]);

    $this->s1 = propStudent('A1', $this->class->id, 'male');
    $this->s2 = propStudent('A2', $this->class->id, 'female');
    propStudent('A3', $this->class->id, 'female', 'graduated');

    $fee = FeeItem::create(['name' => 'Tuition', 'amount' => 1000, 'is_active' => true]);
    $fee->classes()->attach($this->class->id);

    Payment::create([
        'student_id' => $this->s1->id, 'fee_item_id' => $fee->id, 'academic_year_id' => $this->session->id,
        'term' => 'first', 'amount_paid' => 1000, 'status' => 'paid', 'paid_at' => '2026-09-10', 'gateway' => 'paystack',
    ]);

    foreach ([[$this->s1, 80, 'A'], [$this->s2, 40, 'F']] as [$student, $score, $grade]) {
        Result::create([
            'student_id' => $student->id, 'class_id' => $this->class->id, 'subject_id' => $this->subject->id,
            'academic_year_id' => $this->session->id, 'term' => 'first', 'total_score' => $score, 'grade' => $grade,
        ]);
    }

    $this->actingAs(User::factory()->create(['role' => 'proprietor', 'is_active' => true]));
});

it('shows headline figures on the dashboard', function () {
    $this->get(route('proprietor.dashboard'))
        ->assertOk()
        ->assertSee('Active students')
        ->assertSee('1,000.00')
        ->assertSee('50% of amount billed'); // 1000 collected of 2 active students x 1000
});

it('reports school overview counts', function () {
    $this->get(route('proprietor.overview'))
        ->assertOk()
        ->assertSee('School Overview')
        ->assertSee('Primary (1 classes)');
});

it('reports finance totals, methods and fee breakdown', function () {
    $this->get(route('proprietor.finance'))
        ->assertOk()
        ->assertSee('Tuition')
        ->assertSee('Online (1 payments)')
        ->assertSee('2,000.00'); // billed
});

it('reports academic averages and pass rate', function () {
    $this->get(route('proprietor.academics'))
        ->assertOk()
        ->assertSee('60.0')   // average of 80 and 40
        ->assertSee('50.0%')  // one of two entries passes
        ->assertSee('Grade A');
});

it('reports enrollment by class, gender and retention', function () {
    $this->get(route('proprietor.enrollment'))
        ->assertOk()
        ->assertSee('Primary 1')
        ->assertSee('100%'); // nobody withdrawn
});

it('handles a school with no active session or data', function () {
    AcademicSession::query()->update(['is_active' => false]);

    foreach (['dashboard', 'overview', 'finance', 'academics', 'enrollment'] as $page) {
        $this->get(route("proprietor.$page"))->assertOk();
    }
});

it('lets the proprietor pick a past period', function () {
    $old = AcademicSession::create(['session' => '2025/2026', 'term' => 'third', 'is_active' => false]);

    $this->get(route('proprietor.finance', ['session_id' => $old->id]))->assertOk()->assertSee('2025/2026');
    $this->get(route('proprietor.academics', ['session_id' => $old->id]))->assertOk()->assertSee('No results have been entered');
});

it('keeps the reports away from other roles', function () {
    $this->actingAs(User::factory()->create(['role' => 'staff', 'is_active' => true]));

    foreach (['dashboard', 'overview', 'finance', 'academics', 'enrollment'] as $page) {
        $this->get(route("proprietor.$page"))->assertForbidden();
    }
});
