@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2><i class="fas fa-clipboard-list me-2"></i>Enter Results</h2>
                <a href="{{ route('staff.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>

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

    <div class="row">
        @forelse($classes as $class)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">{{ $class->name }} {{ $class->arm }}</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text">
                            <strong>Category:</strong> 
                            <span class="badge bg-info">{{ $class->category->name ?? 'N/A' }}</span>
                        </p>
                        <p class="card-text">
                            <strong>Academic Year:</strong> 
                            {{ $class->academicYear->session ?? 'N/A' }}
                        </p>
                        <p class="card-text">
                            <strong>Status:</strong>
                            @if($class->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </p>
                        <p class="card-text">
                            <strong>Total Students:</strong> 
                            {{ $class->students()->count() }}
                        </p>
                    </div>
                    <div class="card-footer bg-white">
                        <a href="{{ route('staff.results.subjects', $class->id) }}" class="btn btn-primary w-100">
                            <i class="fas fa-book me-2"></i>Select Subject
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-warning text-center">
                    <i class="fas fa-info-circle me-2"></i>No classes assigned to you yet.
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
