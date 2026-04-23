@extends('layouts.dashboard')

@section('title', 'Exam Officer Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h2 class="card-title"><i class="fas fa-file-signature me-2"></i>Exam Officer Dashboard</h2>
                    <p class="card-text">Welcome, {{ auth()->user()->name }}! Manage examinations and results.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Exam Scheduling -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-calendar-check fa-3x text-warning mb-3"></i>
                    <h5 class="card-title">Exam Scheduling</h5>
                    <p class="card-text">Create and manage exam timetables</p>
                    <a href="#" class="btn btn-warning">Schedule Exams</a>
                </div>
            </div>
        </div>

        <!-- Result Management -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-chart-bar fa-3x text-primary mb-3"></i>
                    <h5 class="card-title">Result Management</h5>
                    <p class="card-text">Process and publish exam results</p>
                    <a href="#" class="btn btn-primary">Results</a>
                </div>
            </div>
        </div>

        <!-- Student Registration -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-user-edit fa-3x text-success mb-3"></i>
                    <h5 class="card-title">Student Registration</h5>
                    <p class="card-text">Register students for examinations</p>
                    <a href="#" class="btn btn-success">Register</a>
                </div>
            </div>
        </div>

        <!-- Exam Venues -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-map-marker-alt fa-3x text-info mb-3"></i>
                    <h5 class="card-title">Exam Venues</h5>
                    <p class="card-text">Assign and manage examination halls</p>
                    <a href="#" class="btn btn-info">Venues</a>
                </div>
            </div>
        </div>

        <!-- Invigilation Duty -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-user-clock fa-3x text-danger mb-3"></i>
                    <h5 class="card-title">Invigilation Duty</h5>
                    <p class="card-text">Assign invigilators to exam halls</p>
                    <a href="#" class="btn btn-danger">Invigilators</a>
                </div>
            </div>
        </div>

        <!-- Report Cards -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-file-pdf fa-3x text-secondary mb-3"></i>
                    <h5 class="card-title">Report Cards</h5>
                    <p class="card-text">Generate and print report cards</p>
                    <a href="#" class="btn btn-secondary">Report Cards</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
