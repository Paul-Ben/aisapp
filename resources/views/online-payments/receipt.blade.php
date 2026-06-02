@extends('layouts.landing')

@section('title', 'Payment Receipt')

@section('content')
    <x-landing.navbar />

    <section class="section-padding" style="padding-top: 120px; min-height: 80vh;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-9">

                    <div class="text-center mb-4 no-print">
                        <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                        <h1 class="mt-3">Payment Successful</h1>
                        <p class="text-muted">Your payment has been verified by Paystack.</p>
                    </div>

                    <div class="card border-0 shadow-sm" id="receipt" style="border-radius: 12px; border-top: 4px solid #28a745 !important;">
                        <div class="card-body p-4 p-md-5">
                            <div class="text-center mb-4">
                                <h2 class="mb-1">{{ $school['name'] ?? 'Alven International Schools' }}</h2>
                                @if (!empty($school['address']))
                                    <p class="text-muted mb-0">{{ $school['address'] }}</p>
                                @endif
                                @if (!empty($school['phone']) || !empty($school['email']))
                                    <p class="text-muted small mb-0">
                                        @if (!empty($school['phone']))<i class="fas fa-phone me-1"></i>{{ $school['phone'] }}@endif
                                        @if (!empty($school['phone']) && !empty($school['email'])) &middot; @endif
                                        @if (!empty($school['email']))<i class="fas fa-envelope me-1"></i>{{ $school['email'] }}@endif
                                    </p>
                                @endif
                                <hr class="my-3">
                                <h3 class="text-uppercase text-muted" style="letter-spacing: 2px; font-size: 1.1rem;">Official Payment Receipt</h3>
                            </div>

                            <div class="row mb-4">
                                <div class="col-sm-6 mb-3 mb-sm-0">
                                    <div class="text-muted small text-uppercase">Receipt No.</div>
                                    <div class="fw-bold">{{ $payment->reference }}</div>
                                </div>
                                <div class="col-sm-6 text-sm-end">
                                    <div class="text-muted small text-uppercase">Date Paid</div>
                                    <div class="fw-bold">{{ $payment->paid_at?->format('F d, Y') ?? '—' }}</div>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-sm-6 mb-3 mb-sm-0">
                                    <div class="text-muted small text-uppercase">Student Name</div>
                                    <div class="fw-bold">{{ $payment->student->full_name ?? '—' }}</div>
                                    <div class="text-muted small">Adm. No. {{ $payment->student->admission_number ?? '—' }}</div>
                                </div>
                                <div class="col-sm-6 text-sm-end">
                                    <div class="text-muted small text-uppercase">Class</div>
                                    <div class="fw-bold">{{ $payment->student->class->name ?? '—' }}</div>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-sm-6">
                                    <div class="text-muted small text-uppercase">Academic Session</div>
                                    <div class="fw-bold">{{ $payment->academicYear->session ?? '—' }} &middot; {{ ucfirst($payment->term) }} Term</div>
                                </div>
                                <div class="col-sm-6 text-sm-end">
                                    <div class="text-muted small text-uppercase">Payment Method</div>
                                    <div class="fw-bold">
                                        Paystack
                                        @if ($payment->gateway_channel)
                                            &middot; {{ ucfirst($payment->gateway_channel) }}
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table align-middle">
                                    <thead>
                                        <tr>
                                            <th>Description</th>
                                            <th class="text-end">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <strong>{{ $payment->feeItem->name ?? 'School Fee' }}</strong>
                                            </td>
                                            <td class="text-end">&#8358;{{ number_format((float) $payment->amount_paid, 2) }}</td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-light">
                                            <th>Total Paid</th>
                                            <th class="text-end">&#8358;{{ number_format((float) $payment->amount_paid, 2) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <div class="mt-4 p-3" style="background: #f4f7f6; border-radius: 8px;">
                                <div class="row small">
                                    <div class="col-sm-6 mb-1">
                                        <span class="text-muted">Paystack Reference:</span>
                                        <code class="ms-1">{{ $payment->gateway_reference }}</code>
                                    </div>
                                    <div class="col-sm-6 text-sm-end">
                                        <span class="badge bg-success"><i class="fas fa-check me-1"></i>Verified by Paystack</span>
                                    </div>
                                </div>
                            </div>

                            <div class="text-center mt-4 small text-muted">
                                Thank you for your payment. Please retain this receipt for your records.
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-4 no-print">
                        <button onclick="window.print()" class="btn btn-primary-custom me-2">
                            <i class="fas fa-print me-1"></i>Print Receipt
                        </button>
                        <a href="{{ route('pay-online.receipt.pdf', ['payment' => $payment->id]) }}" class="btn btn-accent">
                            <i class="fas fa-file-pdf me-1"></i>Download PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <x-landing.footer />

    <style>
        @media print {
            .no-print, .navbar, footer, .btn { display: none !important; }
            body { background: white !important; }
            .card { box-shadow: none !important; border: 1px solid #ddd !important; }
            section { padding: 0 !important; }
        }
    </style>
@endsection
