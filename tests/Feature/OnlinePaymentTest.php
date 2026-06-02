<?php

use App\Models\AcademicSession;
use App\Models\ClassCategory;
use App\Models\FeeItem;
use App\Models\Payment;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Support\Facades\Http;

function payOnlineSetupSession(): AcademicSession
{
    return AcademicSession::firstOrCreate(
        ['session' => '2026/2027', 'term' => 'first'],
        ['is_active' => true]
    );
}

function payOnlineSetupClass(): array
{
    $category = ClassCategory::firstOrCreate(['name' => 'PayOnline Test Cat']);
    $class = SchoolClass::firstOrCreate(
        ['name' => 'PayOnline Test Class'],
        ['class_category_id' => $category->id, 'is_active' => true]
    );

    return [$class, $category];
}

beforeEach(function () {
    config()->set('services.paystack.secret_key', 'sk_test_dummy_secret_key_for_pest');
    config()->set('services.paystack.base_url', 'https://api.paystack.co');
    config()->set('services.paystack.callback_url', 'http://localhost/pay-online/callback');
});

it('shows the search form on the public landing page', function () {
    $this->get(route('pay-online.search'))
        ->assertOk()
        ->assertSee('Pay School Fees Online')
        ->assertSee('Student Admission Number')
        ->assertSee('Your Email Address');
});

it('finds an active student and shows name + class on the search page', function () {
    [$class] = payOnlineSetupClass();
    $student = Student::create([
        'admission_number' => 'AIS-PO-001',
        'first_name' => 'Ada',
        'last_name' => 'Lovelace',
        'date_of_birth' => '2015-01-01',
        'gender' => 'female',
        'class_id' => $class->id,
        'status' => 'active',
        'admission_date' => '2024-09-01',
    ]);

    $this->get(route('pay-online.search', [
        'admission_number' => 'AIS-PO-001',
        'email' => 'parent@example.com',
    ]))
        ->assertOk()
        ->assertSee('Ada')
        ->assertSee('Lovelace')
        ->assertSee('PayOnline Test Class')
        ->assertSee('Make Payment');
});

it('hides a graduated student from the search result', function () {
    [$class] = payOnlineSetupClass();
    Student::create([
        'admission_number' => 'AIS-PO-002',
        'first_name' => 'Old',
        'last_name' => 'Grad',
        'date_of_birth' => '2010-01-01',
        'gender' => 'male',
        'class_id' => $class->id,
        'status' => 'graduated',
        'admission_date' => '2018-09-01',
    ]);

    $this->get(route('pay-online.search', [
        'admission_number' => 'AIS-PO-002',
        'email' => 'parent@example.com',
    ]))
        ->assertOk()
        ->assertSee('not available for this student', false);
});

it('rejects the search when admission number is unknown', function () {
    $this->get(route('pay-online.search', [
        'admission_number' => 'DOES-NOT-EXIST',
        'email' => 'parent@example.com',
    ]))
        ->assertOk()
        ->assertSee('No student found', false);
});

it('shows only fully-outstanding fees and excludes paid or partially-paid fees', function () {
    payOnlineSetupSession();
    [$class] = payOnlineSetupClass();

    $tuition = FeeItem::create(['name' => 'Tuition', 'amount' => 50000, 'is_active' => true]);
    $tuition->classes()->sync([$class->id]);

    $pta = FeeItem::create(['name' => 'PTA Levy', 'amount' => 10000, 'is_active' => true]);
    $pta->classes()->sync([$class->id]);

    $books = FeeItem::create(['name' => 'Books', 'amount' => 15000, 'is_active' => true]);
    $books->classes()->sync([$class->id]);

    $student = Student::create([
        'admission_number' => 'AIS-PO-003',
        'first_name' => 'Mixed',
        'last_name' => 'Payments',
        'date_of_birth' => '2015-01-01',
        'gender' => 'male',
        'class_id' => $class->id,
        'status' => 'active',
        'admission_date' => '2024-09-01',
    ]);

    $session = AcademicSession::getActive();

    Payment::create([
        'student_id' => $student->id,
        'fee_item_id' => $pta->id,
        'academic_year_id' => $session->id,
        'term' => 'first',
        'amount_paid' => 10000,
        'status' => 'paid',
        'paid_at' => '2026-04-01',
        'reference' => 'RCP-MANUAL-1',
    ]);

    Payment::create([
        'student_id' => $student->id,
        'fee_item_id' => $books->id,
        'academic_year_id' => $session->id,
        'term' => 'first',
        'amount_paid' => 5000,
        'status' => 'part',
        'paid_at' => '2026-04-01',
        'reference' => 'RCP-MANUAL-2',
    ]);

    $this->get(route('pay-online.fees', [
        'student' => $student,
        'email' => 'parent@example.com',
    ]))
        ->assertOk()
        ->assertSee('Tuition')
        ->assertDontSee('PTA Levy')
        ->assertDontSee('Books');
});

