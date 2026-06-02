<?php

use App\Models\AcademicSession;
use App\Models\ClassCategory;
use App\Models\FeeItem;
use App\Models\Payment;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;

function revenueSetupSession(string $term = 'first', bool $activate = true): AcademicSession
{
    $session = AcademicSession::firstOrCreate(
        ['session' => '2026/2027', 'term' => $term],
        ['is_active' => false],
    );

    if ($activate) {
        $session->setActive();
    }

    return $session;
}

function revenueSetupClass(string $name = 'Rev Test Class'): SchoolClass
{
    $category = ClassCategory::firstOrCreate(['name' => 'Rev Test Category']);

    return SchoolClass::firstOrCreate(
        ['name' => $name],
        ['class_category_id' => $category->id, 'is_active' => true]
    );
}

function revenueMakeStudent(string $adm, string $first, string $last, ?int $classId, string $status = 'active'): Student
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

function revenueMakePayment(int $studentId, int $feeId, int $sessionId, string $term, float $amount, ?string $gateway, string $reference = 'RCP-TEST'): Payment
{
    return Payment::create([
        'student_id' => $studentId,
        'fee_item_id' => $feeId,
        'academic_year_id' => $sessionId,
        'term' => $term,
        'amount_paid' => $amount,
        'status' => 'paid',
        'reference' => $reference,
        'paid_at' => '2026-05-02',
        'gateway' => $gateway,
        'gateway_reference' => $gateway ? 'AIS-'.$reference : null,
        'gateway_channel' => $gateway === 'paystack' ? 'card' : null,
    ]);
}

beforeEach(function () {
    $this->finance = User::factory()->create(['role' => 'finance_officer', 'is_active' => true]);
    $this->actingAs($this->finance);
});

it('renders the revenue tracking page for the finance officer', function () {
    revenueSetupSession();

    $this->get(route('finance.revenue.index'))
        ->assertOk()
        ->assertSee('Revenue Tracking')
        ->assertSee('Active term');
});

it('defaults to payments in the active term when no filter is applied', function () {
    $active = revenueSetupSession('first', true);
    $other = revenueSetupSession('second', false);

    $class = revenueSetupClass();
    $fee = FeeItem::create(['name' => 'Default Term Fee', 'amount' => 10000, 'is_active' => true]);
    $fee->classes()->sync([$class->id]);

    $student = revenueMakeStudent('AIS-RV-001', 'Default', 'Term', $class->id);
    revenueMakePayment($student->id, $fee->id, $active->id, 'first', 10000, null, 'RCP-ACTIVE-1');
    revenueMakePayment($student->id, $fee->id, $other->id, 'second', 99999, null, 'RCP-OTHER-1');

    $this->get(route('finance.revenue.index'))
        ->assertOk()
        ->assertSee('RCP-ACTIVE-1')
        ->assertDontSee('RCP-OTHER-1');
});

it('shows all payments when no active session exists and no filter is applied', function () {
    AcademicSession::query()->update(['is_active' => false]);
    $session = revenueSetupSession('first', false);

    $class = revenueSetupClass();
    $fee = FeeItem::create(['name' => 'No Active Session Fee', 'amount' => 5000, 'is_active' => true]);
    $fee->classes()->sync([$class->id]);

    $student = revenueMakeStudent('AIS-RV-NAS-001', 'NoActive', 'Session', $class->id);
    revenueMakePayment($student->id, $fee->id, $session->id, 'first', 5000, null, 'RCP-NOACTIVE-1');

    $this->get(route('finance.revenue.index'))
        ->assertOk()
        ->assertSee('RCP-NOACTIVE-1')
        ->assertSee('No active academic session', false);
});

it('filters by student name (first, last, or middle)', function () {
    $session = revenueSetupSession();
    $class = revenueSetupClass();
    $fee = FeeItem::create(['name' => 'Name Filter Fee', 'amount' => 1000, 'is_active' => true]);
    $fee->classes()->sync([$class->id]);

    $ada = revenueMakeStudent('AIS-RV-002', 'Ada', 'Lovelace', $class->id);
    $bob = revenueMakeStudent('AIS-RV-003', 'Bob', 'Builder', $class->id);

    revenueMakePayment($ada->id, $fee->id, $session->id, 'first', 1000, null, 'RCP-NAME-ADA');
    revenueMakePayment($bob->id, $fee->id, $session->id, 'first', 1000, null, 'RCP-NAME-BOB');

    $this->get(route('finance.revenue.index', ['student_name' => 'Ada']))
        ->assertOk()
        ->assertSee('RCP-NAME-ADA')
        ->assertDontSee('RCP-NAME-BOB');
});

