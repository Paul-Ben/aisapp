@extends('layouts.app')

@section('title', 'Student Details')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0 text-gray-800">Student Details: {{ $student->full_name }}</h1>
                <div>
                    <a href="{{ route('admin.students.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                    <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Profile Picture</h6>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-user-circle fa-8x text-gray-300"></i>
                    </div>
                    <h5>{{ $student->full_name }}</h5>
                    <p class="text-muted">{{ $student->admission_number }}</p>
                    <span class="badge badge-{{ $student->status === 'active' ? 'success' : ($student->status === 'graduated' ? 'info' : 'warning') }}">
                        {{ ucfirst($student->status) }}
                    </span>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Personal Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Full Name</label>
                            <p class="mb-0">{{ $student->full_name }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Admission Number</label>
                            <p class="mb-0">{{ $student->admission_number }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Date of Birth</label>
                            <p class="mb-0">{{ $student->date_of_birth->format('F j, Y') }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Gender</label>
                            <p class="mb-0">{{ ucfirst($student->gender) }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Email</label>
                            <p class="mb-0">{{ $student->email ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Phone</label>
                            <p class="mb-0">{{ $student->phone ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Blood Group</label>
                            <p class="mb-0">{{ $student->blood_group ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Genotype</label>
                            <p class="mb-0">{{ $student->genotype ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Address Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="text-muted small">Address</label>
                            <p class="mb-0">{{ $student->address ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="text-muted small">State</label>
                            <p class="mb-0">{{ $student->state ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="text-muted small">LGA</label>
                            <p class="mb-0">{{ $student->lga ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="text-muted small">Nationality</label>
                            <p class="mb-0">{{ $student->nationality ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Academic Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="text-muted small">Current Class</label>
                            <p class="mb-0">{{ $student->class ? $student->class->name . ($student->class->arm ? ' - ' . $student->class->arm : '') : 'Not Assigned' }}</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="text-muted small">Previous Class</label>
                            <p class="mb-0">{{ $student->previousClass ? $student->previousClass->name . ($student->previousClass->arm ? ' - ' . $student->previousClass->arm : '') : 'N/A' }}</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="text-muted small">Admission Date</label>
                            <p class="mb-0">{{ $student->admission_date->format('F j, Y') }}</p>
                        </div>
                        @if($student->graduation_date)
                        <div class="col-md-4 mb-3">
                            <label class="text-muted small">Graduation Date</label>
                            <p class="mb-0">{{ $student->graduation_date->format('F j, Y') }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
