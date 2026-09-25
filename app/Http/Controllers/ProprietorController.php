<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\ClassCategory;
use App\Models\FeeItem;
use App\Models\Payment;
use App\Models\Result;
use App\Models\SchoolClass;
use App\Models\Staff;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Read-only, school-wide reporting for the proprietor.
 *
 * An "AcademicSession" row is one session + term (e.g. 2026/2027 first term),
 * so every report is scoped to one of those rows, defaulting to the active one.
 */
class ProprietorController extends Controller
{
    /** Minimum total_score treated as a pass in academic reports. */
    private const PASS_MARK = 50;

    public function dashboard(): View
    {
        $session = AcademicSession::getActive();
        $finance = $this->financeTotals($session);
        $enrolled = Student::where('status', 'active')->count();

        return view('dashboards.proprietor', [
            'session' => $session,
            'enrolled' => $enrolled,
            'staffCount' => Staff::where('is_active', true)->count(),
            'collected' => $finance['collected'],
            'collectionRate' => $finance['rate'],
            'averageScore' => $session
                ? round((float) Result::where('academic_year_id', $session->id)->avg('total_score'), 1)
                : null,
        ]);
    }

    public function overview(): View
    {
        $session = AcademicSession::getActive();

        $statusCounts = Student::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')->pluck('total', 'status');

        $activeStudents = Student::where('status', 'active');

        $genderCounts = (clone $activeStudents)
            ->select('gender', DB::raw('count(*) as total'))
            ->groupBy('gender')->pluck('total', 'gender');

        $categories = ClassCategory::withCount('classes')->orderBy('name')->get()
            ->map(function (ClassCategory $category) {
                $category->student_total = Student::where('status', 'active')
                    ->whereIn('class_id', $category->classes()->pluck('id'))
                    ->count();

                return $category;
            });

        return view('proprietor.overview', [
            'session' => $session,
            'statusCounts' => $statusCounts,
            'genderCounts' => $genderCounts,
            'categories' => $categories,
            'classCount' => SchoolClass::where('is_active', true)->count(),
            'subjectCount' => Subject::where('is_active', true)->count(),
            'staffCount' => Staff::where('is_active', true)->count(),
            'unassignedStudents' => (clone $activeStudents)->whereNull('class_id')->count(),
            'finance' => $this->financeTotals($session),
            'averageScore' => $session
                ? round((float) Result::where('academic_year_id', $session->id)->avg('total_score'), 1)
                : null,
        ]);
    }

    public function finance(Request $request): View
    {
        $sessions = AcademicSession::orderByDesc('id')->get();
        $session = $this->resolveSession($request, $sessions);

        $paymentsQuery = fn () => Payment::query()
            ->when($session, fn ($q) => $q->where('payments.academic_year_id', $session->id));

        $totals = $this->financeTotals($session);

        $byFee = $paymentsQuery()
            ->join('fee_items', 'fee_items.id', '=', 'payments.fee_item_id')
            ->select('fee_items.name', DB::raw('count(*) as payments'), DB::raw('sum(payments.amount_paid) as total'))
            ->groupBy('fee_items.id', 'fee_items.name')
            ->orderByDesc('total')
            ->get();

        $byClass = $paymentsQuery()
            ->join('students', 'students.id', '=', 'payments.student_id')
            ->leftJoin('classes', 'classes.id', '=', 'students.class_id')
            ->select(DB::raw("coalesce(classes.name, 'No class') as name"), DB::raw('sum(payments.amount_paid) as total'))
            ->groupBy('classes.id', 'classes.name')
            ->orderByDesc('total')
            ->get();

        $method = $paymentsQuery()
            ->select(
                DB::raw("case when gateway = 'paystack' then 'online' else 'manual' end as method"),
                DB::raw('count(*) as payments'),
                DB::raw('sum(amount_paid) as total')
            )
            ->groupBy('method')->get()->keyBy('method');

        // Collected per session/term across the school's history, oldest first.
        $trend = Payment::query()
            ->select('academic_year_id', DB::raw('sum(amount_paid) as total'))
            ->groupBy('academic_year_id')
            ->pluck('total', 'academic_year_id');

        $trend = $sessions->reverse()->filter(fn ($s) => $trend->has($s->id))
            ->map(fn ($s) => ['label' => $s->session.' · '.ucfirst($s->term), 'total' => (float) $trend[$s->id]])
            ->values();

        return view('proprietor.finance', compact('sessions', 'session', 'totals', 'byFee', 'byClass', 'method', 'trend'));
    }

