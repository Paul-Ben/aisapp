@extends('layouts.dashboard')

@section('title', 'My Classes')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2><i class="fas fa-book me-2 text-success"></i>My Classes</h2>
                <a href="{{ route('staff.dashboard') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($classes->count() > 0)
        <div class="row g-4">
            @foreach($classes as $class)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm border-success">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h5 class="card-title text-success mb-0">
                                    {{ $class->name }}
                                    @if($class->arm)
                                        <span class="badge bg-secondary">{{ $class->arm }}</span>
                                    @endif
                                </h5>
                                <span class="badge bg-{{ $class->is_active ? 'success' : 'danger' }}">
                                    {{ $class->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            
                            @if($class->category)
                                <p class="text-muted mb-2">
                                    <i class="fas fa-layer-group me-1"></i>
                                    {{ $class->category->name }}
                                </p>
                            @endif
                            
                            @if($class->description)
                                <p class="card-text small text-muted">{{ Str::limit($class->description, 80) }}</p>
                            @endif
                            
                            <div class="mt-3">
                                <small class="text-muted">
                                    <i class="fas fa-users me-1"></i>
                                    {{ $class->staff->count() }} Teacher(s) Assigned
                                </small>
                            </div>
                            
                            <div class="mt-4">
                                <a href="{{ route('staff.classes.students', $class->id) }}" class="btn btn-success w-100">
                                    <i class="fas fa-eye me-1"></i> View Students
                                </a>
                            </div>
                        </div>
                        <div class="card-footer bg-white border-top-0">
                            <small class="text-muted">
                                <i class="fas fa-calendar me-1"></i>
                                Academic Year: {{ $class->academic_year }}
                            </small>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-info" role="alert">
            <i class="fas fa-info-circle me-2"></i>
            You are not currently assigned to any classes. Please contact the administrator.
        </div>
    @endif
</div>
@endsection
