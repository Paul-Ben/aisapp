@extends('layouts.dashboard')

@section('title', 'Proprietor Dashboard')

@section('content')
<div class="container-fluid">
    <div class="card bg-danger text-white mb-4">
        <div class="card-body">
            <h2 class="card-title"><i class="fas fa-crown me-2"></i>Proprietor Dashboard</h2>
            <p class="card-text mb-0">
                Welcome, {{ auth()->user()->name }}!
                @if ($session)
                    Showing {{ $session->session }} · {{ ucfirst($session->term) }} term.
                @else
                    No academic session is active, so term figures are unavailable.
                @endif
            </p>
        </div>
    </div>

    @include('proprietor._nav')

    <div class="row g-4 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card h-100"><div class="card-body">
                <div class="text-muted small">Active students</div>
                <div class="h3 mb-0">{{ number_format($enrolled) }}</div>
            </div></div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card h-100"><div class="card-body">
                <div class="text-muted small">Active staff</div>
                <div class="h3 mb-0">{{ number_format($staffCount) }}</div>
            </div></div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card h-100"><div class="card-body">
                <div class="text-muted small">Fees collected this term</div>
                <div class="h3 mb-0">&#8358;{{ number_format($collected, 2) }}</div>
                <div class="small text-muted">{{ $collectionRate !== null ? $collectionRate.'% of amount billed' : 'No fees assigned yet' }}</div>
            </div></div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card h-100"><div class="card-body">
                <div class="text-muted small">Average score this term</div>
                <div class="h3 mb-0">{{ $averageScore !== null && $averageScore > 0 ? $averageScore : '—' }}</div>
            </div></div>
        </div>
    </div>

    <div class="row g-4">
        @foreach ([
            ['proprietor.overview', 'fa-school', 'text-danger', 'School Overview', 'Students, staff, classes and this term at a glance'],
            ['proprietor.finance', 'fa-chart-line', 'text-success', 'Financial Reports', 'Collections, outstanding fees and revenue trend'],
            ['proprietor.academics', 'fa-graduation-cap', 'text-primary', 'Academic Reports', 'Scores, pass rates and grade distribution'],
            ['proprietor.enrollment', 'fa-users', 'text-warning', 'Enrollment Stats', 'Enrollment by class, gender, admissions and retention'],
        ] as [$route, $icon, $color, $title, $text])
            <div class="col-md-6 col-xl-3">
                <a href="{{ route($route) }}" class="card h-100 text-decoration-none text-body">
                    <div class="card-body text-center">
                        <i class="fas {{ $icon }} fa-3x {{ $color }} mb-3"></i>
                        <h5 class="card-title">{{ $title }}</h5>
                        <p class="card-text text-muted">{{ $text }}</p>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
</div>
@endsection
