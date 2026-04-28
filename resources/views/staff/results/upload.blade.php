@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2><i class="fas fa-upload me-2"></i>Upload Results - {{ $subject->name }} ({{ $schoolClass->name }} {{ $schoolClass->arm }})</h2>
                <a href="{{ route('staff.results.subjects', $schoolClass->id) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back to Subjects
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

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Configuration Info -->
    <div class="card mb-4 bg-light">
        <div class="card-body">
            <h5 class="card-title"><i class="fas fa-info-circle me-2"></i>Scoring Configuration</h5>
            <div class="row">
                <div class="col-md-3">
                    <strong>Max CA Score:</strong> {{ $resultConfig->max_ca_score }}
                </div>
                @if($resultConfig->has_project)
                <div class="col-md-3">
                    <strong>Max Project Score:</strong> {{ $resultConfig->max_project_score }}
                </div>
                @endif
                <div class="col-md-3">
                    <strong>Max Exam Score:</strong> {{ $resultConfig->max_exam_score }}
                </div>
                <div class="col-md-3">
                    <strong>Total:</strong> 
                    {{ $resultConfig->max_ca_score + ($resultConfig->has_project ? $resultConfig->max_project_score : 0) + $resultConfig->max_exam_score }}
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs for Upload Methods -->
    <ul class="nav nav-tabs mb-3" id="uploadTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="excel-tab" data-bs-toggle="tab" data-bs-target="#excel" type="button">
                <i class="fas fa-file-excel me-2"></i>Excel Upload
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="manual-tab" data-bs-toggle="tab" data-bs-target="#manual" type="button">
                <i class="fas fa-keyboard me-2"></i>Manual Entry
            </button>
        </li>
    </ul>

    <div class="tab-content" id="uploadTabsContent">
        <!-- Excel Upload Tab -->
        <div class="tab-pane fade show active" id="excel" role="tabpanel">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-file-excel me-2"></i>Upload via Excel</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-download me-2"></i>
                        <strong>Step 1:</strong> Download the template below, fill in the scores, and upload it.
                    </div>
                    
                    <a href="{{ route('staff.results.download-template', ['classId' => $schoolClass->id, 'subjectId' => $subject->id]) }}" 
                       class="btn btn-outline-primary mb-3">
                        <i class="fas fa-download me-2"></i>Download Template
                    </a>

                    <form action="{{ route('staff.results.process-upload', ['classId' => $schoolClass->id, 'subjectId' => $subject->id]) }}" 
                          method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="excel_file" class="form-label"><strong>Step 2:</strong> Select Excel File</label>
                            <input type="file" class="form-control" id="excel_file" name="excel_file" 
                                   accept=".xlsx,.xls,.csv" required>
                            <div class="form-text">Accepted formats: .xlsx, .xls, .csv</div>
                        </div>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-upload me-2"></i>Upload & Process
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Manual Entry Tab -->
        <div class="tab-pane fade" id="manual" role="tabpanel">
            <form action="{{ route('staff.results.manual-save', ['classId' => $schoolClass->id, 'subjectId' => $subject->id]) }}" 
                  method="POST">
                @csrf
                <input type="hidden" name="term" value="First">
                
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Enter Scores Manually</h5>
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Admission No</th>
                                    <th>Student Name</th>
                                    <th>CA Score (Max: {{ $resultConfig->max_ca_score }})</th>
                                    @if($resultConfig->has_project)
                                        <th>Project Score (Max: {{ $resultConfig->max_project_score }})</th>
                                    @endif
                                    <th>Exam Score (Max: {{ $resultConfig->max_exam_score }})</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($schoolClass->students as $student)
                                    <tr>
                                        <td>{{ $student->admission_number ?? 'N/A' }}</td>
                                        <td>{{ $student->full_name }}</td>
                                        <input type="hidden" name="scores[{{ $loop->index }}][student_id]" value="{{ $student->id }}">
                                        <td>
                                            <input type="number" class="form-control score-input" 
                                                   name="scores[{{ $loop->index }}][ca_score]" 
                                                   min="0" max="{{ $resultConfig->max_ca_score }}" 
                                                   step="0.01" 
                                                   value="{{ $existingResults[$student->id] ?? '' }}"
                                                   placeholder="0-{{ $resultConfig->max_ca_score }}">
                                        </td>
                                        @if($resultConfig->has_project)
                                        <td>
                                            <input type="number" class="form-control score-input" 
                                                   name="scores[{{ $loop->index }}][project_score]" 
                                                   min="0" max="{{ $resultConfig->max_project_score }}" 
                                                   step="0.01"
                                                   placeholder="0-{{ $resultConfig->max_project_score }}">
                                        </td>
                                        @endif
                                        <td>
                                            <input type="number" class="form-control score-input" 
                                                   name="scores[{{ $loop->index }}][exam_score]" 
                                                   min="0" max="{{ $resultConfig->max_exam_score }}" 
                                                   step="0.01"
                                                   placeholder="0-{{ $resultConfig->max_exam_score }}">
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ 4 + ($resultConfig->has_project ? 1 : 0) }}" class="text-center">
                                            No students found in this class.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Save All Scores
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validate score inputs in real-time
    document.querySelectorAll('.score-input').forEach(function(input) {
        input.addEventListener('blur', function() {
            const max = parseFloat(this.getAttribute('max'));
            const value = parseFloat(this.value);
            
            if (!isNaN(value) && value > max) {
                alert('Score exceeds maximum allowed value of ' + max);
                this.value = '';
                this.focus();
            }
        });
    });
});
</script>
@endpush
@endsection