it('redirects back to search when the fees view is hit without an email', function () {
    payOnlineSetupSession();
    [$class] = payOnlineSetupClass();
    $student = Student::create([
        'admission_number' => 'AIS-PO-004',
        'first_name' => 'No',
        'last_name' => 'Email',
        'date_of_birth' => '2015-01-01',
        'gender' => 'male',
        'class_id' => $class->id,
        'status' => 'active',
        'admission_date' => '2024-09-01',
    ]);

    $this->get(route('pay-online.fees', ['student' => $student]))
        ->assertRedirect(route('pay-online.search'));
});

it('initializes a Paystack transaction and redirects to the authorization URL', function () {
    payOnlineSetupSession();
    [$class] = payOnlineSetupClass();
    $fee = FeeItem::create(['name' => 'Online Tuition', 'amount' => 75000, 'is_active' => true]);
    $fee->classes()->sync([$class->id]);

    $student = Student::create([
        'admission_number' => 'AIS-PO-005',
        'first_name' => 'Init',
        'last_name' => 'Student',
        'date_of_birth' => '2015-01-01',
        'gender' => 'male',
        'class_id' => $class->id,
        'status' => 'active',
        'admission_date' => '2024-09-01',
    ]);

    Http::fake([
        'api.paystack.co/transaction/initialize' => Http::response([
            'status' => true,
            'message' => 'Authorization URL created',
            'data' => [
                'authorization_url' => 'https://checkout.paystack.com/abc123',
                'access_code' => 'access_xyz',
                'reference' => 'AIS-TEST-REF',
            ],
        ]),
    ]);

    $response = $this->post(route('pay-online.initialize', [
        'student' => $student,
        'fee' => $fee,
    ]), [
        'email' => 'parent@example.com',
    ]);

    $response->assertRedirect('https://checkout.paystack.com/abc123');

    Http::assertSent(function ($request) use ($student, $fee) {
        $body = json_decode($request->body(), true);

        return $request->url() === 'https://api.paystack.co/transaction/initialize'
            && $body['email'] === 'parent@example.com'
            && $body['amount'] === 7500000
            && $body['callback_url'] === route('pay-online.callback')
            && $body['metadata']['student_id'] === $student->id
            && $body['metadata']['fee_item_id'] === $fee->id;
    });
});

it('blocks initialize when the fee is already paid for the term', function () {
    payOnlineSetupSession();
    [$class] = payOnlineSetupClass();
    $fee = FeeItem::create(['name' => 'Already Paid', 'amount' => 25000, 'is_active' => true]);
    $fee->classes()->sync([$class->id]);

    $student = Student::create([
        'admission_number' => 'AIS-PO-006',
        'first_name' => 'Already',
        'last_name' => 'Paid',
        'date_of_birth' => '2015-01-01',
        'gender' => 'male',
        'class_id' => $class->id,
        'status' => 'active',
        'admission_date' => '2024-09-01',
    ]);

    $session = AcademicSession::getActive();

    Payment::create([
        'student_id' => $student->id,
        'fee_item_id' => $fee->id,
        'academic_year_id' => $session->id,
        'term' => 'first',
        'amount_paid' => 25000,
        'status' => 'paid',
        'paid_at' => '2026-04-01',
        'reference' => 'RCP-MANUAL-EXISTS',
    ]);

    $this->post(route('pay-online.initialize', [
        'student' => $student,
        'fee' => $fee,
    ]), [
        'email' => 'parent@example.com',
    ])->assertRedirect(route('pay-online.fees', [
        'student' => $student,
        'email' => 'parent@example.com',
    ]))->assertSessionHas('error');
});

