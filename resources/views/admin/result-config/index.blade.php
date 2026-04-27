@extends('layouts.dashboard')

@section('title', 'Result Configuration')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h2 class="card-title"><i class="fas fa-cog me-2"></i>Result Configuration</h2>
                    <p class="card-text">Configure result settings and grading scales for each class</p>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Class Result Configurations</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>Class Name</th>
                                    <th>Category</th>
                                    <th>Max CA Score</th>
                                    <th>Max Project Score</th>
                                    <th>Max Exam Score</th>
                                    <th>Grading Scale</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($classes as $class)
                                    <tr>
                                        <td><strong>{{ $class->full_name }}</strong></td>
                                        <td>{{ $class->category->name ?? 'N/A' }}</td>
                                        <td>
                                            @if($class->resultConfig)
                                                {{ $class->resultConfig->max_ca_score }}
                                            @else
                                                <span class="text-muted">Not configured</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($class->resultConfig && $class->resultConfig->project_enabled)
                                                {{ $class->resultConfig->max_project_score }}
                                            @else
                                                <span class="text-muted">Disabled</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($class->resultConfig)
                                                {{ $class->resultConfig->max_exam_score }}
                                            @else
                                                <span class="text-muted">Not configured</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($class->resultConfig && $class->resultConfig->gradeScales->count() > 0)
                                                <small>
                                                    @foreach($class->resultConfig->gradeScales->take(3) as $scale)
                                                        <span class="badge bg-info">{{ $scale->grade }}: {{ $scale->min_percentage }}-{{ $scale->max_percentage }}%</span>
                                                    @endforeach
                                                    @if($class->resultConfig->gradeScales->count() > 3)
                                                        <span class="badge bg-secondary">+{{ $class->resultConfig->gradeScales->count() - 3 }} more</span>
                                                    @endif
                                                </small>
                                            @else
                                                <span class="text-muted">Default</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.result-config.edit', $class->id) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i> Configure
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">No classes found. Please create classes first.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
