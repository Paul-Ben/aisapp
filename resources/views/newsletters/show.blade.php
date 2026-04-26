@extends('layouts.landing')

@section('title', 'Newsletter - Alven International Schools')

@section('content')
    <x-landing.navbar />
    <div class="row mb-5"></div>
    <!-- Newsletter Section -->
    <section class="section-padding" style="background-color: var(--color-bg-light); min-height: 80vh;">
        <div class="container">
            <div class="section-title">
                <h2>School Newsletter</h2>
                <p>View the latest newsletter from Alven International Schools</p>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-10">
                    @if($newsletter && $newsletter->pdf_path)
                        <div class="card shadow" style="border-radius: var(--border-radius-md);">
                            <div class="card-body p-4 text-center">
                                @if($newsletter->term || $newsletter->session)
                                    <h4 class="mb-3" style="color: var(--color-primary);">
                                        {{ $newsletter->term }} {{ $newsletter->session ? '(' . $newsletter->session . ')' : '' }}
                                    </h4>
                                @endif
                                <div class="mb-4">
                                    <i class="fas fa-file-pdf fa-5x text-danger mb-3"></i>
                                    <p class="text-muted">Newsletter PDF Document</p>
                                </div>
                                <div class="d-flex gap-3 justify-content-center flex-wrap">
                                    <a href="{{ asset('storage/' . $newsletter->pdf_path) }}" 
                                       target="_blank" 
                                       class="btn btn-primary-custom">
                                        <i class="fas fa-eye me-2"></i>View PDF
                                    </a>
                                    <a href="{{ asset('storage/' . $newsletter->pdf_path) }}" 
                                       download 
                                       class="btn btn-accent">
                                        <i class="fas fa-download me-2"></i>Download PDF
                                    </a>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-file-pdf fa-5x text-muted mb-4"></i>
                            <h4 class="text-muted">No Newsletter Available</h4>
                            <p class="text-muted">The newsletter has not been uploaded yet.</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="text-center mt-4">
                <a href="{{ route('home') }}" class="btn btn-primary-custom">
                    <i class="fas fa-arrow-left me-2"></i>Back to Home
                </a>
            </div>
        </div>
    </section>

    <x-landing.footer />
@endsection