it('filters by student number (admission number)', function () {
    $session = revenueSetupSession();
    $class = revenueSetupClass();
    $fee = FeeItem::create(['name' => 'Admno Filter Fee', 'amount' => 1000, 'is_active' => true]);
    $fee->classes()->sync([$class->id]);

    $a = revenueMakeStudent('AIS-RV-ADMNO-1', 'Admno', 'One', $class->id);
    $b = revenueMakeStudent('AIS-RV-ADMNO-2', 'Admno', 'Two', $class->id);

    revenueMakePayment($a->id, $fee->id, $session->id, 'first', 1000, null, 'RCP-ADMNO-1');
    revenueMakePayment($b->id, $fee->id, $session->id, 'first', 1000, null, 'RCP-ADMNO-2');

    $this->get(route('finance.revenue.index', ['student_number' => 'AIS-RV-ADMNO-1']))
        ->assertOk()
        ->assertSee('RCP-ADMNO-1')
        ->assertDontSee('RCP-ADMNO-2');
});

it('filters by class', function () {
    $session = revenueSetupSession();
    $classA = revenueSetupClass('Class A Rev');
    $classB = revenueSetupClass('Class B Rev');
    $fee = FeeItem::create(['name' => 'Class Filter Fee', 'amount' => 1000, 'is_active' => true]);
    $fee->classes()->sync([$classA->id, $classB->id]);

    $studentA = revenueMakeStudent('AIS-RV-CL-A', 'ClassA', 'Student', $classA->id);
    $studentB = revenueMakeStudent('AIS-RV-CL-B', 'ClassB', 'Student', $classB->id);

    revenueMakePayment($studentA->id, $fee->id, $session->id, 'first', 1000, null, 'RCP-CLASS-A');
    revenueMakePayment($studentB->id, $fee->id, $session->id, 'first', 1000, null, 'RCP-CLASS-B');

    $this->get(route('finance.revenue.index', ['class_id' => $classA->id]))
        ->assertOk()
        ->assertSee('RCP-CLASS-A')
        ->assertDontSee('RCP-CLASS-B');
});

it('filters by online payment method', function () {
    $session = revenueSetupSession();
    $class = revenueSetupClass();
    $onlineFee = FeeItem::create(['name' => 'Online Method Fee', 'amount' => 1000, 'is_active' => true]);
    $onlineFee->classes()->sync([$class->id]);
    $manualFee = FeeItem::create(['name' => 'Manual Method Fee', 'amount' => 2000, 'is_active' => true]);
    $manualFee->classes()->sync([$class->id]);

    $student = revenueMakeStudent('AIS-RV-METH-1', 'Method', 'Test', $class->id);
    revenueMakePayment($student->id, $onlineFee->id, $session->id, 'first', 1000, 'paystack', 'RCP-ONLINE-1');
    revenueMakePayment($student->id, $manualFee->id, $session->id, 'first', 2000, null, 'RCP-MANUAL-1');

    $this->get(route('finance.revenue.index', ['payment_method' => 'online']))
        ->assertOk()
        ->assertSee('RCP-ONLINE-1')
        ->assertDontSee('RCP-MANUAL-1');
});

it('filters by manual payment method', function () {
    $session = revenueSetupSession();
    $class = revenueSetupClass();
    $onlineFee = FeeItem::create(['name' => 'Online Method Fee B', 'amount' => 1000, 'is_active' => true]);
    $onlineFee->classes()->sync([$class->id]);
    $manualFee = FeeItem::create(['name' => 'Manual Method Fee B', 'amount' => 2000, 'is_active' => true]);
    $manualFee->classes()->sync([$class->id]);

    $student = revenueMakeStudent('AIS-RV-METH-2', 'Method', 'Test2', $class->id);
    revenueMakePayment($student->id, $onlineFee->id, $session->id, 'first', 1000, 'paystack', 'RCP-ONLINE-2');
    revenueMakePayment($student->id, $manualFee->id, $session->id, 'first', 2000, null, 'RCP-MANUAL-2');

    $this->get(route('finance.revenue.index', ['payment_method' => 'manual']))
        ->assertOk()
        ->assertSee('RCP-MANUAL-2')
        ->assertDontSee('RCP-ONLINE-2');
});

