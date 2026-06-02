<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentRequest;
use App\Models\AcademicSession;
use App\Models\FeeItem;
use App\Models\Payment;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class FeeCollectionController extends Controller
{
    public function index(Request $request): View
    {
        $query = trim((string) $request->input('q'));
        $students = collect();

        if ($query !== '') {
            $students = Student::query()
                ->with(['class.category', 'previousClass'])
                ->where(function ($q) use ($query) {
                    $q->where('first_name', 'like', "%{$query}%")
                        ->orWhere('last_name', 'like', "%{$query}%")
                        ->orWhere('middle_name', 'like', "%{$query}%")
                        ->orWhere('admission_number', 'like', "%{$query}%")
                        ->orWhereHas('class', function ($q2) use ($query) {
                            $q2->where('name', 'like', "%{$query}%")
                                ->orWhereHas('category', function ($q3) use ($query) {
                                    $q3->where('name', 'like', "%{$query}%");
                                });
                        });
                })
                ->where('status', 'active')
                ->orderBy('last_name')
                ->orderBy('first_name')
                ->limit(50)
                ->get();
        }

        return view('finance.payments.index', compact('students', 'query'));
    }

    public function student(Request $request, Student $student): View
    {
        $student->load(['class.category']);

        $sessionId = $request->input('session_id');
        $activeSession = AcademicSession::getActive();

        $session = $sessionId
            ? AcademicSession::find($sessionId)
            : $activeSession;

        if (! $session) {
            $sessions = AcademicSession::orderBy('created_at', 'desc')->get();

            return view('finance.payments.no_active_session', compact('student', 'sessions'));
        }

        $studentClass = $student->class;
        $studentCategoryId = $studentClass?->class_category_id;

        $assignedFees = FeeItem::query()
            ->where('is_active', true)
            ->where(function ($q) use ($studentClass, $studentCategoryId) {
                if ($studentClass) {
                    $q->whereHas('classes', fn ($q2) => $q2->where('classes.id', $studentClass->id));
                }
                if ($studentCategoryId) {
                    $q->orWhereHas('classCategories', fn ($q2) => $q2->where('class_categories.id', $studentCategoryId));
                }
            })
            ->orderBy('name')
            ->get();

        $payments = Payment::where('student_id', $student->id)
            ->where('academic_year_id', $session->id)
            ->where('term', $session->term)
            ->get()
            ->keyBy('fee_item_id');

        $allSessions = AcademicSession::orderBy('created_at', 'desc')->get();

        return view('finance.payments.student', compact(
            'student', 'session', 'assignedFees', 'payments', 'allSessions', 'activeSession'
        ));
    }

    public function form(Request $request, Student $student, FeeItem $fee): View|RedirectResponse
    {
        $session = $this->resolveSession($request);

        if (! $session) {
            return redirect()->route('finance.payments.student', $student)
                ->with('error', 'No academic session is selected or active. Ask the administrator to set an active session in the admin dashboard.');
        }

        $student->load(['class.category']);

        $studentClass = $student->class;
        $studentCategoryId = $studentClass?->class_category_id;

        $isAssigned = $fee->is_active && (
            ($studentClass && $fee->classes()->where('classes.id', $studentClass->id)->exists()) ||
            ($studentCategoryId && $fee->classCategories()->where('class_categories.id', $studentCategoryId)->exists())
        );

        if (! $isAssigned) {
            return redirect()->route('finance.payments.student', ['student' => $student, 'session_id' => $session->id])
                ->with('error', 'This fee is not assigned to the student\'s class.');
        }

        $payment = Payment::where('student_id', $student->id)
            ->where('fee_item_id', $fee->id)
            ->where('academic_year_id', $session->id)
            ->where('term', $session->term)
            ->first();

        return view('finance.payments.form', compact('student', 'fee', 'session', 'payment'));
    }

    public function save(StorePaymentRequest $request, Student $student, FeeItem $fee): RedirectResponse
    {
        $session = $this->resolveSession($request);

        if (! $session) {
            return redirect()->route('finance.payments.student', $student)
                ->with('error', 'No academic session is selected or active. Ask the administrator to set an active session in the admin dashboard.');
        }

        DB::beginTransaction();
        try {
            Payment::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'fee_item_id' => $fee->id,
                    'academic_year_id' => $session->id,
                    'term' => $session->term,
                ],
                [
                    'amount_paid' => $request->amount_paid,
                    'status' => $request->status,
                    'reference' => $request->reference,
                    'notes' => $request->notes,
                    'paid_at' => $request->paid_at,
                    'recorded_by' => auth()->id(),
                ]
            );

            DB::commit();

            return redirect()->route('finance.payments.student', [
                'student' => $student->id,
                'session_id' => $session->id,
            ])->with('success', 'Payment recorded successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors(['error' => 'Failed to record payment: '.$e->getMessage()])
                ->withInput();
        }
    }

    private function resolveSession(Request $request): ?AcademicSession
    {
        $sessionId = $request->input('session_id');

        return $sessionId
            ? AcademicSession::find($sessionId)
            : AcademicSession::getActive();
    }
}
