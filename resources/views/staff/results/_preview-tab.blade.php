@php
    use Illuminate\Support\Number;
@endphp

<div class="card">
    <div class="card-header bg-info text-white d-flex flex-wrap justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="fas fa-eye me-2"></i>
            Uploaded Results
            @if ($activeAcademicSession)
                <small class="opacity-75">
                    &middot; {{ $activeAcademicSession->session }} &middot; {{ ucfirst($activeAcademicSession->term) }} term
                </small>
            @endif
        </h5>
        @if ($activeAcademicSession)
            <span class="badge bg-light text-dark">
                {{ $resultStats['count'] }} of {{ $resultStats['class_size'] }} uploaded
            </span>
        @endif
    </div>

    <div class="card-body">
        @if (! $activeAcademicSession)
            <div class="alert alert-warning mb-0">
                <i class="fas fa-exclamation-triangle me-1"></i>
                No active academic session is set. Ask the administrator to set an active session in the admin dashboard.
            </div>
        @else
            <div class="row g-3 mb-4">
                <div class="col-md-3 col-sm-6">
                    <div class="border rounded p-3 h-100">
                        <div class="text-muted small text-uppercase">Students in Class</div>
                        <div class="h4 mb-0 fw-bold">{{ number_format($resultStats['class_size']) }}</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="border rounded p-3 h-100" style="border-left: 4px solid #10B981 !important;">
                        <div class="text-muted small text-uppercase">Uploaded</div>
                        <div class="h4 mb-0 fw-bold">{{ number_format($resultStats['count']) }}</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="border rounded p-3 h-100" style="border-left: 4px solid #F7941D !important;">
                        <div class="text-muted small text-uppercase">Still Missing</div>
                        <div class="h4 mb-0 fw-bold">{{ number_format($resultStats['missing_count']) }}</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="border rounded p-3 h-100" style="border-left: 4px solid #003399 !important;">
                        <div class="text-muted small text-uppercase">Class Average</div>
                        <div class="h4 mb-0 fw-bold">
                            @if ($resultStats['average'] === null)
                                —
                            @else
                                {{ number_format((float) $resultStats['average'], 2) }}
                            @endif
                        </div>
                        <div class="text-muted small">
                            @if ($resultStats['highest'] !== null)
                                Highest {{ number_format((float) $resultStats['highest'], 2) }}
                                &middot; Lowest {{ number_format((float) $resultStats['lowest'], 2) }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @if ($resultStats['count'] > 0)
                <h6 class="mt-2 mb-2">
                    <i class="fas fa-list me-1"></i>Results ({{ $resultStats['count'] }})
                </h6>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-4">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Admission No</th>
                                <th>Student Name</th>
                                <th class="text-end">CA ({{ $resultConfig->max_ca_score }})</th>
                                @if ($resultConfig->project_enabled)
                                    <th class="text-end">Project ({{ $resultConfig->max_project_score }})</th>
                                @endif
                                <th class="text-end">Exam ({{ $resultConfig->max_exam_score }})</th>
                                <th class="text-end">Total</th>
                                <th>Grade</th>
                                <th>Remark</th>
                                <th>Entered On</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($uploadedResults as $index => $result)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><code class="small">{{ $result->student?->admission_number ?? '—' }}</code></td>
                                    <td>{{ $result->student?->full_name ?? '—' }}</td>
                                    <td class="text-end">{{ $result->ca_score !== null ? number_format((float) $result->ca_score, 2) : '—' }}</td>
                                    @if ($resultConfig->project_enabled)
                                        <td class="text-end">{{ $result->project_score !== null ? number_format((float) $result->project_score, 2) : '—' }}</td>
                                    @endif
                                    <td class="text-end">{{ $result->exam_score !== null ? number_format((float) $result->exam_score, 2) : '—' }}</td>
                                    <td class="text-end fw-semibold">{{ $result->total_score !== null ? number_format((float) $result->total_score, 2) : '—' }}</td>
                                    <td>
                                        <span class="badge
                                            @switch($result->grade)
                                                @case('A') bg-success @break
                                                @case('B') bg-primary @break
                                                @case('C') bg-info @break
                                                @case('D') bg-warning text-dark @break
                                                @case('E') bg-secondary @break
                                                @default bg-danger
                                            @endswitch
                                        ">{{ $result->grade ?? '—' }}</span>
                                    </td>
                                    <td>{{ $result->remark ?? '—' }}</td>
                                    <td>
                                        <div>{{ $result->created_at?->format('M d, Y') ?? '—' }}</div>
                                        <div class="text-muted small">{{ $result->created_at?->format('h:i A') }}</div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($resultStats['grade_distribution']->isNotEmpty())
                    <h6 class="mt-2 mb-2"><i class="fas fa-chart-pie me-1"></i>Grade Distribution</h6>
                    <div class="d-flex flex-wrap gap-2 mb-4">
                        @foreach ($resultStats['grade_distribution']->sortKeys() as $grade => $count)
                            <span class="badge
                                @switch($grade)
                                    @case('A') bg-success @break
                                    @case('B') bg-primary @break
                                    @case('C') bg-info @break
                                    @case('D') bg-warning text-dark @break
                                    @case('E') bg-secondary @break
                                    @default bg-danger
                                @endswitch
                            " style="font-size: 0.9rem;">
                                {{ $grade }}: {{ $count }}
                            </span>
                        @endforeach
                    </div>
                @endif
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-1"></i>
                    No results uploaded yet for <strong>{{ $activeAcademicSession->session }} &middot; {{ ucfirst($activeAcademicSession->term) }}</strong> term.
                    Use the Excel Upload or Manual Entry tabs to add results.
                </div>
            @endif

            @if ($resultStats['missing_count'] > 0)
                <h6 class="mt-2 mb-2 text-warning">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    Still Missing ({{ $resultStats['missing_count'] }})
                </h6>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Admission No</th>
                                <th>Student Name</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($missingStudents as $index => $student)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><code class="small">{{ $student->admission_number ?? '—' }}</code></td>
                                    <td>{{ $student->full_name }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        @endif
    </div>
</div>