it('filters by session', function () {
    $active = revenueSetupSession('first', true);
    $other = revenueSetupSession('second', false);

    $class = revenueSetupClass();
    $fee = FeeItem::create(['name' => 'Session Filter Fee', 'amount' => 1000, 'is_active' => true]);
    $fee->classes()->sync([$class->id]);

    $student = revenueMakeStudent('AIS-RV-SES-1', 'Session', 'Test', $class->id);
    revenueMakePayment($student->id, $fee->id, $active->id, 'first', 1000, null, 'RCP-SESSION-ACTIVE');
    revenueMakePayment($student->id, $fee->id, $other->id, 'second', 1000, null, 'RCP-SESSION-OTHER');

    $this->get(route('finance.revenue.index', ['academic_year_id' => $other->id]))
        ->assertOk()
        ->assertSee('RCP-SESSION-OTHER')
        ->assertDontSee('RCP-SESSION-ACTIVE');
});

it('filters by term', function () {
    $session = revenueSetupSession('first');
    $class = revenueSetupClass();
    $fee = FeeItem::create(['name' => 'Term Filter Fee', 'amount' => 1000, 'is_active' => true]);
    $fee->classes()->sync([$class->id]);

    $student = revenueMakeStudent('AIS-RV-TRM-1', 'Term', 'Test', $class->id);
    revenueMakePayment($student->id, $fee->id, $session->id, 'first', 1000, null, 'RCP-TERM-FIRST');
    revenueMakePayment($student->id, $fee->id, $session->id, 'second', 1000, null, 'RCP-TERM-SECOND');

    $this->get(route('finance.revenue.index', ['term' => 'second', 'academic_year_id' => $session->id]))
        ->assertOk()
        ->assertSee('RCP-TERM-SECOND')
        ->assertDontSee('RCP-TERM-FIRST');
});

it('combines multiple filters', function () {
    $session = revenueSetupSession();
    $classA = revenueSetupClass('Combo A');
    $classB = revenueSetupClass('Combo B');
    $onlineFee = FeeItem::create(['name' => 'Combo Online Fee', 'amount' => 1000, 'is_active' => true]);
    $onlineFee->classes()->sync([$classA->id, $classB->id]);
    $manualFee = FeeItem::create(['name' => 'Combo Manual Fee', 'amount' => 1000, 'is_active' => true]);
    $manualFee->classes()->sync([$classA->id]);

    $studentA = revenueMakeStudent('AIS-RV-CB-1', 'Combo', 'Alpha', $classA->id);
    $studentB = revenueMakeStudent('AIS-RV-CB-2', 'Combo', 'Beta', $classB->id);

    revenueMakePayment($studentA->id, $onlineFee->id, $session->id, 'first', 1000, 'paystack', 'RCP-COMBO-A-ONLINE');
    revenueMakePayment($studentA->id, $manualFee->id, $session->id, 'first', 1000, null, 'RCP-COMBO-A-MANUAL');
    revenueMakePayment($studentB->id, $onlineFee->id, $session->id, 'first', 1000, 'paystack', 'RCP-COMBO-B-ONLINE');

    $this->get(route('finance.revenue.index', [
        'class_id' => $classA->id,
        'payment_method' => 'online',
        'academic_year_id' => $session->id,
        'term' => 'first',
    ]))
        ->assertOk()
        ->assertSee('RCP-COMBO-A-ONLINE')
        ->assertDontSee('RCP-COMBO-A-MANUAL')
        ->assertDontSee('RCP-COMBO-B-ONLINE');
});

it('shows an empty state when filters match no payments', function () {
    revenueSetupSession();
    revenueSetupClass();

    $this->get(route('finance.revenue.index', ['student_name' => 'NobodyHere']))
        ->assertOk()
        ->assertSee('No payments found', false);
});

