@extends('layouts.dashboard')

@section('title', 'Proprietor Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h2 class="card-title"><i class="fas fa-crown me-2"></i>Proprietor Dashboard</h2>
                    <p class="card-text">Welcome, {{ auth()->user()->name }}! Oversee all school operations and performance.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- School Overview -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-school fa-3x text-danger mb-3"></i>
                    <h5 class="card-title">School Overview</h5>
                    <p class="card-text">View overall school statistics and metrics</p>
                    <a href="#" class="btn btn-danger">Overview</a>
                </div>
            </div>
        </div>

        <!-- Financial Reports -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-chart-line fa-3x text-success mb-3"></i>
                    <h5 class="card-title">Financial Reports</h5>
                    <p class="card-text">Review income, expenses and financial health</p>
                    <a href="#" class="btn btn-success">Finance</a>
                </div>
            </div>
        </div>

        <!-- Academic Performance -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-graduation-cap fa-3x text-primary mb-3"></i>
                    <h5 class="card-title">Academic Performance</h5>
                    <p class="card-text">Monitor student performance and exam results</p>
                    <a href="#" class="btn btn-primary">Performance</a>
                </div>
            </div>
        </div>

        <!-- Staff Performance -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-user-tie fa-3x text-info mb-3"></i>
                    <h5 class="card-title">Staff Performance</h5>
                    <p class="card-text">Review staff productivity and evaluations</p>
                    <a href="#" class="btn btn-info">Staff Review</a>
                </div>
            </div>
        </div>

        <!-- Enrollment Statistics -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-users fa-3x text-warning mb-3"></i>
                    <h5 class="card-title">Enrollment Stats</h5>
                    <p class="card-text">Track student enrollment and retention</p>
                    <a href="#" class="btn btn-warning">Enrollment</a>
                </div>
            </div>
        </div>

        <!-- Strategic Planning -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-chess fa-3x text-secondary mb-3"></i>
                    <h5 class="card-title">Strategic Planning</h5>
                    <p class="card-text">Set goals and plan for school development</p>
                    <a href="#" class="btn btn-secondary">Planning</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
