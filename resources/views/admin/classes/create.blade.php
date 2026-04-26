@extends('layouts.dashboard')

@section('title', 'Add New Class')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2><i class="fas fa-chalkboard-plus me-2"></i>Add New Class</h2>
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
            <form action="{{ route('admin.classes.store') }}" method="POST">
                @csrf
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="class_category_id" class="form-label">Class Category <span class="text-danger">*</span></label>
                        <select class="form-select @error('class_category_id') is-invalid @enderror" 
                                id="class_category_id" name="class_category_id" required>
                            <option value="">Select a category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('class_category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('class_category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text mt-1">
                            <a href="#" data-bs-toggle="modal" data-bs-target="#categoryModal">Create new category</a>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="name" class="form-label">Class Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name') }}" required 
                               placeholder="e.g., JSS 1, Primary 3, Nursery 2">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="arm" class="form-label">Class Arm</label>
                        <input type="text" class="form-control @error('arm') is-invalid @enderror" 
                               id="arm" name="arm" value="{{ old('arm') }}" 
                               placeholder="e.g., A, B, Red, Blue (optional)">
                        @error('arm')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="2">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('admin.classes.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Back to Classes
                    </a>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-1"></i>Create Class
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Quick Category Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.class-categories.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add New Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="quick_category_name" class="form-label">Category Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="quick_category_name" name="name" required 
                               placeholder="e.g., Nursery, Primary, Secondary">
                    </div>
                    <div class="mb-3">
                        <label for="quick_category_description" class="form-label">Description</label>
                        <textarea class="form-control" id="quick_category_description" name="description" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Save Category</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
