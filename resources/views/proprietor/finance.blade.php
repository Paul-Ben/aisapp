@extends('layouts.dashboard')

@section('title', 'Financial Reports')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
        <h3 class="mb-0"><i class="fas fa-chart-line me-2"></i>Financial Reports</h3>
        @include('proprietor._session-filter')
    </div>
    @include('proprietor._nav')

    @if (! $session)
        <div class="alert alert-info">No academic session is selected or active, so there is nothing to report yet.</div>
    @else
        <div class="row g-4 mb-4">
            <div class="col-6 col-lg-3"><div class="card h-100"><div class="card-body">
                <div class="text-muted small">Collected</div>
                <div class="h4 mb-0">&#8358;{{ number_format($totals['collected'], 2) }}</div>
                <div class="small text-muted">{{ number_format($totals['payments']) }} payments</div>
            </div></div></div>
            <div class="col-6 col-lg-3"><div class="card h-100"><div class="card-body">
                <div class="text-muted small">Billed to active students</div>
                <div class="h4 mb-0">&#8358;{{ number_format($totals['expected'], 2) }}</div>
            </div></div></div>
            <div class="col-6 col-lg-3"><div class="card h-100"><div class="card-body">
                <div class="text-muted small">Outstanding</div>
                <div class="h4 mb-0">&#8358;{{ number_format($totals['outstanding'], 2) }}</div>
            </div></div></div>
            <div class="col-6 col-lg-3"><div class="card h-100"><div class="card-body">
                <div class="text-muted small">Collection rate</div>
                <div class="h4 mb-0">{{ $totals['rate'] !== null ? $totals['rate'].'%' : '—' }}</div>
            </div></div></div>
        </div>

        <div class="row g-4">
            <div class="col-lg-6"><div class="card h-100"><div class="card-body">
                <h5 class="card-title">Online vs manual</h5>
                @php $all = max((float) $totals['collected'], 0.01); @endphp
                @foreach (['online' => 'success', 'manual' => 'secondary'] as $key => $color)
                    @php $row = $method->get($key); @endphp
                    @include('proprietor._bar', [
                        'label' => ucfirst($key).' ('.number_format($row->payments ?? 0).' payments)',
                        'value' => '₦'.number_format($row->total ?? 0, 2),
                        'percent' => ($row->total ?? 0) / $all * 100,
                        'color' => $color,
                    ])
                @endforeach
            </div></div></div>

            <div class="col-lg-6"><div class="card h-100"><div class="card-body">
                <h5 class="card-title">Revenue by term</h5>
                @php $maxTerm = max($trend->max('total') ?? 0, 0.01); @endphp
                @forelse ($trend as $point)
                    @include('proprietor._bar', [
                        'label' => $point['label'],
                        'value' => '₦'.number_format($point['total'], 2),
                        'percent' => $point['total'] / $maxTerm * 100,
                        'color' => 'primary',
                    ])
                @empty
                    <p class="text-muted mb-0">No payments recorded yet.</p>
                @endforelse
            </div></div></div>

            <div class="col-lg-6"><div class="card h-100"><div class="card-body">
                <h5 class="card-title">By fee item</h5>
                <table class="table table-sm mb-0">
                    <thead><tr><th>Fee</th><th class="text-end">Payments</th><th class="text-end">Total</th></tr></thead>
                    <tbody>
                    @forelse ($byFee as $row)
                        <tr><td>{{ $row->name }}</td><td class="text-end">{{ number_format($row->payments) }}</td><td class="text-end">&#8358;{{ number_format($row->total, 2) }}</td></tr>
                    @empty
                        <tr><td colspan="3" class="text-muted">No payments in this period.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div></div></div>

            <div class="col-lg-6"><div class="card h-100"><div class="card-body">
                <h5 class="card-title">By class</h5>
                <table class="table table-sm mb-0">
                    <thead><tr><th>Class</th><th class="text-end">Collected</th></tr></thead>
                    <tbody>
                    @forelse ($byClass as $row)
                        <tr><td>{{ $row->name }}</td><td class="text-end">&#8358;{{ number_format($row->total, 2) }}</td></tr>
                    @empty
                        <tr><td colspan="2" class="text-muted">No payments in this period.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div></div></div>
        </div>
        <p class="small text-muted mt-3">
            "Billed" is every active fee assigned to a student's class or section, multiplied by active students. Collected counts recorded payments, including part payments.
            Class figures use each student's <em>current</em> class.
        </p>
    @endif
</div>
@endsection
