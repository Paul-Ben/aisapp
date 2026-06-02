<?php

use App\Models\AcademicSession;
use App\Models\ClassCategory;
use App\Models\FeeItem;
use App\Models\Payment;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;

beforeEach(function () {
    $this->finance = User::factory()->create(['role' => 'finance_officer', 'is_active' => true]);
    $this->actingAs($this->finance);
});

function makeStudent(string $admissionNumber, string $firstName, string $lastName, ?int $classId = null, string $status = 'active'): Student
{
    return Student::create([
        'admission_number' => $admissionNumber,
        'first_name' => $firstName,
        'last_name' => $lastName,
        'date_of_birth' => '2015-01-01',
        'gender' => 'male',
        'class_id' => $classId,
        'status' => $status,
        'admission_date' => '2024-09-01',
    ]);
}

it('searches students by name, admission number, and class', function () {
    $category = ClassCategory::firstOrCreate(['name' => 'Pay Test Cat Search']);
    $class = SchoolClass::firstOrCreate(
        ['name' => 'Pay Test Class Search'],
        ['class_category_id' => $category->id, 'is_active' => true]
    );

    $byName = makeStudent('AIS-S-001', 'Adebayo', 'Ogunlesi', $class->id);
    $byAdmNo = makeStudent('AIS-2026-001', 'Chinedu', 'Okafor', $class->id);
    $graduated = makeStudent('AIS-S-002', 'Should', 'NotAppear', $class->id, 'graduated');

    $this->get(route('finance.payments.index', ['q' => 'Adebayo']))
        ->assertOk()
        ->assertSee('Adebayo')
        ->assertDontSee('NotAppear');

    $this->get(route('finance.payments.index', ['q' => 'AIS-2026-001']))
        ->assertOk()
        ->assertSee('AIS-2026-001');

    $this->get(route('finance.payments.index', ['q' => 'Pay Test Class Search']))
        ->assertOk()
        ->assertSee('Adebayo')
        ->assertSee('Chinedu');
});

it('records a new payment for the active term', function () {
    $session = AcademicSession::firstOrCreate(
        ['session' => '2026/2027', 'term' => 'first'],
        ['is_active' => true]
    );
    $session->setActive();

    $category = ClassCategory::firstOrCreate(['name' => 'Pay Test Cat New']);
    $class = SchoolClass::firstOrCreate(
        ['name' => 'Pay Test Class New'],
        ['class_category_id' => $category->id, 'is_active' => true]
    );
    $fee = FeeItem::create(['name' => 'Tuition', 'amount' => 50000, 'is_active' => true]);
    $fee->classes()->sync([$class->id]);

    $student = makeStudent('AIS-N-001', 'NewStudent', 'First', $class->id);

    $response = $this->put(route('finance.payments.save', ['student' => $student, 'fee' => $fee]), [
        'amount_paid' => 50000,
        'status' => 'paid',
        'reference' => 'TELLER-001',
        'notes' => 'Bank transfer',
        'paid_at' => '2026-05-02',
    ]);

    $response->assertRedirect(route('finance.payments.student', ['student' => $student, 'session_id' => $session->id]));

    $payment = Payment::where('student_id', $student->id)
        ->where('fee_item_id', $fee->id)
        ->where('academic_year_id', $session->id)
        ->where('term', 'first')
        ->first();

    expect($payment)->not->toBeNull();
    expect((float) $payment->amount_paid)->toBe(50000.0);
    expect($payment->status)->toBe('paid');
    expect($payment->reference)->toBe('TELLER-001');
    expect($payment->recorded_by)->toBe($this->finance->id);
});

it('updates an existing payment for the same student + fee + term (upsert)', function () {
    $session = AcademicSession::firstOrCreate(
        ['session' => '2026/2027', 'term' => 'second'],
        ['is_active' => true]
    );
    $session->setActive();

    $category = ClassCategory::firstOrCreate(['name' => 'Pay Test Cat Upd']);
    $class = SchoolClass::firstOrCreate(
        ['name' => 'Pay Test Class Upd'],
        ['class_category_id' => $category->id, 'is_active' => true]
    );
    $fee = FeeItem::create(['name' => 'PTA Levy', 'amount' => 10000, 'is_active' => true]);
    $fee->classes()->sync([$class->id]);

    $student = makeStudent('AIS-U-001', 'UpdStudent', 'First', $class->id);

    Payment::create([
        'student_id' => $student->id,
        'fee_item_id' => $fee->id,
        'academic_year_id' => $session->id,
        'term' => 'second',
        'amount_paid' => 5000,
        'status' => 'part',
        'paid_at' => '2026-05-02',
    ]);

    $this->put(route('finance.payments.save', ['student' => $student, 'fee' => $fee]), [
        'amount_paid' => 10000,
        'status' => 'paid',
        'paid_at' => '2026-05-02',
    ])->assertRedirect();

    $count = Payment::where('student_id', $student->id)
        ->where('fee_item_id', $fee->id)
        ->where('academic_year_id', $session->id)
        ->where('term', 'second')
        ->count();

    expect($count)->toBe(1);

    $updated = Payment::where('student_id', $student->id)
        ->where('fee_item_id', $fee->id)
        ->where('academic_year_id', $session->id)
        ->where('term', 'second')
        ->first();

    expect((float) $updated->amount_paid)->toBe(10000.0);
    expect($updated->status)->toBe('paid');
});

