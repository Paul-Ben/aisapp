@extends('layouts.dashboard')

@section('title', 'Super Admin Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h2 class="card-title"><i class="fas fa-shield-alt me-2"></i>Super Admin Dashboard</h2>
                    <p class="card-text">Welcome, {{ auth()->user()->name }}! You have full access to all system features.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- User Management -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-users fa-3x text-primary mb-3"></i>
                    <h5 class="card-title">User Management</h5>
                    <p class="card-text">Manage all users, roles and permissions</p>
                    <a href="#" class="btn btn-primary">Manage Users</a>
                </div>
            </div>
        </div>

        <!-- System Settings -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-cogs fa-3x text-secondary mb-3"></i>
                    <h5 class="card-title">System Settings</h5>
                    <p class="card-text">Configure application settings</p>
                    <a href="#" class="btn btn-secondary">Settings</a>
                </div>
            </div>
        </div>

        <!-- Reports & Analytics -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-chart-line fa-3x text-success mb-3"></i>
                    <h5 class="card-title">Reports & Analytics</h5>
                    <p class="card-text">View comprehensive reports</p>
                    <a href="#" class="btn btn-success">View Reports</a>
                </div>
            </div>
        </div>

        <!-- Finance Overview -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-dollar-sign fa-3x text-warning mb-3"></i>
                    <h5 class="card-title">Finance Overview</h5>
                    <p class="card-text">Monitor financial activities</p>
                    <a href="#" class="btn btn-warning">Finance</a>
                </div>
            </div>
        </div>

        <!-- Exam Management -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-graduation-cap fa-3x text-info mb-3"></i>
                    <h5 class="card-title">Exam Management</h5>
                    <p class="card-text">Oversee exam operations</p>
                    <a href="#" class="btn btn-info">Exams</a>
                </div>
            </div>
        </div>

        <!-- Staff Management -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-id-badge fa-3x text-danger mb-3"></i>
                    <h5 class="card-title">Staff Management</h5>
                    <p class="card-text">Manage staff records</p>
                    <a href="#" class="btn btn-danger">Staff</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
