@extends('layouts.dashboard')

@section('title', 'Academic Reports')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
        <h3 class="mb-0"><i class="fas fa-graduation-cap me-2"></i>Academic Reports</h3>
        @include('proprietor._session-filter')
    </div>
    @include('proprietor._nav')

    @if (! $session || ! $summary->entries)
        <div class="alert alert-info">No results have been entered for {{ $session ? $session->session.' · '.ucfirst($session->term).' term' : 'this period' }} yet.</div>
    @else
        @php $passRate = $summary->entries ? $summary->passed / $summary->entries * 100 : 0; @endphp
        <div class="row g-4 mb-4">
            <div class="col-6 col-lg-3"><div class="card h-100"><div class="card-body">
                <div class="text-muted small">Students with results</div>
                <div class="h3 mb-0">{{ number_format($summary->students) }}</div>
            </div></div></div>
            <div class="col-6 col-lg-3"><div class="card h-100"><div class="card-body">
                <div class="text-muted small">Result entries</div>
                <div class="h3 mb-0">{{ number_format($summary->entries) }}</div>
            </div></div></div>
            <div class="col-6 col-lg-3"><div class="card h-100"><div class="card-body">
                <div class="text-muted small">Average score</div>
                <div class="h3 mb-0">{{ number_format($summary->average, 1) }}</div>
            </div></div></div>
            <div class="col-6 col-lg-3"><div class="card h-100"><div class="card-body">
                <div class="text-muted small">Pass rate (&ge; {{ $passMark }})</div>
                <div class="h3 mb-0">{{ number_format($passRate, 1) }}%</div>
            </div></div></div>
        </div>

        <div class="row g-4">
            <div class="col-lg-6"><div class="card h-100"><div class="card-body">
                <h5 class="card-title">Average score by class</h5>
                @foreach ($byClass as $row)
                    @include('proprietor._bar', [
                        'label' => $row->name.' · '.$row->students.' students · '.number_format($row->passed / max($row->entries, 1) * 100).'% pass',
                        'value' => number_format($row->average, 1),
                        'percent' => $row->average,
                        'color' => $row->average >= $passMark ? 'success' : 'warning',
                    ])
                @endforeach
            </div></div></div>

            <div class="col-lg-6"><div class="card h-100"><div class="card-body">
                <h5 class="card-title">Average score by subject</h5>
                @foreach ($bySubject as $row)
                    @include('proprietor._bar', [
                        'label' => $row->name.' · '.number_format($row->passed / max($row->entries, 1) * 100).'% pass',
                        'value' => number_format($row->average, 1),
                        'percent' => $row->average,
                        'color' => $row->average >= $passMark ? 'info' : 'warning',
                    ])
                @endforeach
            </div></div></div>

            <div class="col-lg-5"><div class="card h-100"><div class="card-body">
                <h5 class="card-title">Grade distribution</h5>
                @forelse ($grades as $grade => $total)
                    @include('proprietor._bar', [
                        'label' => 'Grade '.$grade,
                        'value' => number_format($total),
                        'percent' => $total / $grades->sum() * 100,
                        'color' => 'primary',
                    ])
                @empty
                    <p class="text-muted mb-0">No grades recorded.</p>
                @endforelse
            </div></div></div>

            <div class="col-lg-7"><div class="card h-100"><div class="card-body">
                <h5 class="card-title">Top 10 students (average across subjects)</h5>
                <table class="table table-sm mb-0">
                    <thead><tr><th>#</th><th>Student</th><th>Class</th><th class="text-end">Average</th></tr></thead>
                    <tbody>
                    @foreach ($topStudents as $i => $row)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $row->first_name }} {{ $row->last_name }} <small class="text-muted">{{ $row->admission_number }}</small></td>
                            <td>{{ $row->class_name ?? '—' }}</td>
                            <td class="text-end">{{ number_format($row->average, 1) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div></div></div>
        </div>
        <p class="small text-muted mt-3">A result counts as a pass when its total score is at least {{ $passMark }}. Class figures use the class recorded on each result.</p>
    @endif
</div>
@endsection
