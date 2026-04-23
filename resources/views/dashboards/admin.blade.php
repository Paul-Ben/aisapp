@extends('layouts.dashboard')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h2 class="card-title"><i class="fas fa-user-shield me-2"></i>Admin Dashboard</h2>
                    <p class="card-text">Welcome, {{ auth()->user()->name }}! Manage daily operations and staff.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Staff Management -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-users fa-3x text-info mb-3"></i>
                    <h5 class="card-title">Staff Management</h5>
                    <p class="card-text">Manage teaching and non-teaching staff</p>
                    <a href="#" class="btn btn-info">Manage Staff</a>
                </div>
            </div>
        </div>

        <!-- Student Management -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-user-graduate fa-3x text-primary mb-3"></i>
                    <h5 class="card-title">Student Management</h5>
                    <p class="card-text">Manage student records and admissions</p>
                    <a href="#" class="btn btn-primary">Students</a>
                </div>
            </div>
        </div>

        <!-- Class Management -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-chalkboard-teacher fa-3x text-success mb-3"></i>
                    <h5 class="card-title">Class Management</h5>
                    <p class="card-text">Organize classes and timetables</p>
                    <a href="#" class="btn btn-success">Classes</a>
                </div>
            </div>
        </div>

        <!-- Attendance Tracking -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-clipboard-check fa-3x text-warning mb-3"></i>
                    <h5 class="card-title">Attendance Tracking</h5>
                    <p class="card-text">Monitor staff and student attendance</p>
                    <a href="#" class="btn btn-warning">Attendance</a>
                </div>
            </div>
        </div>

        <!-- Academic Calendar -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-calendar-alt fa-3x text-danger mb-3"></i>
                    <h5 class="card-title">Academic Calendar</h5>
                    <p class="card-text">View and manage school calendar</p>
                    <a href="#" class="btn btn-danger">Calendar</a>
                </div>
            </div>
        </div>

        <!-- Announcements -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-bullhorn fa-3x text-secondary mb-3"></i>
                    <h5 class="card-title">Announcements</h5>
                    <p class="card-text">Post and manage announcements</p>
                    <a href="#" class="btn btn-secondary">Announcements</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
