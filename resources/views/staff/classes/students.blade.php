@extends('layouts.dashboard')

@section('title', 'Students in ' . $schoolClass->full_name)

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2>
                    <i class="fas fa-users me-2 text-success"></i>
                    Students in {{ $schoolClass->full_name }}
                </h2>
                <a href="{{ route('staff.classes.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back to My Classes
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

    <!-- Class Info Card -->
    <div class="card mb-4 border-success">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <h6 class="text-muted mb-1">Class Name</h6>
                    <p class="mb-0 fw-bold">{{ $schoolClass->name }}</p>
                </div>
                @if($schoolClass->arm)
                    <div class="col-md-3">
                        <h6 class="text-muted mb-1">Arm</h6>
                        <p class="mb-0 fw-bold">{{ $schoolClass->arm }}</p>
                    </div>
                @endif
                @if($schoolClass->category)
                    <div class="col-md-3">
                        <h6 class="text-muted mb-1">Category</h6>
                        <p class="mb-0 fw-bold">{{ $schoolClass->category->name }}</p>
                    </div>
                @endif
                <div class="col-md-3">
                    <h6 class="text-muted mb-1">Total Students</h6>
                    <p class="mb-0 fw-bold text-success">{{ $students->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Students Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-success">
                    <i class="fas fa-user-graduate me-2"></i>Student List
                </h5>
                <span class="badge bg-success">{{ $students->count() }} Student(s)</span>
            </div>
        </div>
        <div class="card-body p-0">
            @if($students->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" style="width: 50px;">#</th>
                                <th scope="col">Admission Number</th>
                                <th scope="col">Full Name</th>
                                <th scope="col">Gender</th>
                                <th scope="col">Email</th>
                                <th scope="col">Phone</th>
                                <th scope="col">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $index => $student)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <span class="badge bg-primary">{{ $student->admission_number }}</span>
                                    </td>
                                    <td>
                                        <strong>{{ $student->full_name }}</strong>
                                    </td>
                                    <td>
                                        @if($student->gender === 'male')
                                            <span class="badge bg-info"><i class="fas fa-male me-1"></i>Male</span>
                                        @elseif($student->gender === 'female')
                                            <span class="badge bg-danger"><i class="fas fa-female me-1"></i>Female</span>
                                        @else
                                            <span class="badge bg-secondary">N/A</span>
                                        @endif
                                    </td>
                                    <td>{{ $student->email ?? 'N/A' }}</td>
                                    <td>{{ $student->phone ?? 'N/A' }}</td>
                                    <td>
                                        @if($student->status === 'active')
                                            <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Active</span>
                                        @elseif($student->status === 'graduated')
                                            <span class="badge bg-info"><i class="fas fa-graduation-cap me-1"></i>Graduated</span>
                                        @elseif($student->status === 'inactive')
                                            <span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i>Inactive</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($student->status) }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-user-slash fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">No Students Found</h5>
                    <p class="text-muted">There are no active students in this class yet.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .badge-pink {
        background-color: #e83e8c;
        color: white;
    }
</style>
@endsection
