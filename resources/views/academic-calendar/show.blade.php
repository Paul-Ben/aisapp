@extends('layouts.landing')

@section('title', 'Term Calendar - Alven International Schools')

@section('content')
    <x-landing.navbar />
    <div class="row mb-5"></div>
    <!-- Academic Calendar Section -->
    <section class="section-padding" style="background-color: var(--color-bg-light); min-height: 80vh;">
        <div class="container">
            <div class="section-title">
                <h2>Academic Calendar</h2>
                <p>View the current term calendar for Alven International Schools</p>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-10">
                    @if($calendar && $calendar->image_path)
                        <div class="card shadow" style="border-radius: var(--border-radius-md);">
                            <div class="card-body p-4 text-center">
                                @if($calendar->term || $calendar->session)
                                    <h4 class="mb-3" style="color: var(--color-primary);">
                                        {{ $calendar->term }} {{ $calendar->session ? '(' . $calendar->session . ')' : '' }}
                                    </h4>
                                @endif
                                <img src="{{ asset('storage/' . $calendar->image_path) }}" 
                                     alt="Academic Calendar" 
                                     class="img-fluid rounded" 
                                     style="max-width: 100%; height: auto;">
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-alt fa-5x text-muted mb-4"></i>
                            <h4 class="text-muted">No Academic Calendar Available</h4>
                            <p class="text-muted">The academic calendar for the current term has not been uploaded yet.</p>
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
