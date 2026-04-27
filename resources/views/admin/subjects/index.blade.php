@extends('layouts.dashboard')

@section('title', 'Subject Management')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h2 class="card-title"><i class="fas fa-book me-2"></i>Subject Management</h2>
                    <p class="card-text">Manage subjects offered by each class</p>
                </div>
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

    <div class="row">
        <!-- Subjects List -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>All Subjects</h5>
                    <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#addSubjectModal">
                        <i class="fas fa-plus"></i> Add Subject
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Name</th>
                                    <th>Classes</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($subjects as $subject)
                                    <tr>
                                        <td><strong>{{ $subject->code }}</strong></td>
                                        <td>{{ $subject->name }}</td>
                                        <td>
                                            <span class="badge bg-info">{{ $subject->classes_count }} class(es)</span>
                                        </td>
                                        <td>
                                            <button type="button" 
                                                    class="btn btn-sm btn-primary" 
                                                    onclick="editSubject({{ $subject->id }}, '{{ $subject->name }}', '{{ $subject->code }}', '{{ $subject->description }}')">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="{{ route('admin.subjects.destroy', $subject->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this subject?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">No subjects found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assign Subjects to Classes -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chalkboard-teacher me-2"></i>Assign Subjects to Class</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.subjects.assign-to-class', ':classId') }}" method="POST" id="assignSubjectsForm">
                        @csrf
                        <input type="hidden" name="class_id" id="selected_class_id">
                        
                        <div class="mb-3">
                            <label for="class_select" class="form-label">Select Class</label>
                            <select class="form-select" id="class_select" required onchange="loadClassSubjects(this.value)">
                                <option value="">-- Select a Class --</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}">{{ $class->full_name }} ({{ $class->category->name ?? 'N/A' }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div id="subjects_assignment_area" style="display: none;">
                            <div class="mb-3">
                                <label class="form-label">Available Subjects</label>
                                <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                                    @foreach($subjects as $subject)
                                        <div class="form-check">
                                            <input class="form-check-input subject-checkbox" 
                                                   type="checkbox" 
                                                   name="subjects[]" 
                                                   value="{{ $subject->id }}" 
                                                   id="subject_{{ $subject->id }}">
                                            <label class="form-check-label" for="subject_{{ $subject->id }}">
                                                {{ $subject->code }} - {{ $subject->name }}
                                            </label>
                                            <div class="ms-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" 
                                                           type="checkbox" 
                                                           name="is_compulsory[]" 
                                                           value="{{ $subject->id }}" 
                                                           id="compulsory_{{ $subject->id }}">
                                                    <label class="form-check-label text-muted small" for="compulsory_{{ $subject->id }}">
                                                        Mark as Compulsory
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-save me-1"></i> Assign Subjects to Class
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Current Class Subjects -->
    <div class="row mt-4" id="current_subjects_row" style="display: none;">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-check-circle me-2"></i>Current Subjects for <span id="current_class_name"></span></h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="current_subjects_body">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Subject Modal -->
<div class="modal fade" id="addSubjectModal" tabindex="-1" aria-labelledby="addSubjectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSubjectModalLabel">
                    <i class="fas fa-plus me-2"></i>Add New Subject
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.subjects.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="subject_name" class="form-label">Subject Name</label>
                        <input type="text" class="form-control" id="subject_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="subject_code" class="form-label">Subject Code</label>
                        <input type="text" class="form-control" id="subject_code" name="code" required placeholder="e.g., MTH101">
                    </div>
                    <div class="mb-3">
                        <label for="subject_description" class="form-label">Description (Optional)</label>
                        <textarea class="form-control" id="subject_description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Add Subject</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Subject Modal -->
<div class="modal fade" id="editSubjectModal" tabindex="-1" aria-labelledby="editSubjectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editSubjectModalLabel">
                    <i class="fas fa-edit me-2"></i>Edit Subject
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editSubjectForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_subject_name" class="form-label">Subject Name</label>
                        <input type="text" class="form-control" id="edit_subject_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_subject_code" class="form-label">Subject Code</label>
                        <input type="text" class="form-control" id="edit_subject_code" name="code" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_subject_description" class="form-label">Description (Optional)</label>
                        <textarea class="form-control" id="edit_subject_description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Subject</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editSubject(id, name, code, description) {
    document.getElementById('editSubjectForm').action = '/admin/subjects/' + id;
    document.getElementById('edit_subject_name').value = name;
    document.getElementById('edit_subject_code').value = code;
    document.getElementById('edit_subject_description').value = description || '';
    
    const modal = new bootstrap.Modal(document.getElementById('editSubjectModal'));
    modal.show();
}

function loadClassSubjects(classId) {
    if (!classId) {
        document.getElementById('subjects_assignment_area').style.display = 'none';
        document.getElementById('current_subjects_row').style.display = 'none';
        return;
    }

    // Update form action
    document.getElementById('assignSubjectsForm').action = document.getElementById('assignSubjectsForm').action.replace(':classId', classId);
    document.getElementById('selected_class_id').value = classId;
    document.getElementById('subjects_assignment_area').style.display = 'block';

    // Load current subjects for the class
    fetch('/admin/subjects/class/' + classId)
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('current_subjects_body');
            tbody.innerHTML = '';

            if (data.subjects && data.subjects.length > 0) {
                data.subjects.forEach(subject => {
                    const row = `
                        <tr>
                            <td><strong>${subject.code}</strong></td>
                            <td>${subject.name}</td>
                            <td>
                                ${subject.pivot.is_compulsory 
                                    ? '<span class="badge bg-success">Compulsory</span>' 
                                    : '<span class="badge bg-warning">Elective</span>'}
                            </td>
                            <td>
                                <form action="/admin/subjects/${classId}/remove/${subject.id}" method="POST" class="d-inline" onsubmit="return confirm('Remove this subject from the class?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-times"></i> Remove
                                    </button>
                                </form>
                            </td>
                        </tr>
                    `;
                    tbody.innerHTML += row;
                });

                document.getElementById('current_class_name').textContent = data.subjects[0]?.pivot?.class_name || 'Selected Class';
                document.getElementById('current_subjects_row').style.display = 'block';
            } else {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">No subjects assigned to this class yet.</td></tr>';
                document.getElementById('current_subjects_row').style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Error loading subjects:', error);
        });
}
</script>
@endsection