it('verifies the callback, creates a payment, and redirects to the receipt', function () {
    payOnlineSetupSession();
    [$class] = payOnlineSetupClass();
    $fee = FeeItem::create(['name' => 'Verify Fee', 'amount' => 30000, 'is_active' => true]);
    $fee->classes()->sync([$class->id]);

    $student = Student::create([
        'admission_number' => 'AIS-PO-007',
        'first_name' => 'Verify',
        'last_name' => 'Student',
        'date_of_birth' => '2015-01-01',
        'gender' => 'male',
        'class_id' => $class->id,
        'status' => 'active',
        'admission_date' => '2024-09-01',
    ]);

    $session = AcademicSession::getActive();
    $reference = 'AIS-20260101-PAYMENT1';

    Http::fake([
        'api.paystack.co/transaction/verify/*' => Http::response([
            'status' => true,
            'message' => 'Verification successful',
            'data' => [
                'id' => 12345,
                'reference' => $reference,
                'amount' => 3000000,
                'currency' => 'NGN',
                'channel' => 'card',
                'paid_at' => '2026-05-02T10:30:00.000Z',
                'status' => 'success',
                'metadata' => [
                    'student_id' => $student->id,
                    'fee_item_id' => $fee->id,
                    'academic_year_id' => $session->id,
                    'term' => 'first',
                ],
            ],
        ]),
    ]);

    $this->get(route('pay-online.callback', ['reference' => $reference]))
        ->assertRedirect();

    $payment = Payment::where('gateway_reference', $reference)->first();

    expect($payment)->not->toBeNull();
    expect($payment->student_id)->toBe($student->id);
    expect($payment->fee_item_id)->toBe($fee->id);
    expect($payment->academic_year_id)->toBe($session->id);
    expect($payment->term)->toBe('first');
    expect($payment->status)->toBe('paid');
    expect($payment->gateway)->toBe('paystack');
    expect($payment->gateway_channel)->toBe('card');
    expect($payment->gateway_response)->toBeArray();
    expect((float) $payment->amount_paid)->toBe(30000.0);
    expect($payment->reference)->toStartWith('RCP-');
});

it('is idempotent: a second callback for the same reference does not create a duplicate payment', function () {
    payOnlineSetupSession();
    [$class] = payOnlineSetupClass();
    $fee = FeeItem::create(['name' => 'Idempotent Fee', 'amount' => 12000, 'is_active' => true]);
    $fee->classes()->sync([$class->id]);

    $student = Student::create([
        'admission_number' => 'AIS-PO-008',
        'first_name' => 'Idem',
        'last_name' => 'Potent',
        'date_of_birth' => '2015-01-01',
        'gender' => 'male',
        'class_id' => $class->id,
        'status' => 'active',
        'admission_date' => '2024-09-01',
    ]);

    $session = AcademicSession::getActive();
    $reference = 'AIS-DUPLICATE-REF';

    Payment::create([
        'student_id' => $student->id,
        'fee_item_id' => $fee->id,
        'academic_year_id' => $session->id,
        'term' => 'first',
        'amount_paid' => 12000,
        'status' => 'paid',
        'reference' => 'RCP-EXISTING-001',
        'paid_at' => '2026-05-02',
        'gateway' => 'paystack',
        'gateway_reference' => $reference,
        'gateway_channel' => 'card',
    ]);

    Http::fake([
        'api.paystack.co/transaction/verify/*' => Http::response([
            'status' => true,
            'message' => 'Verification successful',
            'data' => [
                'reference' => $reference,
                'amount' => 1200000,
                'channel' => 'card',
                'paid_at' => '2026-05-02T10:30:00.000Z',
                'status' => 'success',
                'metadata' => [
                    'student_id' => $student->id,
                    'fee_item_id' => $fee->id,
                    'academic_year_id' => $session->id,
                    'term' => 'first',
                ],
            ],
        ]),
    ]);

    $this->get(route('pay-online.callback', ['reference' => $reference]))
        ->assertRedirect(route('pay-online.receipt', ['payment' => 1]));

    expect(Payment::where('gateway_reference', $reference)->count())->toBe(1);
});

it('shows an error page when the Paystack verify response status is not success', function () {
    payOnlineSetupSession();

    Http::fake([
        'api.paystack.co/transaction/verify/*' => Http::response([
            'status' => true,
            'message' => 'Verification successful',
            'data' => [
                'reference' => 'AIS-FAILED-REF',
                'amount' => 5000,
                'status' => 'failed',
                'gateway_response' => 'Insufficient funds',
                'metadata' => [],
            ],
        ]),
    ]);

    $this->get(route('pay-online.callback', ['reference' => 'AIS-FAILED-REF']))
        ->assertOk()
        ->assertSee('Payment Not Successful', false)
        ->assertSee('Insufficient funds', false);
});

it('shows an error when callback amount does not match the fee amount', function () {
    payOnlineSetupSession();
    [$class] = payOnlineSetupClass();
    $fee = FeeItem::create(['name' => 'Mismatch Fee', 'amount' => 20000, 'is_active' => true]);
    $fee->classes()->sync([$class->id]);

    $student = Student::create([
        'admission_number' => 'AIS-PO-009',
        'first_name' => 'Mismatch',
        'last_name' => 'Student',
        'date_of_birth' => '2015-01-01',
        'gender' => 'male',
        'class_id' => $class->id,
        'status' => 'active',
        'admission_date' => '2024-09-01',
    ]);

    $session = AcademicSession::getActive();

    Http::fake([
        'api.paystack.co/transaction/verify/*' => Http::response([
            'status' => true,
            'message' => 'Verification successful',
            'data' => [
                'reference' => 'AIS-MISMATCH-REF',
                'amount' => 100, // intentionally wrong
                'status' => 'success',
                'metadata' => [
                    'student_id' => $student->id,
                    'fee_item_id' => $fee->id,
                    'academic_year_id' => $session->id,
                    'term' => 'first',
                ],
            ],
        ]),
    ]);

    $this->get(route('pay-online.callback', ['reference' => 'AIS-MISMATCH-REF']))
        ->assertOk()
        ->assertSee('Amount Mismatch', false);

    expect(Payment::count())->toBe(0);
});

