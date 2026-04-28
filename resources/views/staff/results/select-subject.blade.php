@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2><i class="fas fa-book me-2"></i>Select Subject for {{ $schoolClass->name }} {{ $schoolClass->arm }}</h2>
                <a href="{{ route('staff.results.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back to Classes
                </a>
            </div>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row mb-3">
        <div class="col-md-6">
            <div class="card bg-light">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-info-circle me-2"></i>Class Information</h5>
                    <p class="mb-1"><strong>Class:</strong> {{ $schoolClass->name }} {{ $schoolClass->arm }}</p>
                    <p class="mb-1"><strong>Category:</strong> {{ $schoolClass->category->name ?? 'N/A' }}</p>
                    <p class="mb-1"><strong>Academic Year:</strong> {{ $schoolClass->academicYear->session ?? 'N/A' }}</p>
                    <p class="mb-1"><strong>Total Students:</strong> {{ $schoolClass->students()->count() }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card bg-light">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-cog me-2"></i>Result Configuration</h5>
                    <p class="mb-1"><strong>Max CA Score:</strong> {{ $resultConfig->max_ca_score }}</p>
                    @if($resultConfig->has_project)
                        <p class="mb-1"><strong>Max Project Score:</strong> {{ $resultConfig->max_project_score }}</p>
                    @endif
                    <p class="mb-1"><strong>Max Exam Score:</strong> {{ $resultConfig->max_exam_score }}</p>
                    <p class="mb-0"><strong>Total Obtainable:</strong> 
                        {{ $resultConfig->max_ca_score + ($resultConfig->has_project ? $resultConfig->max_project_score : 0) + $resultConfig->max_exam_score }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        @forelse($subjects as $subject)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">{{ $subject->name }}</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text">
                            <strong>Code:</strong> {{ $subject->code }}
                        </p>
                        <p class="card-text">
                            <strong>Type:</strong> 
                            <span class="badge {{ $subject->is_compulsory ? 'bg-primary' : 'bg-warning' }}">
                                {{ $subject->is_compulsory ? 'Compulsory' : 'Elective' }}
                            </span>
                        </p>
                        <p class="card-text">
                            <strong>Description:</strong> {{ Str::limit($subject->description, 50) ?? 'No description' }}
                        </p>
                    </div>
                    <div class="card-footer bg-white">
                        <a href="{{ route('staff.results.upload', ['classId' => $schoolClass->id, 'subjectId' => $subject->id]) }}" 
                           class="btn btn-success w-100">
                            <i class="fas fa-upload me-2"></i>Upload Results
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-warning text-center">
                    <i class="fas fa-info-circle me-2"></i>No subjects assigned to this class yet.
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
