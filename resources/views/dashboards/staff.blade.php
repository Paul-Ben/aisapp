@extends('layouts.dashboard')

@section('title', 'Staff Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h2 class="card-title"><i class="fas fa-chalkboard-teacher me-2"></i>Staff Dashboard</h2>
                    <p class="card-text">Welcome, {{ auth()->user()->name }}! Access your teaching resources and tasks.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- My Classes -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-book fa-3x text-success mb-3"></i>
                    <h5 class="card-title">My Classes</h5>
                    <p class="card-text">View your assigned classes and subjects</p>
                    <a href="{{ route('staff.classes.index') }}" class="btn btn-success">My Classes</a>
                </div>
            </div>
        </div>

        <!-- Attendance -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-clipboard-list fa-3x text-primary mb-3"></i>
                    <h5 class="card-title">Mark Attendance</h5>
                    <p class="card-text">Take student attendance for your classes</p>
                    <a href="#" class="btn btn-primary">Attendance</a>
                </div>
            </div>
        </div>

        <!-- Lesson Plans -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-book-open fa-3x text-info mb-3"></i>
                    <h5 class="card-title">Lesson Plans</h5>
                    <p class="card-text">Create and manage lesson plans</p>
                    <a href="#" class="btn btn-info">Lesson Plans</a>
                </div>
            </div>
        </div>

        <!-- Assignments -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-pencil-alt fa-3x text-warning mb-3"></i>
                    <h5 class="card-title">Assignments</h5>
                    <p class="card-text">Create and grade student assignments</p>
                    <a href="#" class="btn btn-warning">Assignments</a>
                </div>
            </div>
        </div>

        <!-- Student Results -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-star fa-3x text-danger mb-3"></i>
                    <h5 class="card-title">Enter Results</h5>
                    <p class="card-text">Submit exam scores and grades</p>
                    <a href="{{ route('staff.results.index') }}" class="btn btn-danger">Results</a>
                </div>
            </div>
        </div>

        <!-- Timetable -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-calendar-week fa-3x text-secondary mb-3"></i>
                    <h5 class="card-title">My Timetable</h5>
                    <p class="card-text">View your weekly teaching schedule</p>
                    <a href="#" class="btn btn-secondary">Timetable</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