it('treats payments in different terms as separate records', function () {
    $first = AcademicSession::firstOrCreate(
        ['session' => '2026/2027', 'term' => 'first'],
        ['is_active' => true]
    );
    $second = AcademicSession::firstOrCreate(
        ['session' => '2026/2027', 'term' => 'second'],
        ['is_active' => false]
    );

    $category = ClassCategory::firstOrCreate(['name' => 'Pay Test Cat Term']);
    $class = SchoolClass::firstOrCreate(
        ['name' => 'Pay Test Class Term'],
        ['class_category_id' => $category->id, 'is_active' => true]
    );
    $fee = FeeItem::create(['name' => 'Books', 'amount' => 15000, 'is_active' => true]);
    $fee->classes()->sync([$class->id]);

    $student = makeStudent('AIS-T-001', 'TermStudent', 'First', $class->id);

    Payment::create([
        'student_id' => $student->id,
        'fee_item_id' => $fee->id,
        'academic_year_id' => $first->id,
        'term' => 'first',
        'amount_paid' => 15000,
        'status' => 'paid',
        'paid_at' => '2026-01-15',
    ]);

    $second->setActive();

    $this->put(route('finance.payments.save', ['student' => $student, 'fee' => $fee]), [
        'amount_paid' => 10000,
        'status' => 'part',
        'paid_at' => '2026-05-02',
    ])->assertRedirect();

    $count = Payment::where('student_id', $student->id)
        ->where('fee_item_id', $fee->id)
        ->count();

    expect($count)->toBe(2);

    $firstTerm = Payment::where('student_id', $student->id)
        ->where('fee_item_id', $fee->id)
        ->where('term', 'first')
        ->first();
    $secondTerm = Payment::where('student_id', $student->id)
        ->where('fee_item_id', $fee->id)
        ->where('term', 'second')
        ->first();

    expect((float) $firstTerm->amount_paid)->toBe(15000.0);
    expect($firstTerm->status)->toBe('paid');
    expect((float) $secondTerm->amount_paid)->toBe(10000.0);
    expect($secondTerm->status)->toBe('part');
});

it('redirects to the student page with an error when no active session and no session_id is given', function () {
    AcademicSession::query()->update(['is_active' => false]);

    $category = ClassCategory::firstOrCreate(['name' => 'Pay Test Cat NoActive']);
    $class = SchoolClass::firstOrCreate(
        ['name' => 'Pay Test Class NoActive'],
        ['class_category_id' => $category->id, 'is_active' => true]
    );
    $fee = FeeItem::create(['name' => 'No Session Fee', 'amount' => 5000, 'is_active' => true]);
    $fee->classes()->sync([$class->id]);

    $student = makeStudent('AIS-NS-001', 'NoSession', 'Student', $class->id);

    $response = $this->get(route('finance.payments.form', ['student' => $student, 'fee' => $fee]));

    $response->assertRedirect(route('finance.payments.student', $student));
    $response->assertSessionHas('error', 'No academic session is selected or active. Ask the administrator to set an active session in the admin dashboard.');
});

it('opens the form when a non-active session_id is passed explicitly', function () {
    $category = ClassCategory::firstOrCreate(['name' => 'Pay Test Cat Explicit']);
    $class = SchoolClass::firstOrCreate(
        ['name' => 'Pay Test Class Explicit'],
        ['class_category_id' => $category->id, 'is_active' => true]
    );
    $fee = FeeItem::create(['name' => 'Backfill Fee', 'amount' => 7500, 'is_active' => true]);
    $fee->classes()->sync([$class->id]);

    $student = makeStudent('AIS-NS-002', 'Backfill', 'Student', $class->id);

    $session = AcademicSession::firstOrCreate(
        ['session' => '2025/2026', 'term' => 'second'],
        ['is_active' => false]
    );
    AcademicSession::query()->where('id', '!=', $session->id)->update(['is_active' => false]);

    $response = $this->get(route('finance.payments.form', [
        'student' => $student,
        'fee' => $fee,
        'session_id' => $session->id,
    ]));

    $response->assertOk();
    $response->assertViewIs('finance.payments.form');
    $response->assertViewHas('session.id', $session->id);
});

it('records the payment under the passed session_id even when it is not the active one', function () {
    $category = ClassCategory::firstOrCreate(['name' => 'Pay Test Cat SaveExplicit']);
    $class = SchoolClass::firstOrCreate(
        ['name' => 'Pay Test Class SaveExplicit'],
        ['class_category_id' => $category->id, 'is_active' => true]
    );
    $fee = FeeItem::create(['name' => 'Backfill Save Fee', 'amount' => 20000, 'is_active' => true]);
    $fee->classes()->sync([$class->id]);

    $student = makeStudent('AIS-NS-003', 'SaveExplicit', 'Student', $class->id);

    $session = AcademicSession::firstOrCreate(
        ['session' => '2025/2026', 'term' => 'third'],
        ['is_active' => false]
    );
    AcademicSession::query()->where('id', '!=', $session->id)->update(['is_active' => false]);

    $response = $this->put(route('finance.payments.save', [
        'student' => $student,
        'fee' => $fee,
        'session_id' => $session->id,
    ]), [
        'amount_paid' => 20000,
        'status' => 'paid',
        'paid_at' => '2026-05-02',
    ]);

    $response->assertRedirect(route('finance.payments.student', [
        'student' => $student->id,
        'session_id' => $session->id,
    ]));

    $payment = Payment::where('student_id', $student->id)
        ->where('fee_item_id', $fee->id)
        ->where('academic_year_id', $session->id)
        ->where('term', 'third')
        ->first();

    expect($payment)->not->toBeNull();
    expect((float) $payment->amount_paid)->toBe(20000.0);
    expect($payment->recorded_by)->toBe($this->finance->id);
});
