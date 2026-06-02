@extends('layouts.landing')

@section('title', $title ?? 'Notice')

@section('content')
    <x-landing.navbar />

    <section class="section-padding" style="padding-top: 120px; min-height: 60vh;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm" style="border-radius: 12px; border-top: 4px solid #dc3545 !important;">
                        <div class="card-body p-4 p-md-5 text-center">
                            <i class="fas fa-exclamation-triangle text-danger" style="font-size: 3rem;"></i>
                            <h2 class="mt-3">{{ $title ?? 'Notice' }}</h2>
                            <p class="text-muted">{{ $message ?? '' }}</p>
                            <a href="{{ route('pay-online.search') }}" class="btn btn-primary-custom mt-3">
                                <i class="fas fa-redo me-1"></i>Start Over
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <x-landing.footer />
@endsection
