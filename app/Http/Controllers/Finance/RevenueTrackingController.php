<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\Payment;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RevenueTrackingController extends Controller
{
    private const FILTER_KEYS = [
        'student_name',
        'student_number',
        'class_id',
        'payment_method',
        'academic_year_id',
        'term',
    ];

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'student_name' => ['nullable', 'string', 'max:100'],
            'student_number' => ['nullable', 'string', 'max:50'],
            'class_id' => ['nullable', 'integer', 'exists:classes,id'],
            'payment_method' => ['nullable', 'in:online,manual'],
            'academic_year_id' => ['nullable', 'integer', 'exists:academic_sessions,id'],
            'term' => ['nullable', 'in:first,second,third'],
        ]);

        $activeSession = AcademicSession::getActive();
        $hasExplicitFilters = $request->hasAny(self::FILTER_KEYS);

        if (! $hasExplicitFilters && $activeSession) {
            $filters['academic_year_id'] = $activeSession->id;
            $filters['term'] = $activeSession->term;
        }

        $query = Payment::with([
            'student.class.category',
            'feeItem',
            'academicYear',
            'recordedBy',
        ]);

        if (! empty($filters['student_name'])) {
            $term = '%'.$filters['student_name'].'%';
            $query->whereHas('student', function ($q) use ($term) {
                $q->where('first_name', 'like', $term)
                    ->orWhere('last_name', 'like', $term)
                    ->orWhere('middle_name', 'like', $term);
            });
        }

        if (! empty($filters['student_number'])) {
            $term = '%'.$filters['student_number'].'%';
            $query->whereHas('student', function ($q) use ($term) {
                $q->where('admission_number', 'like', $term);
            });
        }

        if (! empty($filters['class_id'])) {
            $query->whereHas('student', function ($q) use ($filters) {
                $q->where('class_id', $filters['class_id']);
            });
        }

        if (! empty($filters['payment_method'])) {
            if ($filters['payment_method'] === 'online') {
                $query->where('gateway', 'paystack');
            } else {
                $query->where(function ($q) {
                    $q->whereNull('gateway')->orWhere('gateway', '!=', 'paystack');
                });
            }
        }

        if (! empty($filters['academic_year_id'])) {
            $query->where('academic_year_id', $filters['academic_year_id']);
        }

        if (! empty($filters['term'])) {
            $query->where('term', $filters['term']);
        }

        $payments = $query
            ->orderByDesc('paid_at')
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        $activeTermStats = $this->activeTermStats($activeSession);

        $allClasses = SchoolClass::with('category')->orderBy('name')->get();
        $allSessions = AcademicSession::orderByDesc('created_at')->get();

        return view('finance.revenue.index', [
            'payments' => $payments,
            'filters' => $filters,
            'hasExplicitFilters' => $hasExplicitFilters,
            'activeSession' => $activeSession,
            'activeTermStats' => $activeTermStats,
            'allClasses' => $allClasses,
            'allSessions' => $allSessions,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function activeTermStats(?AcademicSession $activeSession): array
    {
        $empty = [
            'has_session' => false,
            'count' => 0,
            'total' => 0.0,
            'online_count' => 0,
            'online_total' => 0.0,
            'manual_count' => 0,
            'manual_total' => 0.0,
        ];

        if (! $activeSession) {
            return $empty;
        }

        $base = Payment::query()
            ->where('academic_year_id', $activeSession->id)
            ->where('term', $activeSession->term);

        $onlineQuery = (clone $base)->where('gateway', 'paystack');
        $manualQuery = (clone $base)->where(function ($q) {
            $q->whereNull('gateway')->orWhere('gateway', '!=', 'paystack');
        });

        return [
            'has_session' => true,
            'count' => (int) (clone $base)->count(),
            'total' => (float) (clone $base)->sum('amount_paid'),
            'online_count' => (int) $onlineQuery->count(),
            'online_total' => (float) $onlineQuery->sum('amount_paid'),
            'manual_count' => (int) $manualQuery->count(),
            'manual_total' => (float) $manualQuery->sum('amount_paid'),
        ];
    }
}