    public function academics(Request $request): View
    {
        $sessions = AcademicSession::orderByDesc('id')->get();
        $session = $this->resolveSession($request, $sessions);

        $results = fn () => Result::query()
            ->when($session, fn ($q) => $q->where('results.academic_year_id', $session->id));

        $summary = $results()->selectRaw(
            'count(*) as entries, count(distinct student_id) as students, avg(total_score) as average, '.
            'sum(case when total_score >= ? then 1 else 0 end) as passed',
            [self::PASS_MARK]
        )->first();

        $byClass = $results()
            ->join('classes', 'classes.id', '=', 'results.class_id')
            ->selectRaw(
                'classes.name as name, count(distinct results.student_id) as students, avg(results.total_score) as average, '.
                'sum(case when results.total_score >= ? then 1 else 0 end) as passed, count(*) as entries',
                [self::PASS_MARK]
            )
            ->groupBy('classes.id', 'classes.name')
            ->orderByDesc('average')->get();

        $bySubject = $results()
            ->join('subjects', 'subjects.id', '=', 'results.subject_id')
            ->selectRaw(
                'subjects.name as name, avg(results.total_score) as average, '.
                'sum(case when results.total_score >= ? then 1 else 0 end) as passed, count(*) as entries',
                [self::PASS_MARK]
            )
            ->groupBy('subjects.id', 'subjects.name')
            ->orderByDesc('average')->get();

        $grades = $results()->whereNotNull('grade')
            ->select('grade', DB::raw('count(*) as total'))
            ->groupBy('grade')->orderBy('grade')->pluck('total', 'grade');

        $topStudents = $results()
            ->join('students', 'students.id', '=', 'results.student_id')
            ->leftJoin('classes', 'classes.id', '=', 'students.class_id')
            ->selectRaw(
                "students.first_name, students.last_name, students.admission_number, classes.name as class_name, avg(results.total_score) as average"
            )
            ->groupBy('students.id', 'students.first_name', 'students.last_name', 'students.admission_number', 'classes.name')
            ->orderByDesc('average')->limit(10)->get();

        return view('proprietor.academics', [
            'sessions' => $sessions,
            'session' => $session,
            'summary' => $summary,
            'byClass' => $byClass,
            'bySubject' => $bySubject,
            'grades' => $grades,
            'topStudents' => $topStudents,
            'passMark' => self::PASS_MARK,
        ]);
    }

    public function enrollment(): View
    {
        $students = Student::select('id', 'gender', 'status', 'class_id', 'admission_date', 'graduation_session_id')->get();
        $active = $students->where('status', 'active');

        $classes = SchoolClass::with('category')->orderBy('name')->get();
        $perClass = $active->groupBy('class_id')->map->count();

        $byClass = $classes->map(fn (SchoolClass $class) => [
            'name' => $class->full_name,
            'category' => $class->category?->name ?? '—',
            'total' => $perClass->get($class->id, 0),
            'male' => $active->where('class_id', $class->id)->where('gender', 'male')->count(),
            'female' => $active->where('class_id', $class->id)->where('gender', 'female')->count(),
        ]);

        $byCategory = $byClass->groupBy('category')->map(fn (Collection $rows) => $rows->sum('total'));

        $admissionsPerYear = $students
            ->groupBy(fn ($s) => $s->admission_date?->format('Y') ?? 'Unknown')
            ->map->count()->sortKeys();

        $graduatesBySession = AcademicSession::query()
            ->whereIn('id', $students->where('status', 'graduated')->pluck('graduation_session_id')->filter()->unique())
            ->orderBy('id')->get()
            ->mapWithKeys(fn ($s) => [
                $s->session.' · '.ucfirst($s->term) => $students->where('graduation_session_id', $s->id)->count(),
            ]);

        $statusCounts = $students->groupBy('status')->map->count();
        $everEnrolled = $students->count();
        $retained = $everEnrolled - $statusCounts->get('withdrawn', 0);

        return view('proprietor.enrollment', [
            'total' => $active->count(),
            'gender' => $active->groupBy('gender')->map->count(),
            'statusCounts' => $statusCounts,
            'byClass' => $byClass,
            'byCategory' => $byCategory,
            'admissionsPerYear' => $admissionsPerYear,
            'graduatesBySession' => $graduatesBySession,
            'retentionRate' => $everEnrolled > 0 ? round($retained / $everEnrolled * 100, 1) : null,
        ]);
    }

    /**
     * Amount collected in a session/term vs. the amount billed to active students.
     * Billed = every active fee assigned to a student's class or class category.
     *
     * @return array{collected: float, expected: float, outstanding: float, rate: float|null, payments: int}
     */
    private function financeTotals(?AcademicSession $session): array
    {
        if (! $session) {
            return ['collected' => 0.0, 'expected' => 0.0, 'outstanding' => 0.0, 'rate' => null, 'payments' => 0];
        }

        $payments = Payment::where('academic_year_id', $session->id);
        $collected = (float) (clone $payments)->sum('amount_paid');

        $fees = FeeItem::where('is_active', true)->with(['classes:id', 'classCategories:id'])->get();
        $classCategory = SchoolClass::pluck('class_category_id', 'id');
        $studentsPerClass = Student::where('status', 'active')->whereNotNull('class_id')
            ->select('class_id', DB::raw('count(*) as total'))->groupBy('class_id')->pluck('total', 'class_id');

        $expected = 0.0;
        foreach ($studentsPerClass as $classId => $count) {
            $categoryId = $classCategory->get($classId);
            foreach ($fees as $fee) {
                if ($fee->classes->contains('id', $classId) || ($categoryId && $fee->classCategories->contains('id', $categoryId))) {
                    $expected += (float) $fee->amount * $count;
                }
            }
        }

        return [
            'collected' => $collected,
            'expected' => $expected,
            'outstanding' => max($expected - $collected, 0),
            'rate' => $expected > 0 ? round(min($collected / $expected, 1) * 100, 1) : null,
            'payments' => (clone $payments)->count(),
        ];
    }

    private function resolveSession(Request $request, Collection $sessions): ?AcademicSession
    {
        $request->validate(['session_id' => ['nullable', 'integer']]);

        return $request->filled('session_id')
            ? $sessions->firstWhere('id', (int) $request->query('session_id'))
            : AcademicSession::getActive();
    }
}
