@extends('layouts.app')

@section('title', 'Edit Student')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0 text-gray-800">Edit Student: {{ $student->full_name }}</h1>
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

    <form action="{{ route('admin.students.update', $student) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Personal Information</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label for="admission_number">Admission Number *</label>
                        <input type="text" class="form-control" id="admission_number" name="admission_number" value="{{ old('admission_number', $student->admission_number) }}" required>
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="first_name">First Name *</label>
                        <input type="text" class="form-control" id="first_name" name="first_name" value="{{ old('first_name', $student->first_name) }}" required>
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="middle_name">Middle Name</label>
                        <input type="text" class="form-control" id="middle_name" name="middle_name" value="{{ old('middle_name', $student->middle_name) }}">
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="last_name">Last Name *</label>
                        <input type="text" class="form-control" id="last_name" name="last_name" value="{{ old('last_name', $student->last_name) }}" required>
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="date_of_birth">Date of Birth *</label>
                        <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $student->date_of_birth->format('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="gender">Gender *</label>
                        <select class="form-control" id="gender" name="gender" required>
                            <option value="">Select Gender</option>
                            <option value="male" {{ old('gender', $student->gender) === 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender', $student->gender) === 'female' ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $student->email) }}">
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="phone">Phone</label>
                        <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $student->phone) }}">
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="blood_group">Blood Group</label>
                        <input type="text" class="form-control" id="blood_group" name="blood_group" value="{{ old('blood_group', $student->blood_group) }}">
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="genotype">Genotype</label>
                        <input type="text" class="form-control" id="genotype" name="genotype" value="{{ old('genotype', $student->genotype) }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Address & Other Information</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="address">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="2">{{ old('address', $student->address) }}</textarea>
                    </div>
                    <div class="col-md-3 form-group">
                        <label for="state">State</label>
                        <input type="text" class="form-control" id="state" name="state" value="{{ old('state', $student->state) }}">
                    </div>
                    <div class="col-md-3 form-group">
                        <label for="lga">LGA</label>
                        <input type="text" class="form-control" id="lga" name="lga" value="{{ old('lga', $student->lga) }}">
                    </div>
                    <div class="col-md-3 form-group">
                        <label for="nationality">Nationality</label>
                        <input type="text" class="form-control" id="nationality" name="nationality" value="{{ old('nationality', $student->nationality) }}">
                    </div>
                    <div class="col-md-3 form-group">
                        <label for="religion">Religion</label>
                        <input type="text" class="form-control" id="religion" name="religion" value="{{ old('religion', $student->religion) }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Academic Information</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label for="class_id">Class</label>
                        <select class="form-control" id="class_id" name="class_id">
                            <option value="">Select Class</option>
                            @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ old('class_id', $student->class_id) == $class->id ? 'selected' : '' }}>
                                {{ $class->name }}{{ $class->arm ? ' - ' . $class->arm : '' }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="admission_date">Admission Date *</label>
                        <input type="date" class="form-control" id="admission_date" name="admission_date" value="{{ old('admission_date', $student->admission_date->format('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="status">Status *</label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="active" {{ old('status', $student->status) === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $student->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="graduated" {{ old('status', $student->status) === 'graduated' ? 'selected' : '' }}>Graduated</option>
                            <option value="withdrawn" {{ old('status', $student->status) === 'withdrawn' ? 'selected' : '' }}>Withdrawn</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <a href="{{ route('admin.students.index') }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Update Student</button>
        </div>
    </form>
</div>
@endsection
