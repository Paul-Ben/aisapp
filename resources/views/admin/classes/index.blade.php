@extends('layouts.dashboard')

@section('title', 'Class Management')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h2><i class="fas fa-chalkboard-teacher me-2"></i>Class Management</h2>
            <div>
                <button type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#categoryModal">
                    <i class="fas fa-folder-plus me-1"></i>Manage Categories
                </button>
                <a href="{{ route('admin.classes.create') }}" class="btn btn-success">
                    <i class="fas fa-plus me-1"></i>Add New Class
                </a>
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

    <!-- Categories Section -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="fas fa-folder me-2"></i>Class Categories</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                @forelse($categories as $category)
                    <div class="col-md-4">
                        <div class="card h-100 border-success">
                            <div class="card-body">
                                <h6 class="card-title">{{ $category->name }}</h6>
                                <p class="card-text text-muted small">{{ $category->description ?? 'No description' }}</p>
                                <hr>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-info">{{ $category->classes->count() }} Class(es)</span>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-primary edit-category-btn" 
                                                data-id="{{ $category->id }}"
                                                data-name="{{ $category->name }}"
                                                data-description="{{ $category->description }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('admin.class-categories.destroy', $category->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" 
                                                    onclick="return confirm('Are you sure? This will delete the category and all its classes.')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-4">
                        <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No categories found. Create your first category.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Classes Section -->
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-chalkboard me-2"></i>All Classes</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Class Name</th>
                            <th>Category</th>
                            <th>Arm</th>
                            <th>Assigned Staff</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($classes as $class)
                            <tr>
                                <td><strong>{{ $class->name }}</strong></td>
                                <td>
                                    <span class="badge bg-success">{{ $class->category->name ?? 'N/A' }}</span>
                                </td>
                                <td>{{ $class->arm ?? 'N/A' }}</td>
                                <td>
                                    @if($class->staff->count() > 0)
                                        <span class="badge bg-info">{{ $class->staff->count() }} Staff</span>
                                        <button type="button" class="btn btn-sm btn-link" data-bs-toggle="modal" 
                                                data-bs-target="#staffListModal{{ $class->id }}">
                                            View
                                        </button>
                                    @else
                                        <span class="badge bg-secondary">No Staff</span>
                                    @endif
                                </td>
                                <td>
                                    @if($class->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('admin.classes.edit', $class->id) }}" class="btn btn-outline-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-info" title="Assign Staff"
                                                data-bs-toggle="modal" data-bs-target="#assignStaffModal{{ $class->id }}">
                                            <i class="fas fa-user-plus"></i>
                                        </button>
                                        <form action="{{ route('admin.classes.destroy', $class->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" 
                                                    title="Delete"
                                                    onclick="return confirm('Are you sure you want to delete this class?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            <!-- Staff List Modal -->
                            <div class="modal fade" id="staffListModal{{ $class->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Staff in {{ $class->full_name }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <ul class="list-group">
                                                @foreach($class->staff as $staffMember)
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        {{ $staffMember->full_name }}
                                                        <span class="badge bg-primary rounded-pill">{{ $staffMember->pivot->role }}</span>
                                                        <form action="{{ route('admin.classes.remove-staff', [$class->id, $staffMember->id]) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                                    onclick="return confirm('Remove this staff from the class?')">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        </form>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Assign Staff Modal -->
                            <div class="modal fade" id="assignStaffModal{{ $class->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('admin.classes.assign-staff', $class->id) }}" method="POST">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title">Assign Staff to {{ $class->full_name }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label for="staff_ids_{{ $class->id }}" class="form-label">Select Staff</label>
                                                    <select class="form-select" id="staff_ids_{{ $class->id }}" name="staff_ids[]" multiple required>
                                                        @foreach(\App\Models\Staff::where('is_active', true)->get() as $staffMember)
                                                            <option value="{{ $staffMember->id }}">{{ $staffMember->full_name }} ({{ $staffMember->position ?? 'Teacher' }})</option>
                                                        @endforeach
                                                    </select>
                                                    <div class="form-text">Hold Ctrl/Cmd to select multiple staff</div>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="role_{{ $class->id }}" class="form-label">Role</label>
                                                    <input type="text" class="form-control" id="role_{{ $class->id }}" name="role" value="teacher" placeholder="e.g., teacher, HOD">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-info">Assign Staff</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <i class="fas fa-chalkboard fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No classes found. Add your first class.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Category Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="categoryForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="categoryModalTitle">Add Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="category_id" name="_method" value="POST">
                    <div class="mb-3">
                        <label for="category_name" class="form-label">Category Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="category_name" name="name" required 
                               placeholder="e.g., Nursery, Primary, Secondary">
                    </div>
                    <div class="mb-3">
                        <label for="category_description" class="form-label">Description</label>
                        <textarea class="form-control" id="category_description" name="description" rows="2" 
                                  placeholder="Brief description of this category"></textarea>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const categoryModal = new bootstrap.Modal(document.getElementById('categoryModal'));
    const editButtons = document.querySelectorAll('.edit-category-btn');
    const categoryForm = document.getElementById('categoryForm');
    const modalTitle = document.getElementById('categoryModalTitle');
    const categoryIdInput = document.getElementById('category_id');
    const categoryNameInput = document.getElementById('category_name');
    const categoryDescInput = document.getElementById('category_description');

    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name;
            const description = this.dataset.description;

            modalTitle.textContent = 'Edit Category';
            categoryIdInput.value = 'PUT';
            categoryNameInput.value = name;
            categoryDescInput.value = description || '';
            categoryForm.action = `/admin/class-categories/${id}`;
            
            categoryModal.show();
        });
    });

    // Reset form when modal is closed
    document.getElementById('categoryModal').addEventListener('hidden.bs.modal', function() {
        modalTitle.textContent = 'Add Category';
        categoryIdInput.value = 'POST';
        categoryNameInput.value = '';
        categoryDescInput.value = '';
        categoryForm.action = '/admin/class-categories';
    });
});
</script>
@endsection
