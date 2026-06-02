<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\FeeItem;
use App\Models\Payment;
use App\Models\Student;
use App\Services\Paystack\InitializeTransactionData;
use App\Services\Paystack\PaystackException;
use App\Services\Paystack\PaystackService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;

class OnlinePaymentController extends Controller
{
    public function search(Request $request): View
    {
        $student = null;
        $error = null;
        $admissionNumber = trim((string) $request->query('admission_number', ''));
        $email = trim((string) $request->query('email', ''));

        if ($admissionNumber !== '' || $email !== '') {
            $validator = validator(
                ['admission_number' => $admissionNumber, 'email' => $email],
                [
                    'admission_number' => ['required', 'string', 'min:2', 'max:50'],
                    'email' => ['required', 'email:rfc'],
                ],
                [
                    'admission_number.required' => 'Please enter the student admission number.',
                    'email.required' => 'Please enter your email address.',
                    'email.email' => 'Please enter a valid email address.',
                ],
            );

            if ($validator->fails()) {
                $error = $validator->errors()->first();
            } else {
                $student = Student::with(['class.category'])
                    ->where('admission_number', $admissionNumber)
                    ->first();

                if (! $student) {
                    $error = 'No student found with that admission number. Please check and try again.';
                } elseif (! $student->isActive()) {
                    $error = 'Online payment is not available for this student. Please contact the school office.';
                    $student = null;
                }
            }
        }

        return view('online-payments.search', compact('student', 'error', 'admissionNumber', 'email'));
    }

    public function fees(Request $request, Student $student): View|RedirectResponse
    {
        if (! $student->isActive()) {
            abort(404);
        }

        $email = trim((string) $request->query('email', ''));
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->route('pay-online.search')
                ->with('error', 'A valid email address is required to proceed.');
        }

        $session = AcademicSession::getActive();

        if (! $session) {
            return view('online-payments.unavailable', [
                'reason' => 'Online payment is currently unavailable because no active academic session has been set. Please contact the school.',
                'student' => $student,
            ]);
        }

        $studentClass = $student->class;
        $studentCategoryId = $studentClass?->class_category_id;

        if (! $studentClass) {
            return view('online-payments.unavailable', [
                'reason' => 'No class is currently assigned to this student. Please contact the school office.',
                'student' => $student,
            ]);
        }

        $assignedFees = FeeItem::query()
            ->where('is_active', true)
            ->where(function ($q) use ($studentClass, $studentCategoryId) {
                $q->whereHas('classes', fn ($q2) => $q2->where('classes.id', $studentClass->id));
                if ($studentCategoryId) {
                    $q->orWhereHas('classCategories', fn ($q2) => $q2->where('class_categories.id', $studentCategoryId));
                }
            })
            ->orderBy('name')
            ->get();

        $existingPayments = Payment::where('student_id', $student->id)
            ->where('academic_year_id', $session->id)
            ->where('term', $session->term)
            ->get()
            ->keyBy('fee_item_id');

        $outstandingFees = $assignedFees->reject(fn (FeeItem $fee) => $existingPayments->has($fee->id))->values();

        $total = $outstandingFees->sum(fn (FeeItem $fee) => (float) $fee->amount);

        return view('online-payments.fees', compact(
            'student', 'session', 'outstandingFees', 'existingPayments', 'email', 'total'
        ));
    }

    public function initialize(Request $request, Student $student, FeeItem $fee): RedirectResponse|View
    {
        if (! $student->isActive()) {
            abort(404);
        }

        $validated = $request->validate([
            'email' => ['required', 'email:rfc'],
        ]);

        $session = AcademicSession::getActive();
        if (! $session) {
            return redirect()->route('pay-online.search')
                ->with('error', 'No active academic session. Please contact the school.');
        }

        $studentClass = $student->class;
        $studentCategoryId = $studentClass?->class_category_id;

        $isAssigned = $fee->is_active && (
            ($studentClass && $fee->classes()->where('classes.id', $studentClass->id)->exists()) ||
            ($studentCategoryId && $fee->classCategories()->where('class_categories.id', $studentCategoryId)->exists())
        );

        if (! $isAssigned) {
            return redirect()->route('pay-online.fees', ['student' => $student, 'email' => $validated['email']])
                ->with('error', 'This fee is not assigned to the student.');
        }

        $existingPayment = Payment::where('student_id', $student->id)
            ->where('fee_item_id', $fee->id)
            ->where('academic_year_id', $session->id)
            ->where('term', $session->term)
            ->first();

        if ($existingPayment) {
            return redirect()->route('pay-online.fees', ['student' => $student, 'email' => $validated['email']])
                ->with('error', 'This fee has already been paid for the current term.');
        }

        $amountKobo = (int) round(((float) $fee->amount) * 100);
        $reference = 'AIS-'.now()->format('YmdHis').'-'.strtoupper(Str::random(8));
        $callbackUrl = route('pay-online.callback');

        try {
            $paystack = PaystackService::fromConfig();
            $result = $paystack->initialize(new InitializeTransactionData(
                email: $validated['email'],
                amount: $amountKobo,
                reference: $reference,
                callbackUrl: $callbackUrl,
                metadata: [
                    'student_id' => $student->id,
                    'fee_item_id' => $fee->id,
                    'fee_name' => $fee->name,
                    'academic_year_id' => $session->id,
                    'session_label' => $session->session,
                    'term' => $session->term,
                ],
            ));
        } catch (PaystackException $e) {
            Log::error('Paystack initialize failed', [
                'student_id' => $student->id,
                'fee_item_id' => $fee->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('pay-online.fees', ['student' => $student, 'email' => $validated['email']])
                ->with('error', 'Could not start payment: '.$e->getMessage());
        } catch (\InvalidArgumentException $e) {
            return redirect()->route('pay-online.fees', ['student' => $student, 'email' => $validated['email']])
                ->with('error', 'Online payments are not configured. Please contact the school.');
        }

        if (empty($result['authorization_url'])) {
            Log::error('Paystack initialize: missing authorization_url', ['result' => $result]);

            return redirect()->route('pay-online.fees', ['student' => $student, 'email' => $validated['email']])
                ->with('error', 'Could not start payment. Please try again.');
        }

        return redirect()->away($result['authorization_url']);
    }

    public function callback(Request $request): RedirectResponse|View
    {
        $reference = trim((string) $request->query('reference', ''));

        if ($reference === '') {
            return view('online-payments.error', [
                'title' => 'Invalid Payment Callback',
                'message' => 'No payment reference was provided. Please try your payment again from the start.',
            ]);
        }

        try {
            $paystack = PaystackService::fromConfig();
            $data = $paystack->verify($reference);
        } catch (PaystackException $e) {
            Log::error('Paystack verify failed', [
                'reference' => $reference,
                'error' => $e->getMessage(),
            ]);

            return view('online-payments.error', [
                'title' => 'Payment Verification Failed',
                'message' => 'We could not verify your payment with Paystack. Please contact the school office if you were charged. Reference: '.$reference,
            ]);
        } catch (\InvalidArgumentException $e) {
            return view('online-payments.error', [
                'title' => 'Online Payments Unavailable',
                'message' => 'Online payments are not configured. Please contact the school.',
            ]);
        }

        if (($data['status'] ?? null) !== 'success') {
            return view('online-payments.error', [
                'title' => 'Payment Not Successful',
                'message' => 'Your payment was not successful. '.($data['gateway_response'] ?? 'Please try again or use a different payment method.'),
            ]);
        }

        $metadata = $data['metadata'] ?? [];
        $studentId = $metadata['student_id'] ?? null;
        $feeItemId = $metadata['fee_item_id'] ?? null;
        $academicYearId = $metadata['academic_year_id'] ?? null;
        $term = $metadata['term'] ?? null;

        if (! $studentId || ! $feeItemId || ! $academicYearId || ! $term) {
            Log::error('Paystack callback: missing metadata', [
                'reference' => $reference,
                'metadata' => $metadata,
            ]);

            return view('online-payments.error', [
                'title' => 'Payment Metadata Missing',
                'message' => 'The payment was successful, but we could not match it to a fee. Please contact the school with your reference: '.$reference,
            ]);
        }

        $fee = FeeItem::find($feeItemId);
        $expectedKobo = $fee ? (int) round(((float) $fee->amount) * 100) : 0;
        $actualKobo = (int) ($data['amount'] ?? 0);

        if ($fee && $expectedKobo !== $actualKobo) {
            Log::error('Paystack callback: amount mismatch', [
                'reference' => $reference,
                'expected_kobo' => $expectedKobo,
                'actual_kobo' => $actualKobo,
            ]);

            return view('online-payments.error', [
                'title' => 'Payment Amount Mismatch',
                'message' => 'The amount charged does not match the fee. Please contact the school with your reference: '.$reference,
            ]);
        }

        $payment = DB::transaction(function () use ($studentId, $feeItemId, $academicYearId, $term, $data, $reference, $fee) {
            $existing = Payment::where('gateway_reference', $reference)->first();
            if ($existing) {
                return $existing;
            }

            $paidAt = isset($data['paid_at']) ? Carbon::parse($data['paid_at']) : now();

            return Payment::create([
                'student_id' => $studentId,
                'fee_item_id' => $feeItemId,
                'academic_year_id' => $academicYearId,
                'term' => $term,
                'amount_paid' => $fee ? (float) $fee->amount : ($actualKobo / 100),
                'status' => 'paid',
                'reference' => $this->generateReceiptNumber(),
                'notes' => 'Paid online via Paystack',
                'paid_at' => $paidAt,
                'recorded_by' => null,
                'gateway' => 'paystack',
                'gateway_reference' => $reference,
                'gateway_channel' => $data['channel'] ?? null,
                'gateway_response' => $data,
            ]);
        });

        return redirect()->route('pay-online.receipt', ['payment' => $payment->id]);
    }

    public function receipt(Payment $payment): View
    {
        $payment->load(['student.class.category', 'feeItem', 'academicYear']);

        if ($payment->gateway !== 'paystack') {
            abort(404);
        }

        return view('online-payments.receipt', [
            'payment' => $payment,
            'school' => config('school'),
        ]);
    }

    public function receiptPdf(Payment $payment)
    {
        $payment->load(['student.class.category', 'feeItem', 'academicYear']);

        if ($payment->gateway !== 'paystack') {
            abort(404);
        }

        $pdf = Pdf::loadView('online-payments.print', [
            'payment' => $payment,
            'school' => config('school'),
        ])->setPaper('a4', 'portrait');

        return $pdf->download('receipt-'.$payment->reference.'.pdf');
    }

    private function generateReceiptNumber(): string
    {
        $year = now()->year;
        $count = (int) Payment::whereYear('created_at', $year)->count() + 1;

        return 'RCP-'.$year.'-'.str_pad((string) $count, 6, '0', STR_PAD_LEFT);
    }
}