it('renders the receipt page for a Paystack-paid payment', function () {
    payOnlineSetupSession();
    [$class] = payOnlineSetupClass();
    $fee = FeeItem::create(['name' => 'Receipt Fee', 'amount' => 40000, 'is_active' => true]);
    $fee->classes()->sync([$class->id]);

    $student = Student::create([
        'admission_number' => 'AIS-PO-010',
        'first_name' => 'Receipt',
        'last_name' => 'View',
        'date_of_birth' => '2015-01-01',
        'gender' => 'male',
        'class_id' => $class->id,
        'status' => 'active',
        'admission_date' => '2024-09-01',
    ]);

    $session = AcademicSession::getActive();

    $payment = Payment::create([
        'student_id' => $student->id,
        'fee_item_id' => $fee->id,
        'academic_year_id' => $session->id,
        'term' => 'first',
        'amount_paid' => 40000,
        'status' => 'paid',
        'reference' => 'RCP-2026-000001',
        'paid_at' => '2026-05-02',
        'gateway' => 'paystack',
        'gateway_reference' => 'AIS-RECEIPT-REF',
        'gateway_channel' => 'card',
    ]);

    $this->get(route('pay-online.receipt', ['payment' => $payment->id]))
        ->assertOk()
        ->assertSee('RCP-2026-000001')
        ->assertSee('Receipt')
        ->assertSee('View')
        ->assertSee('Receipt Fee')
        ->assertSee('AIS-RECEIPT-REF')
        ->assertSee('Verified by Paystack');
});

it('returns 404 on the receipt page for a non-Paystack payment', function () {
    $session = payOnlineSetupSession();
    [$class] = payOnlineSetupClass();
    $student = Student::create([
        'admission_number' => 'AIS-PO-011',
        'first_name' => 'Manual',
        'last_name' => 'Receipt',
        'date_of_birth' => '2015-01-01',
        'gender' => 'male',
        'class_id' => $class->id,
        'status' => 'active',
        'admission_date' => '2024-09-01',
    ]);

    $fee = FeeItem::create(['name' => 'Manual Fee', 'amount' => 5000, 'is_active' => true]);
    $fee->classes()->sync([$class->id]);

    $payment = Payment::create([
        'student_id' => $student->id,
        'fee_item_id' => $fee->id,
        'academic_year_id' => $session->id,
        'term' => 'first',
        'amount_paid' => 5000,
        'status' => 'paid',
        'reference' => 'RCP-MANUAL-NO-GATEWAY',
        'paid_at' => '2026-05-01',
        'gateway' => null,
    ]);

    $this->get(route('pay-online.receipt', ['payment' => $payment->id]))
        ->assertNotFound();
});

it('downloads a PDF for the receipt', function () {
    payOnlineSetupSession();
    [$class] = payOnlineSetupClass();
    $fee = FeeItem::create(['name' => 'PDF Fee', 'amount' => 18000, 'is_active' => true]);
    $fee->classes()->sync([$class->id]);

    $student = Student::create([
        'admission_number' => 'AIS-PO-012',
        'first_name' => 'PDF',
        'last_name' => 'Download',
        'date_of_birth' => '2015-01-01',
        'gender' => 'male',
        'class_id' => $class->id,
        'status' => 'active',
        'admission_date' => '2024-09-01',
    ]);

    $session = AcademicSession::getActive();

    $payment = Payment::create([
        'student_id' => $student->id,
        'fee_item_id' => $fee->id,
        'academic_year_id' => $session->id,
        'term' => 'first',
        'amount_paid' => 18000,
        'status' => 'paid',
        'reference' => 'RCP-2026-000099',
        'paid_at' => '2026-05-02',
        'gateway' => 'paystack',
        'gateway_reference' => 'AIS-PDF-REF',
        'gateway_channel' => 'bank_transfer',
    ]);

    $response = $this->get(route('pay-online.receipt.pdf', ['payment' => $payment->id]));

    $response->assertOk();
    expect($response->headers->get('content-type'))->toContain('pdf');
    expect($response->headers->get('content-disposition'))->toContain('receipt-RCP-2026-000099.pdf');
});
