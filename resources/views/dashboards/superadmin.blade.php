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

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    <div class="row g-4">
        <!-- User Management -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-users-cog fa-3x text-primary mb-3"></i>
                    <h5 class="card-title">User Management</h5>
                    <p class="card-text">Manage all users, assign roles, and control access</p>
                    <a href="{{ route('superadmin.users.index') }}" class="btn btn-primary">Manage Users</a>
                </div>
            </div>
        </div>

        <!-- System Settings -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-cogs fa-3x text-secondary mb-3"></i>
                    <h5 class="card-title">System Settings</h5>
                    <p class="card-text">Configure system-wide settings and preferences</p>
                    <a href="#" class="btn btn-secondary">Settings</a>
                </div>
            </div>
        </div>

        <!-- Audit Logs -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-history fa-3x text-info mb-3"></i>
                    <h5 class="card-title">Audit Logs</h5>
                    <p class="card-text">View system activity and user actions</p>
                    <a href="#" class="btn btn-info">View Logs</a>
                </div>
            </div>
        </div>

        <!-- Role Management -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-user-tag fa-3x text-warning mb-3"></i>
                    <h5 class="card-title">Role Management</h5>
                    <p class="card-text">Define and manage user roles and permissions</p>
                    <a href="#" class="btn btn-warning">Manage Roles</a>
                </div>
            </div>
        </div>

        <!-- Backup & Restore -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-database fa-3x text-success mb-3"></i>
                    <h5 class="card-title">Backup & Restore</h5>
                    <p class="card-text">Manage database backups and restoration</p>
                    <a href="#" class="btn btn-success">Backup</a>
                </div>
            </div>
        </div>

        <!-- View Website -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-external-link-alt fa-3x text-danger mb-3"></i>
                    <h5 class="card-title">View Website</h5>
                    <p class="card-text">Visit the public-facing website</p>
                    <a href="{{ url('/') }}" target="_blank" class="btn btn-danger">Visit Site</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
