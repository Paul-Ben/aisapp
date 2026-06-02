@extends('layouts.dashboard')

@section('title', 'Edit Fee Item')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2><i class="fas fa-edit me-2"></i>Edit Fee Item</h2>
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

    <div class="card">
        <div class="card-body">
            <form action="{{ route('finance.fees.update', $fee) }}" method="POST">
                @csrf
                @method('PUT')

                <h5 class="mb-3 text-info"><i class="fas fa-info-circle me-2"></i>Fee Details</h5>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                               id="name" name="name" value="{{ old('name', $fee->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="amount" class="form-label">Amount (₦) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0" class="form-control @error('amount') is-invalid @enderror"
                               id="amount" name="amount" value="{{ old('amount', $fee->amount) }}" required>
                        @error('amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror"
                              id="description" name="description" rows="2">{{ old('description', $fee->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3 form-check">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1"
                           {{ old('is_active', $fee->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Active</label>
                </div>

                <hr class="my-4">

                <h5 class="mb-3 text-info"><i class="fas fa-layer-group me-2"></i>Assignment</h5>
                <p class="text-muted small">Update which classes and/or class categories this fee applies to.</p>

                @php
                    $selectedClassIds = old('classes', $fee->classes->pluck('id')->toArray());
                    $selectedCategoryIds = old('class_categories', $fee->classCategories->pluck('id')->toArray());
                @endphp

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="class_categories" class="form-label">Class Categories</label>
                        <select class="form-select @error('class_categories') is-invalid @enderror"
                                id="class_categories" name="class_categories[]" multiple size="6">
                            @forelse($classCategories as $category)
                                <option value="{{ $category->id }}" {{ in_array($category->id, $selectedCategoryIds) ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @empty
                                <option disabled>No class categories defined</option>
                            @endforelse
                        </select>
                        <div class="form-text">Hold Ctrl/Cmd to select multiple</div>
                        @error('class_categories')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="classes" class="form-label">Specific Classes</label>
                        <select class="form-select @error('classes') is-invalid @enderror"
                                id="classes" name="classes[]" multiple size="6">
                            @forelse($classes as $class)
                                <option value="{{ $class->id }}" {{ in_array($class->id, $selectedClassIds) ? 'selected' : '' }}>
                                    {{ $class->full_name }}{{ $class->category ? ' — ' . $class->category->name : '' }}
                                </option>
                            @empty
                                <option disabled>No classes defined</option>
                            @endforelse
                        </select>
                        <div class="form-text">Hold Ctrl/Cmd to select multiple</div>
                        @error('classes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('finance.fees.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Back to List
                    </a>
                    <button type="submit" class="btn btn-dark">
                        <i class="fas fa-save me-1"></i>Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
