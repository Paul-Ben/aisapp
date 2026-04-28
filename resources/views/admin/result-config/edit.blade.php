@extends('layouts.dashboard')

@section('title', 'Edit Result Configuration')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h2 class="card-title"><i class="fas fa-edit me-2"></i>Edit Result Configuration</h2>
                    <p class="card-text">Configure result settings for {{ $schoolClass->full_name }}</p>
                </div>
            </div>
        </div>
    </div>

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

    <form action="{{ route('admin.result-config.update', $schoolClass->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <!-- Score Settings -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Score Settings</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="max_ca_score" class="form-label">
                                <i class="fas fa-pencil-alt me-1"></i>Max Continuous Assessment (CA) Score
                            </label>
                            <input type="number" 
                                   class="form-control @error('max_ca_score') is-invalid @enderror" 
                                   id="max_ca_score" 
                                   name="max_ca_score" 
                                   value="{{ old('max_ca_score', $resultConfig->max_ca_score) }}"
                                   min="0" 
                                   max="100"
                                   required>
                            <div class="form-text">Maximum score for Continuous Assessment</div>
                            @error('max_ca_score')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       name="project_enabled" 
                                       id="project_enabled"
                                       value="1"
                                       {{ old('project_enabled', $resultConfig->project_enabled) ? 'checked' : '' }}>
                                <label class="form-check-label" for="project_enabled">
                                    Enable Project Score
                                </label>
                            </div>
                        </div>

                        <div class="mb-3" id="project_score_div" style="{{ !$resultConfig->project_enabled ? 'display:none;' : '' }}">
                            <label for="max_project_score" class="form-label">
                                <i class="fas fa-project-diagram me-1"></i>Max Project Score
                            </label>
                            <input type="number" 
                                   class="form-control @error('max_project_score') is-invalid @enderror" 
                                   id="max_project_score" 
                                   name="max_project_score" 
                                   value="{{ old('max_project_score', $resultConfig->max_project_score) }}"
                                   min="0" 
                                   max="100">
                            <div class="form-text">Maximum score for Project (optional)</div>
                            @error('max_project_score')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="max_exam_score" class="form-label">
                                <i class="fas fa-file-alt me-1"></i>Max Exam Score
                            </label>
                            <input type="number" 
                                   class="form-control @error('max_exam_score') is-invalid @enderror" 
                                   id="max_exam_score" 
                                   name="max_exam_score" 
                                   value="{{ old('max_exam_score', $resultConfig->max_exam_score) }}"
                                   min="0" 
                                   max="100"
                                   required>
                            <div class="form-text">Maximum score for Examination</div>
                            @error('max_exam_score')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grading Scale -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-graduation-cap me-2"></i>Grading Scale</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small">Define percentage ranges for each grade (A, B, C, D, E, F)</p>
                        
                        <div id="grade_scales_container">
                            @php
                                $defaultGrades = ['A', 'B', 'C', 'D', 'E', 'F'];
                                $existingScales = $resultConfig->gradeScales->keyBy('grade');
                            @endphp

                            @foreach($defaultGrades as $index => $grade)
                                <div class="row mb-3 grade-scale-row">
                                    <div class="col-md-2">
                                        <label class="form-label fw-bold">Grade</label>
                                        <input type="text" 
                                               class="form-control text-center" 
                                               name="grade_scales[{{ $index }}][grade]" 
                                               value="{{ $grade }}"
                                               readonly
                                               style="font-weight: bold;">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Min %</label>
                                        <input type="number" 
                                               class="form-control @error('grade_scales.'.$index.'.min_percentage') is-invalid @enderror" 
                                               name="grade_scales[{{ $index }}][min_percentage]" 
                                               value="{{ old('grade_scales.'.$index.'.min_percentage', $existingScales[$grade]->min_percentage ?? ($index * 10)) }}"
                                               min="0" 
                                               max="100"
                                               required>
                                        @error('grade_scales.'.$index.'.min_percentage')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Max %</label>
                                        <input type="number" 
                                               class="form-control @error('grade_scales.'.$index.'.max_percentage') is-invalid @enderror" 
                                               name="grade_scales[{{ $index }}][max_percentage]" 
                                               value="{{ old('grade_scales.'.$index.'.max_percentage', $existingScales[$grade]->max_percentage ?? (($index + 1) * 10 - 1)) }}"
                                               min="0" 
                                               max="100"
                                               required>
                                        @error('grade_scales.'.$index.'.max_percentage')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <span class="badge bg-info w-100 py-2">{{ $grade }} Grade</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <a href="{{ route('admin.result-config.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back to List
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Save Configuration
                </button>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const projectEnabledCheckbox = document.getElementById('project_enabled');
    const projectScoreDiv = document.getElementById('project_score_div');

    if (projectEnabledCheckbox) {
        projectEnabledCheckbox.addEventListener('change', function() {
            if (this.checked) {
                projectScoreDiv.style.display = 'block';
                document.getElementById('max_project_score').required = true;
            } else {
                projectScoreDiv.style.display = 'none';
                document.getElementById('max_project_score').required = false;
            }
        });
    }
});
</script>
@endsection
