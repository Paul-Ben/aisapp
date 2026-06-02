@extends('layouts.dashboard')

@section('title', 'Payments — ' . $student->full_name)

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-1"><i class="fas fa-user-graduate me-2"></i>{{ $student->full_name }}</h2>
                <p class="text-muted mb-0">
                    <code>{{ $student->admission_number }}</code>
                    @if($student->class)
                        &middot; {{ $student->class->full_name }}
                        @if($student->class->category)
                            ({{ $student->class->category->name }})
                        @endif
                    @endif
                </p>
            </div>
            <a href="{{ route('finance.payments.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to Search
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card mb-3">
        <div class="card-body py-2">
            <form action="{{ route('finance.payments.student', $student) }}" method="GET" class="row g-2 align-items-center">
                <label for="session_id" class="col-sm-2 col-form-label fw-semibold">
                    <i class="fas fa-calendar-alt me-1"></i>Term:
                </label>
                <div class="col-sm-5">
                    <select name="session_id" id="session_id" class="form-select" onchange="this.form.submit()">
                        @foreach($allSessions as $s)
                            <option value="{{ $s->id }}" {{ $s->id === $session->id ? 'selected' : '' }}>
                                {{ $s->session }} — {{ ucfirst($s->term) }} Term
                                @if($activeSession && $s->id === $activeSession->id)
                                    (Active)
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-5 text-end">
                    <span class="text-muted">
                        Viewing payments for
                        <strong>{{ $session->session }} — {{ ucfirst($session->term) }} Term</strong>
                    </span>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title mb-3"><i class="fas fa-receipt me-2"></i>Assigned Fees</h5>

            @if($assignedFees->isEmpty())
                <div class="text-center py-4">
                    <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                    <p class="text-muted mb-0">No fee items are currently assigned to this student's class.</p>
                </div>
            @else
                @php
                    $totalDue = $assignedFees->sum('amount');
                    $totalPaid = $payments->sum(fn ($p) => (float) $p->amount_paid);
                    $balance = $totalDue - $totalPaid;
                @endphp

                <div class="row mb-3 g-3">
                    <div class="col-md-4">
                        <div class="bg-light rounded p-3 text-center">
                            <small class="text-muted d-block">Total Due</small>
                            <strong class="fs-5">₦{{ number_format($totalDue, 2) }}</strong>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="bg-light rounded p-3 text-center">
                            <small class="text-muted d-block">Total Paid</small>
                            <strong class="fs-5 text-success">₦{{ number_format($totalPaid, 2) }}</strong>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="bg-light rounded p-3 text-center">
                            <small class="text-muted d-block">Balance</small>
                            <strong class="fs-5 {{ $balance > 0 ? 'text-danger' : 'text-success' }}">
                                ₦{{ number_format($balance, 2) }}
                            </strong>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Fee Item</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Paid</th>
                                <th>Balance</th>
                                <th>Last Paid</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($assignedFees as $fee)
                                @php $payment = $payments->get($fee->id); @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $fee->name }}</strong>
                                        @if($fee->description)
                                            <br><small class="text-muted">{{ \Illuminate\Support\Str::limit($fee->description, 60) }}</small>
                                        @endif
                                    </td>
                                    <td>₦{{ number_format((float) $fee->amount, 2) }}</td>
                                    <td>
                                        @if(!$payment)
                                            <span class="badge bg-secondary">Unpaid</span>
                                        @elseif($payment->status === 'paid')
                                            <span class="badge bg-success">Paid</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Part Payment</span>
                                        @endif
                                    </td>
                                    <td>₦{{ $payment ? number_format((float) $payment->amount_paid, 2) : '0.00' }}</td>
                                    <td>
                                        ₦{{ number_format((float) $fee->amount - ($payment ? (float) $payment->amount_paid : 0), 2) }}
                                    </td>
                                    <td>
                                        @if($payment)
                                            <small>{{ $payment->paid_at->format('M d, Y') }}</small>
                                            @if($payment->reference)
                                                <br><small class="text-muted">Ref: {{ $payment->reference }}</small>
                                            @endif
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('finance.payments.form', ['student' => $student, 'fee' => $fee, 'session_id' => $session->id]) }}"
                                           class="btn btn-sm {{ $payment ? 'btn-outline-primary' : 'btn-warning' }}">
                                            <i class="fas {{ $payment ? 'fa-edit' : 'fa-plus' }} me-1"></i>
                                            {{ $payment ? 'Update' : 'Record' }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
