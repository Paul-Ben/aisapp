@extends('layouts.dashboard')

@section('title', 'Revenue Tracking')

@section('content')
<div class="container-fluid">

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1"><i class="fas fa-chart-bar me-2 text-success"></i>Revenue Tracking</h1>
            <p class="text-muted mb-0">All recorded payments across students, classes, sessions, and payment methods.</p>
        </div>
        <div class="text-muted small">
            @if ($activeSession)
                <i class="fas fa-calendar-alt me-1"></i>
                Active term: <strong>{{ $activeSession->session }} &middot; {{ ucfirst($activeSession->term) }}</strong>
            @else
                <i class="fas fa-exclamation-circle me-1 text-warning"></i>
                No active academic session
            @endif
        </div>
    </div>

    @if (! $activeTermStats['has_session'])
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-1"></i>
            The active-term stats below are zero because no academic session is currently marked as active. Ask the administrator to set an active session.
        </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid var(--color-primary) !important;">
                <div class="card-body">
                    <div class="text-muted small text-uppercase">Payments (Active Term)</div>
                    <div class="h3 mb-0 fw-bold">{{ number_format($activeTermStats['count']) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid var(--color-accent-green) !important;">
                <div class="card-body">
                    <div class="text-muted small text-uppercase">Revenue (Active Term)</div>
                    <div class="h3 mb-0 fw-bold">&#8358;{{ number_format($activeTermStats['total'], 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid var(--color-accent-blue) !important;">
                <div class="card-body">
                    <div class="text-muted small text-uppercase">Online (Paystack)</div>
                    <div class="h4 mb-0 fw-bold">{{ number_format($activeTermStats['online_count']) }} <small class="text-muted fs-6">payments</small></div>
                    <div class="text-muted small">&#8358;{{ number_format($activeTermStats['online_total'], 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid var(--color-accent-yellow) !important;">
                <div class="card-body">
                    <div class="text-muted small text-uppercase">Manual (Recorded)</div>
                    <div class="h4 mb-0 fw-bold">{{ number_format($activeTermStats['manual_count']) }} <small class="text-muted fs-6">payments</small></div>
                    <div class="text-muted small">&#8358;{{ number_format($activeTermStats['manual_total'], 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('finance.revenue.index') }}" class="row g-2 align-items-end">
                <div class="col-lg-3 col-md-6">
                    <label for="student_name" class="form-label small text-muted mb-1">Student Name</label>
                    <input type="text" name="student_name" id="student_name" class="form-control form-control-sm"
                           value="{{ $filters['student_name'] ?? '' }}" placeholder="First, middle, or last">
                </div>
                <div class="col-lg-2 col-md-6">
                    <label for="student_number" class="form-label small text-muted mb-1">Student Number</label>
                    <input type="text" name="student_number" id="student_number" class="form-control form-control-sm"
                           value="{{ $filters['student_number'] ?? '' }}" placeholder="Admission no.">
                </div>
                <div class="col-lg-2 col-md-6">
                    <label for="class_id" class="form-label small text-muted mb-1">Class</label>
                    <select name="class_id" id="class_id" class="form-select form-select-sm">
                        <option value="">All classes</option>
                        @foreach ($allClasses as $class)
                            <option value="{{ $class->id }}" @selected(($filters['class_id'] ?? null) == $class->id)>
                                {{ $class->name }}{{ $class->category ? ' · '.$class->category->name : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label for="payment_method" class="form-label small text-muted mb-1">Payment Method</label>
                    <select name="payment_method" id="payment_method" class="form-select form-select-sm">
                        <option value="">All methods</option>
                        <option value="online" @selected(($filters['payment_method'] ?? '') === 'online')>Online (Paystack)</option>
                        <option value="manual" @selected(($filters['payment_method'] ?? '') === 'manual')>Manual</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label for="academic_year_id" class="form-label small text-muted mb-1">Session</label>
                    <select name="academic_year_id" id="academic_year_id" class="form-select form-select-sm">
                        <option value="">All sessions</option>
                        @foreach ($allSessions as $session)
                            <option value="{{ $session->id }}" @selected(($filters['academic_year_id'] ?? null) == $session->id)>
                                {{ $session->session }}{{ $session->is_active ? ' (active)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-1 col-md-6">
                    <label for="term" class="form-label small text-muted mb-1">Term</label>
                    <select name="term" id="term" class="form-select form-select-sm">
                        <option value="">All</option>
                        <option value="first" @selected(($filters['term'] ?? '') === 'first')>First</option>
                        <option value="second" @selected(($filters['term'] ?? '') === 'second')>Second</option>
                        <option value="third" @selected(($filters['term'] ?? '') === 'third')>Third</option>
                    </select>
                </div>
                <div class="col-12 d-flex gap-2 mt-2">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-filter me-1"></i>Filter
                    </button>
                    <a href="{{ route('finance.revenue.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-times me-1"></i>Reset
                    </a>
                    @if ($hasExplicitFilters)
                        <span class="badge bg-info align-self-center">
                            {{ $payments->total() }} matching payment{{ $payments->total() === 1 ? '' : 's' }}
                        </span>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Student</th>
                            <th>Adm. No.</th>
                            <th>Class</th>
                            <th>Fee</th>
                            <th>Session / Term</th>
                            <th>Method</th>
                            <th>Reference</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($payments as $payment)
                            <tr>
                                <td>
                                    <div>{{ $payment->paid_at?->format('M d, Y') ?? '—' }}</div>
                                    <div class="text-muted small">{{ $payment->paid_at?->format('h:i A') }}</div>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $payment->student?->full_name ?? '—' }}</div>
                                </td>
                                <td><code class="small">{{ $payment->student?->admission_number ?? '—' }}</code></td>
                                <td>{{ $payment->student?->class?->name ?? '—' }}</td>
                                <td>{{ $payment->feeItem?->name ?? '—' }}</td>
                                <td>
                                    <div>{{ $payment->academicYear?->session ?? '—' }}</div>
                                    <div class="text-muted small">{{ ucfirst($payment->term) }} term</div>
                                </td>
                                <td>
                                    @if ($payment->gateway === 'paystack')
                                        <span class="badge bg-primary"><i class="fas fa-globe me-1"></i>Online</span>
                                        @if ($payment->gateway_channel)
                                            <div class="text-muted small">{{ ucfirst($payment->gateway_channel) }}</div>
                                        @endif
                                    @else
                                        <span class="badge bg-secondary"><i class="fas fa-pen me-1"></i>Manual</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($payment->reference)
                                        <code class="small">{{ $payment->reference }}</code>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-end fw-semibold">&#8358;{{ number_format((float) $payment->amount_paid, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-5">
                                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                    No payments found for the current filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($payments->hasPages())
            <div class="card-footer bg-white">
                {{ $payments->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
