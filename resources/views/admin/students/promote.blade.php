@extends('layouts.dashboard')

@section('title', 'Bulk Promote Students')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0 text-gray-800">Bulk Promote/Demote Students</h1>
        </div>
    </div>

    @if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Promote Students</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.students.bulk-promote') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="from_class_id">From Class *</label>
                    <select class="form-control" id="from_class_id" name="from_class_id" required>
                        <option value="">Select Source Class</option>
                        @foreach($classes as $class)
                        <option value="{{ $class->id }}">{{ $class->name }}{{ $class->arm ? ' - ' . $class->arm : '' }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="to_class_id">To Class *</label>
                    <select class="form-control" id="to_class_id" name="to_class_id" required>
                        <option value="">Select Destination Class</option>
                        @foreach($classes as $class)
                        <option value="{{ $class->id }}">{{ $class->name }}{{ $class->arm ? ' - ' . $class->arm : '' }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> This will promote all active students from the source class to the destination class.
                </div>

                <div class="form-group">
                    <a href="{{ route('admin.students.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-arrow-up"></i> Promote Students
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Demote Students</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.students.bulk-demote') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="demote_from_class_id">From Class *</label>
                    <select class="form-control" id="demote_from_class_id" name="from_class_id" required>
                        <option value="">Select Source Class</option>
                        @foreach($classes as $class)
                        <option value="{{ $class->id }}">{{ $class->name }}{{ $class->arm ? ' - ' . $class->arm : '' }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="demote_to_class_id">To Class *</label>
                    <select class="form-control" id="demote_to_class_id" name="to_class_id" required>
                        <option value="">Select Destination Class</option>
                        @foreach($classes as $class)
                        <option value="{{ $class->id }}">{{ $class->name }}{{ $class->arm ? ' - ' . $class->arm : '' }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i> This will demote all active students from the source class to the destination class.
                </div>

                <div class="form-group">
                    <a href="{{ route('admin.students.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-arrow-down"></i> Demote Students
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
