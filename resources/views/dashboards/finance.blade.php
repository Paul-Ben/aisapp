@extends('layouts.dashboard')

@section('title', 'Finance Officer Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h2 class="card-title"><i class="fas fa-coins me-2"></i>Finance Officer Dashboard</h2>
                    <p class="card-text">Welcome, {{ auth()->user()->name }}! Manage all financial operations.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Revenue Tracking -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-chart-bar fa-3x text-success mb-3"></i>
                    <h5 class="card-title">Revenue Tracking</h5>
                    <p class="card-text">Monitor income and revenue streams</p>
                    <a href="#" class="btn btn-success">View Revenue</a>
                </div>
            </div>
        </div>

        <!-- Expense Management -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-file-invoice-dollar fa-3x text-danger mb-3"></i>
                    <h5 class="card-title">Expense Management</h5>
                    <p class="card-text">Track and manage expenses</p>
                    <a href="#" class="btn btn-danger">Expenses</a>
                </div>
            </div>
        </div>

        <!-- Financial Reports -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-file-alt fa-3x text-primary mb-3"></i>
                    <h5 class="card-title">Financial Reports</h5>
                    <p class="card-text">Generate financial reports</p>
                    <a href="#" class="btn btn-primary">Reports</a>
                </div>
            </div>
        </div>

        <!-- Fee Collection -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-money-bill-wave fa-3x text-warning mb-3"></i>
                    <h5 class="card-title">Fee Collection</h5>
                    <p class="card-text">Manage student fee collections</p>
                    <a href="#" class="btn btn-warning">Fees</a>
                </div>
            </div>
        </div>

        <!-- Payroll -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-users-cog fa-3x text-info mb-3"></i>
                    <h5 class="card-title">Payroll</h5>
                    <p class="card-text">Process staff payroll</p>
                    <a href="#" class="btn btn-info">Payroll</a>
                </div>
            </div>
        </div>

        <!-- Budget Planning -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-piggy-bank fa-3x text-secondary mb-3"></i>
                    <h5 class="card-title">Budget Planning</h5>
                    <p class="card-text">Plan and allocate budgets</p>
                    <a href="#" class="btn btn-secondary">Budget</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