it('displays stats for the active term including count, total, online, and manual', function () {
    $session = revenueSetupSession('first', true);
    $class = revenueSetupClass();
    $feeA = FeeItem::create(['name' => 'Stats Fee Online A', 'amount' => 10000, 'is_active' => true]);
    $feeA->classes()->sync([$class->id]);
    $feeB = FeeItem::create(['name' => 'Stats Fee Manual', 'amount' => 20000, 'is_active' => true]);
    $feeB->classes()->sync([$class->id]);
    $feeC = FeeItem::create(['name' => 'Stats Fee Online C', 'amount' => 30000, 'is_active' => true]);
    $feeC->classes()->sync([$class->id]);

    $student = revenueMakeStudent('AIS-RV-STATS-1', 'Stats', 'Test', $class->id);
    revenueMakePayment($student->id, $feeA->id, $session->id, 'first', 10000, 'paystack', 'RCP-STATS-1');
    revenueMakePayment($student->id, $feeB->id, $session->id, 'first', 20000, null, 'RCP-STATS-2');
    revenueMakePayment($student->id, $feeC->id, $session->id, 'first', 30000, 'paystack', 'RCP-STATS-3');

    $response = $this->get(route('finance.revenue.index'))
        ->assertOk();

    $response->assertSee('Payments (Active Term)');
    $response->assertSee('Revenue (Active Term)');
    $response->assertSee('Online (Paystack)');
    $response->assertSee('Manual (Recorded)');

    expect((int) $response->viewData('activeTermStats')['count'])->toBe(3);
    expect((float) $response->viewData('activeTermStats')['total'])->toBe(60000.0);
    expect((int) $response->viewData('activeTermStats')['online_count'])->toBe(2);
    expect((float) $response->viewData('activeTermStats')['online_total'])->toBe(40000.0);
    expect((int) $response->viewData('activeTermStats')['manual_count'])->toBe(1);
    expect((float) $response->viewData('activeTermStats')['manual_total'])->toBe(20000.0);
});

it('paginates the results', function () {
    $session = revenueSetupSession();
    $class = revenueSetupClass();
    $fee = FeeItem::create(['name' => 'Pagination Fee', 'amount' => 100, 'is_active' => true]);
    $fee->classes()->sync([$class->id]);

    for ($i = 1; $i <= 30; $i++) {
        $student = revenueMakeStudent('AIS-RV-PG-'.$i, 'Page', 'Student'.$i, $class->id);
        revenueMakePayment($student->id, $fee->id, $session->id, 'first', 100, null, 'RCP-PG-'.$i);
    }

    $response = $this->get(route('finance.revenue.index', ['academic_year_id' => $session->id, 'term' => 'first']))
        ->assertOk();

    expect($response->viewData('payments')->total())->toBe(30);
    expect($response->viewData('payments')->perPage())->toBe(25);
    expect($response->viewData('payments')->lastPage())->toBe(2);
});

it('forbids non-finance users from accessing revenue tracking', function () {
    $staff = User::factory()->create(['role' => 'staff', 'is_active' => true]);
    $this->actingAs($staff);

    $this->get(route('finance.revenue.index'))
        ->assertForbidden();
});

it('preserves filters across pagination and the reset link clears them', function () {
    $session = revenueSetupSession();
    $class = revenueSetupClass();
    $fee = FeeItem::create(['name' => 'Reset Fee', 'amount' => 100, 'is_active' => true]);
    $fee->classes()->sync([$class->id]);

    for ($i = 1; $i <= 30; $i++) {
        $student = revenueMakeStudent('AIS-RV-RS-'.$i, 'Reset', 'Number'.$i, $class->id);
        revenueMakePayment($student->id, $fee->id, $session->id, 'first', 100, null, 'RCP-RS-'.$i);
    }

    $this->get(route('finance.revenue.index', ['student_name' => 'Reset']))
        ->assertOk()
        ->assertSee('matching payment', false);

    $resetUrl = route('finance.revenue.index');
    $this->get($resetUrl)
        ->assertOk()
        ->assertSee('Reset', false);
});
