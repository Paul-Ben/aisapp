@extends('layouts.landing')

@section('title', 'Pay School Fees Online')

@section('content')
    <x-landing.navbar />

    <section class="section-padding" style="padding-top: 120px; min-height: 80vh;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="text-center mb-5">
                        <h1>Pay School Fees Online</h1>
                        <p class="lead text-muted">Secure payments powered by Paystack. Enter your child's admission number and your email to begin.</p>
                    </div>

                    @if (session('error'))
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                        </div>
                    @endif

                    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                        <div class="card-body p-4 p-md-5">
                            <form method="GET" action="{{ route('pay-online.search') }}">
                                <div class="mb-4">
                                    <label for="admission_number" class="form-label fw-semibold">Student Admission Number</label>
                                    <input
                                        type="text"
                                        name="admission_number"
                                        id="admission_number"
                                        class="form-control form-control-lg"
                                        placeholder="e.g. AIS-2026-001"
                                        value="{{ $admissionNumber }}"
                                        required
                                        autofocus
                                    >
                                </div>

                                <div class="mb-4">
                                    <label for="email" class="form-label fw-semibold">Your Email Address</label>
                                    <input
                                        type="email"
                                        name="email"
                                        id="email"
                                        class="form-control form-control-lg"
                                        placeholder="you@example.com"
                                        value="{{ $email }}"
                                        required
                                    >
                                    <div class="form-text">Paystack will send the payment receipt to this email.</div>
                                </div>

                                <button type="submit" class="btn btn-primary-custom btn-lg w-100">
                                    <i class="fas fa-search me-2"></i>Find Student
                                </button>
                            </form>
                        </div>
                    </div>

                    @if ($error)
                        <div class="alert alert-warning mt-4">
                            <i class="fas fa-exclamation-triangle me-2"></i>{{ $error }}
                        </div>
                    @endif

                    @if ($student)
                        <div class="card border-0 shadow-sm mt-4" style="border-radius: 12px; border-top: 4px solid #003399 !important;">
                            <div class="card-body p-4 p-md-5">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="feature-icon me-3" style="width: 60px; height: 60px; font-size: 1.5rem;">
                                        <i class="fas fa-user-graduate"></i>
                                    </div>
                                    <div>
                                        <h3 class="mb-1">{{ $student->full_name }}</h3>
                                        <p class="text-muted mb-0">
                                            <i class="fas fa-chalkboard me-1"></i>
                                            {{ $student->class->name ?? 'No class assigned' }}
                                            @if ($student->class?->category)
                                                <span class="text-muted">&middot; {{ $student->class->category->name }}</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <a href="{{ route('pay-online.fees', ['student' => $student, 'email' => $email]) }}"
                                   class="btn btn-accent btn-lg w-100">
                                    <i class="fas fa-credit-card me-2"></i>Make Payment
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <x-landing.footer />
@endsection
