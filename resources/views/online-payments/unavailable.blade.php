@extends('layouts.landing')

@section('title', 'Online Payment Unavailable')

@section('content')
    <x-landing.navbar />

    <section class="section-padding" style="padding-top: 120px; min-height: 60vh;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm" style="border-radius: 12px; border-top: 4px solid #ffc107 !important;">
                        <div class="card-body p-4 p-md-5">
                            <h2 class="mb-3"><i class="fas fa-info-circle text-warning me-2"></i>Online Payment Unavailable</h2>
                            <p>{{ $reason ?? 'Online payment is not available at this time.' }}</p>
                            <a href="{{ route('home') }}" class="btn btn-primary-custom mt-2">
                                <i class="fas fa-home me-1"></i>Back to Home
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <x-landing.footer />
@endsection
