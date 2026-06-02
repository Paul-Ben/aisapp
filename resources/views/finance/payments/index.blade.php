@extends('layouts.dashboard')

@section('title', 'Fee Collection — Search Student')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2><i class="fas fa-search me-2"></i>Fee Collection</h2>
            <p class="text-muted">Search for a student by name, admission number, or class to record or update their payment.</p>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('finance.payments.index') }}" method="GET">
                <div class="input-group input-group-lg">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" name="q" value="{{ $query }}"
                           class="form-control"
                           placeholder="Search by student name, admission number, or class"
                           autofocus>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-search me-1"></i>Search
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if($query !== '')
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-3">
                    Search Results
                    <span class="badge bg-secondary">{{ $students->count() }}</span>
                </h5>

                @if($students->isEmpty())
                    <div class="text-center py-4">
                        <i class="fas fa-user-graduate fa-3x text-muted mb-3"></i>
                        <p class="text-muted mb-0">No active students found for "{{ $query }}".</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Admission No.</th>
                                    <th>Name</th>
                                    <th>Class</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($students as $s)
                                    <tr>
                                        <td><code>{{ $s->admission_number }}</code></td>
                                        <td><strong>{{ $s->full_name }}</strong></td>
                                        <td>
                                            @if($s->class)
                                                {{ $s->class->full_name }}
                                                @if($s->class->category)
                                                    <small class="text-muted">({{ $s->class->category->name }})</small>
                                                @endif
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('finance.payments.student', $s) }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-money-bill-wave me-1"></i>Manage Payments
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection
