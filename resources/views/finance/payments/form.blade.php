@extends('layouts.dashboard')

@section('title', ($payment ? 'Update' : 'Record') . ' Payment — ' . $student->full_name)

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2>
                <i class="fas {{ $payment ? 'fa-edit' : 'fa-plus-circle' }} me-2"></i>
                {{ $payment ? 'Update' : 'Record' }} Payment
            </h2>
            <p class="text-muted mb-0">
                <strong>{{ $student->full_name }}</strong> &middot;
                <code>{{ $student->admission_number }}</code> &middot;
                {{ $session->session }} — {{ ucfirst($session->term) }} Term
            </p>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="alert alert-light border mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted d-block">Fee Item</small>
                        <strong class="fs-5">{{ $fee->name }}</strong>
                        @if($fee->description)
                            <br><small class="text-muted">{{ $fee->description }}</small>
                        @endif
                    </div>
                    <div class="text-end">
                        <small class="text-muted d-block">Amount Due</small>
                        <strong class="fs-4 text-warning">₦{{ number_format((float) $fee->amount, 2) }}</strong>
                    </div>
                </div>
            </div>

            <form action="{{ route('finance.payments.save', ['student' => $student, 'fee' => $fee, 'session_id' => $session->id]) }}" method="POST">
                @csrf
                @method('PUT')

                <h5 class="mb-3 text-info"><i class="fas fa-money-check-alt me-2"></i>Payment Details</h5>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label d-block">Status <span class="text-danger">*</span></label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="status" id="status_paid" value="paid"
                                   {{ old('status', $payment?->status) === 'paid' ? 'checked' : '' }}
                                   onchange="toggleAmountDefault()">
                            <label class="form-check-label" for="status_paid">
                                <span class="badge bg-success">Paid</span> &mdash; Full payment
                            </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="status" id="status_part" value="part"
                                   {{ old('status', $payment?->status ?? 'part') === 'part' ? 'checked' : '' }}
                                   onchange="toggleAmountDefault()">
                            <label class="form-check-label" for="status_part">
                                <span class="badge bg-warning text-dark">Part</span> &mdash; Partial payment
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="amount_paid" class="form-label">Amount Paid (₦) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0"
                               class="form-control form-control-lg @error('amount_paid') is-invalid @enderror"
                               id="amount_paid" name="amount_paid"
                               value="{{ old('amount_paid', $payment ? (float) $payment->amount_paid : '') }}"
                               required>
                        @error('amount_paid')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="paid_at" class="form-label">Payment Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('paid_at') is-invalid @enderror"
                               id="paid_at" name="paid_at"
                               value="{{ old('paid_at', $payment?->paid_at?->format('Y-m-d') ?? now()->format('Y-m-d')) }}"
                               required>
                        @error('paid_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="reference" class="form-label">Reference / Receipt No.</label>
                        <input type="text" class="form-control @error('reference') is-invalid @enderror"
                               id="reference" name="reference"
                               value="{{ old('reference', $payment?->reference) }}"
                               placeholder="e.g., teller number, receipt no.">
                        @error('reference')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="notes" class="form-label">Notes</label>
                    <textarea class="form-control @error('notes') is-invalid @enderror"
                              id="notes" name="notes" rows="2"
                              placeholder="Optional notes (e.g., bank, channel, payer name)">{{ old('notes', $payment?->notes) }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('finance.payments.student', ['student' => $student, 'session_id' => $session->id]) }}"
                       class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Back to Student
                    </a>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save me-1"></i>{{ $payment ? 'Update Payment' : 'Record Payment' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const feeAmount = {{ (float) $fee->amount }};

    function toggleAmountDefault() {
        const paid = document.getElementById('status_paid');
        const amountInput = document.getElementById('amount_paid');
        if (paid.checked) {
            amountInput.value = feeAmount.toFixed(2);
        }
    }
</script>
@endpush
@endsection
