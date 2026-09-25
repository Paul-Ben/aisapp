@extends('layouts.dashboard')

@section('title', 'School Overview')

@section('content')
<div class="container-fluid">
    <h3 class="mb-3"><i class="fas fa-school me-2"></i>School Overview</h3>
    @include('proprietor._nav')

    @php $active = $statusCounts->get('active', 0); @endphp

    <div class="row g-4 mb-4">
        @foreach ([
            ['Active students', $active],
            ['Active staff', $staffCount],
            ['Active classes', $classCount],
            ['Active subjects', $subjectCount],
            ['Graduates', $statusCounts->get('graduated', 0)],
        ] as [$label, $value])
            <div class="col-6 col-lg">
                <div class="card h-100"><div class="card-body">
                    <div class="text-muted small">{{ $label }}</div>
                    <div class="h3 mb-0">{{ number_format($value) }}</div>
                </div></div>
            </div>
        @endforeach
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card h-100"><div class="card-body">
                <h5 class="card-title">This term
                    @if ($session)<small class="text-muted fs-6">{{ $session->session }} · {{ ucfirst($session->term) }}</small>@endif
                </h5>
                @if (! $session)
                    <p class="text-muted mb-0">No academic session is active.</p>
                @else
                    <dl class="row mb-0">
                        <dt class="col-7 fw-normal text-muted">Fees collected</dt>
                        <dd class="col-5 text-end">&#8358;{{ number_format($finance['collected'], 2) }}</dd>
                        <dt class="col-7 fw-normal text-muted">Fees outstanding</dt>
                        <dd class="col-5 text-end">&#8358;{{ number_format($finance['outstanding'], 2) }}</dd>
                        <dt class="col-7 fw-normal text-muted">Collection rate</dt>
                        <dd class="col-5 text-end">{{ $finance['rate'] !== null ? $finance['rate'].'%' : '—' }}</dd>
                        <dt class="col-7 fw-normal text-muted">Average score</dt>
                        <dd class="col-5 text-end">{{ $averageScore ?: '—' }}</dd>
                    </dl>
                @endif
            </div></div>
        </div>

        <div class="col-lg-6">
            <div class="card h-100"><div class="card-body">
                <h5 class="card-title">Students by status</h5>
                @forelse ($statusCounts as $status => $total)
                    @include('proprietor._bar', [
                        'label' => ucfirst($status),
                        'value' => number_format($total),
                        'percent' => $statusCounts->sum() ? $total / $statusCounts->sum() * 100 : 0,
                        'color' => $status === 'active' ? 'success' : ($status === 'graduated' ? 'primary' : 'secondary'),
                    ])
                @empty
                    <p class="text-muted mb-0">No students yet.</p>
                @endforelse
            </div></div>
        </div>

        <div class="col-lg-6">
            <div class="card h-100"><div class="card-body">
                <h5 class="card-title">Active students by section</h5>
                @forelse ($categories as $category)
                    @include('proprietor._bar', [
                        'label' => $category->name.' ('.$category->classes_count.' classes)',
                        'value' => number_format($category->student_total),
                        'percent' => $active ? $category->student_total / $active * 100 : 0,
                        'color' => 'info',
                    ])
                @empty
                    <p class="text-muted mb-0">No class categories yet.</p>
                @endforelse
                @if ($unassignedStudents)
                    <p class="small text-warning mb-0"><i class="fas fa-exclamation-triangle me-1"></i>{{ $unassignedStudents }} active student(s) have no class.</p>
                @endif
            </div></div>
        </div>

        <div class="col-lg-6">
            <div class="card h-100"><div class="card-body">
                <h5 class="card-title">Gender split (active)</h5>
                @foreach (['male' => 'primary', 'female' => 'danger'] as $gender => $color)
                    @include('proprietor._bar', [
                        'label' => ucfirst($gender),
                        'value' => number_format($genderCounts->get($gender, 0)),
                        'percent' => $active ? $genderCounts->get($gender, 0) / $active * 100 : 0,
                        'color' => $color,
                    ])
                @endforeach
            </div></div>
        </div>
    </div>
</div>
@endsection
