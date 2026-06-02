@extends('layouts.landing')

@section('title', 'Outstanding Fees')

@section('content')
    <x-landing.navbar />

    <section class="section-padding" style="padding-top: 120px; min-height: 80vh;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-9">
                    <div class="mb-4">
                        <a href="{{ route('pay-online.search', ['admission_number' => $student->admission_number, 'email' => $email]) }}"
                           class="text-decoration-none text-muted">
                            <i class="fas fa-arrow-left me-1"></i>Back to search
                        </a>
                    </div>

                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                        <div class="card-body p-4 p-md-5">
                            <div class="d-flex align-items-center">
                                <div class="feature-icon me-3" style="width: 60px; height: 60px; font-size: 1.5rem;">
                                    <i class="fas fa-user-graduate"></i>
                                </div>
                                <div>
                                    <h2 class="mb-1">{{ $student->full_name }}</h2>
                                    <p class="text-muted mb-0">
                                        <i class="fas fa-chalkboard me-1"></i>{{ $student->class->name ?? 'No class' }}
                                        <span class="ms-3"><i class="fas fa-id-card me-1"></i>{{ $student->admission_number }}</span>
                                    </p>
                                </div>
                            </div>
                            <hr class="my-3">
                            <p class="mb-0 text-muted">
                                <i class="fas fa-calendar-alt me-1"></i>
                                <strong>{{ $session->session }}</strong> &middot; {{ ucfirst($session->term) }} Term
                            </p>
                        </div>
                    </div>

                    @if (session('error'))
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                        </div>
                    @endif

                    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                        <div class="card-body p-4 p-md-5">
                            <h3 class="mb-3">Outstanding Fees</h3>
                            <p class="text-muted">Each fee below can be paid separately. Click <strong>Pay</strong> next to the fee you wish to pay; you will be redirected to Paystack's secure payment page.</p>

                            @if ($outstandingFees->isEmpty())
                                <div class="alert alert-success mt-3 mb-0">
                                    <i class="fas fa-check-circle me-2"></i>No outstanding fees for {{ $session->session }} {{ ucfirst($session->term) }} term.
                                </div>
                            @else
                                <div class="table-responsive mt-3">
                                    <table class="table align-middle">
                                        <thead>
                                            <tr>
                                                <th>Fee</th>
                                                <th class="text-end">Amount</th>
                                                <th class="text-end" style="width: 200px;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($outstandingFees as $fee)
                                                <tr>
                                                    <td>
                                                        <strong>{{ $fee->name }}</strong>
                                                        @if ($fee->description)
                                                            <div class="text-muted small">{{ $fee->description }}</div>
                                                        @endif
                                                    </td>
                                                    <td class="text-end">&#8358;{{ number_format((float) $fee->amount, 2) }}</td>
                                                    <td class="text-end">
                                                        <form method="POST" action="{{ route('pay-online.initialize', ['student' => $student, 'fee' => $fee]) }}">
                                                            @csrf
                                                            <input type="hidden" name="email" value="{{ $email }}">
                                                            <button type="submit" class="btn btn-primary-custom">
                                                                <i class="fas fa-lock me-1"></i>Pay &#8358;{{ number_format((float) $fee->amount, 2) }}
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr class="table-light">
                                                <th>Total Outstanding</th>
                                                <th class="text-end">&#8358;{{ number_format($total, 2) }}</th>
                                                <th></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <p class="text-muted small mb-0 mt-3">
                                    <i class="fas fa-shield-alt me-1"></i>
                                    Payments are securely processed by Paystack. Cards, bank transfers, and USSD are supported.
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <x-landing.footer />
@endsection
