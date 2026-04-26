@extends('layouts.app')

@section('title', 'Bulk Upload Students')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0 text-gray-800">Bulk Upload Students</h1>
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
            <h6 class="m-0 font-weight-bold text-primary">Upload Instructions</h6>
        </div>
        <div class="card-body">
            <p>Follow these steps to bulk upload students:</p>
            <ol>
                <li>Download the sample CSV template to understand the required format.</li>
                <li>Fill in student data in the CSV file. Required fields: admission_number, first_name, last_name, date_of_birth, gender.</li>
                <li>Upload the completed CSV file.</li>
                <li>Optionally assign all uploaded students to a specific class.</li>
            </ol>
            
            <a href="{{ route('admin.students.download-template') }}" class="btn btn-info mt-3">
                <i class="fas fa-download"></i> Download CSV Template
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Upload File</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.students.process-upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="file">CSV/Excel File *</label>
                    <input type="file" class="form-control-file" id="file" name="file" accept=".csv,.xlsx,.xls" required>
                    <small class="text-muted">Accepted formats: CSV, XLSX, XLS (Max 10MB)</small>
                </div>
                
                <div class="form-group">
                    <label for="class_id">Assign to Class (Optional)</label>
                    <select class="form-control" id="class_id" name="class_id">
                        <option value="">No Class Assignment</option>
                        @foreach($classes as $class)
                        <option value="{{ $class->id }}">{{ $class->name }}{{ $class->arm ? ' - ' . $class->arm : '' }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <a href="{{ route('admin.students.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload"></i> Upload Students
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
