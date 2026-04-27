@extends('layouts.dashboard')

@section('title', 'Graduated Students')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0 text-gray-800">Graduated Students</h1>
                <div>
                    <a href="{{ route('admin.students.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back to Student Management
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filters</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.students.graduates') }}" class="row">
                <div class="col-md-4 mb-3">
                    <label>Search</label>
                    <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Name, Admission No, Email">
                </div>
                <div class="col-md-3 mb-3">
                    <label>Graduation Session</label>
                    <select class="form-control" name="session_id">
                        <option value="">All Sessions</option>
                        @foreach($sessions as $session)
                            <option value="{{ $session->id }}" {{ request('session_id') == $session->id ? 'selected' : '' }}>
                                {{ $session->session }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <label>Year</label>
                    <select class="form-control" name="year">
                        <option value="">All Years</option>
                        @foreach($years as $year)
                            <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary mr-2">Filter</button>
                    <a href="{{ route('admin.students.graduates') }}" class="btn btn-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Graduates List ({{ $graduates->total() }})</h6>
        </div>
        <div class="card-body">
            @if($graduates->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="graduatesTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th>Admission No</th>
                            <th>Full Name</th>
                            <th>Gender</th>
                            <th>Graduation Session</th>
                            <th>Graduation Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($graduates as $student)
                        <tr>
                            <td>{{ $student->admission_number }}</td>
                            <td>{{ $student->first_name }} {{ $student->middle_name }} {{ $student->last_name }}</td>
                            <td><span class="badge badge-{{ $student->gender === 'male' ? 'primary' : 'danger' }}">{{ ucfirst($student->gender) }}</span></td>
                            <td>{{ $student->graduationSession ? $student->graduationSession->session : 'N/A' }}</td>
                            <td>{{ $student->graduation_date ? $student->graduation_date->format('d M, Y') : 'N/A' }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.students.show', $student) }}" class="btn btn-info" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-5">
                <i class="fas fa-user-graduate fa-4x text-muted mb-3"></i>
                <p>No graduated students found.</p>
            </div>
            @endif
        </div>
        @if($graduates->hasPages())
        <div class="card-footer">
            {{ $graduates->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
