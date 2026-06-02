@extends('layouts.dashboard')

@section('title', 'Payments — ' . $student->full_name)

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="mb-1"><i class="fas fa-user-graduate me-2"></i>{{ $student->full_name }}</h2>
            <p class="text-muted mb-0">
                <code>{{ $student->admission_number }}</code>
                @if($student->class)
                    &middot; {{ $student->class->full_name }}
                @endif
            </p>
        </div>
    </div>

    <div class="alert alert-warning" role="alert">
        <h5 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>No Active Academic Session</h5>
        <p class="mb-0">There is no active academic session at the moment, so payment history can't be shown. The administrator must mark a session as active before payments can be recorded.</p>
    </div>

    @if($sessions->isNotEmpty())
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Available Sessions</h5>
                <p class="text-muted small">Pick a session to view past payments for this student.</p>
                <div class="list-group">
                    @foreach($sessions as $s)
                        <a href="{{ route('finance.payments.student', ['student' => $student, 'session_id' => $s->id]) }}"
                           class="list-group-item list-group-item-action">
                            {{ $s->session }} — {{ ucfirst($s->term) }} Term
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <a href="{{ route('finance.payments.index') }}" class="btn btn-secondary mt-3">
        <i class="fas fa-arrow-left me-1"></i>Back to Search
    </a>
</div>
@endsection
