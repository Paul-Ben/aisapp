@extends('layouts.dashboard')

@section('title', 'Enrollment Stats')

@section('content')
<div class="container-fluid">
    <h3 class="mb-3"><i class="fas fa-users me-2"></i>Enrollment Stats</h3>
    @include('proprietor._nav')

    <div class="row g-4 mb-4">
        <div class="col-6 col-lg-3"><div class="card h-100"><div class="card-body">
            <div class="text-muted small">Currently enrolled</div>
            <div class="h3 mb-0">{{ number_format($total) }}</div>
        </div></div></div>
        <div class="col-6 col-lg-3"><div class="card h-100"><div class="card-body">
            <div class="text-muted small">Male / Female</div>
            <div class="h3 mb-0">{{ $gender->get('male', 0) }} / {{ $gender->get('female', 0) }}</div>
        </div></div></div>
        <div class="col-6 col-lg-3"><div class="card h-100"><div class="card-body">
            <div class="text-muted small">Graduated</div>
            <div class="h3 mb-0">{{ number_format($statusCounts->get('graduated', 0)) }}</div>
        </div></div></div>
        <div class="col-6 col-lg-3"><div class="card h-100"><div class="card-body">
            <div class="text-muted small">Retention rate</div>
            <div class="h3 mb-0">{{ $retentionRate !== null ? $retentionRate.'%' : '—' }}</div>
            <div class="small text-muted">Students not withdrawn</div>
        </div></div></div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6"><div class="card h-100"><div class="card-body">
            <h5 class="card-title">By section</h5>
            @forelse ($byCategory as $name => $count)
                @include('proprietor._bar', [
                    'label' => $name,
                    'value' => number_format($count),
                    'percent' => $total ? $count / $total * 100 : 0,
                    'color' => 'info',
                ])
            @empty
                <p class="text-muted mb-0">No classes yet.</p>
            @endforelse
        </div></div></div>

        <div class="col-lg-6"><div class="card h-100"><div class="card-body">
            <h5 class="card-title">Admissions per year</h5>
            @php $maxAdm = max($admissionsPerYear->max() ?? 0, 1); @endphp
            @forelse ($admissionsPerYear as $year => $count)
                @include('proprietor._bar', [
                    'label' => $year,
                    'value' => number_format($count),
                    'percent' => $count / $maxAdm * 100,
                    'color' => 'success',
                ])
            @empty
                <p class="text-muted mb-0">No students yet.</p>
            @endforelse
        </div></div></div>

        <div class="col-lg-7"><div class="card h-100"><div class="card-body">
            <h5 class="card-title">By class</h5>
            <table class="table table-sm mb-0">
                <thead><tr><th>Class</th><th>Section</th><th class="text-end">Male</th><th class="text-end">Female</th><th class="text-end">Total</th></tr></thead>
                <tbody>
                @forelse ($byClass as $row)
                    <tr>
                        <td>{{ $row['name'] }}</td><td>{{ $row['category'] }}</td>
                        <td class="text-end">{{ $row['male'] }}</td><td class="text-end">{{ $row['female'] }}</td>
                        <td class="text-end fw-semibold">{{ $row['total'] }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-muted">No classes yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div></div></div>

        <div class="col-lg-5"><div class="card h-100"><div class="card-body">
            <h5 class="card-title">Graduates by session</h5>
            @forelse ($graduatesBySession as $label => $count)
                <div class="d-flex justify-content-between border-bottom py-1"><span>{{ $label }}</span><span class="fw-semibold">{{ $count }}</span></div>
            @empty
                <p class="text-muted mb-0">No graduates recorded yet.</p>
            @endforelse
            <h5 class="card-title mt-4">All students by status</h5>
            @foreach ($statusCounts as $status => $count)
                <div class="d-flex justify-content-between border-bottom py-1"><span>{{ ucfirst($status) }}</span><span class="fw-semibold">{{ $count }}</span></div>
            @endforeach
        </div></div></div>
    </div>
</div>
@endsection
