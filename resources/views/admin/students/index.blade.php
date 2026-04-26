@extends('layouts.app')

@section('title', 'Student Management')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0 text-gray-800">Student Management</h1>
                <div>
                    <a href="{{ route('admin.students.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add Student</a>
                    <a href="{{ route('admin.students.upload') }}" class="btn btn-info btn-sm"><i class="fas fa-upload"></i> Bulk Upload</a>
                    <a href="{{ route('admin.students.promote') }}" class="btn btn-success btn-sm"><i class="fas fa-arrow-up"></i> Bulk Promote</a>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">{{ session('success') }}<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button></div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Filters</h6></div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.students.index') }}" class="row">
                <div class="col-md-3 mb-3"><label>Search</label><input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Name, Admission No"></div>
                <div class="col-md-3 mb-3"><label>Class</label><select class="form-control" name="class_id"><option value="">All Classes</option>@foreach($classes as $class)<option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>@endforeach</select></div>
                <div class="col-md-3 mb-3"><label>Status</label><select class="form-control" name="status"><option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active</option><option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Inactive</option><option value="graduated" {{ $status === 'graduated' ? 'selected' : '' }}>Graduated</option><option value="all" {{ $status === 'all' ? 'selected' : '' }}>All</option></select></div>
                <div class="col-md-3 mb-3 d-flex align-items-end"><button type="submit" class="btn btn-primary mr-2">Filter</button><a href="{{ route('admin.students.index') }}" class="btn btn-secondary">Reset</a></div>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Students ({{ $students->total() }})</h6></div>
        <div class="card-body">
            @if($students->count() > 0)
            <form id="bulkActionForm">@csrf
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="thead-light"><tr><th width="5%"><input type="checkbox" id="selectAll"></th><th>Admission No</th><th>Name</th><th>Gender</th><th>Class</th><th>Status</th><th>Actions</th></tr></thead>
                        <tbody>
                            @foreach($students as $student)
                            <tr>
                                <td><input type="checkbox" name="student_ids[]" value="{{ $student->id }}" class="student-checkbox"></td>
                                <td>{{ $student->admission_number }}</td>
                                <td>{{ $student->first_name }} {{ $student->middle_name }} {{ $student->last_name }}</td>
                                <td><span class="badge badge-{{ $student->gender === 'male' ? 'primary' : 'danger' }}">{{ ucfirst($student->gender) }}</span></td>
                                <td>{{ $student->class ? $student->class->name : 'Not Assigned' }}</td>
                                <td><span class="badge badge-{{ $student->status === 'active' ? 'success' : ($student->status === 'graduated' ? 'info' : 'warning') }}">{{ ucfirst($student->status) }}</span></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.students.show', $student) }}" class="btn btn-info"><i class="fas fa-eye"></i></a>
                                        <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-warning"><i class="fas fa-edit"></i></a>
                                        @if($student->isActive())
                                        <button type="button" class="btn btn-success" onclick="showPromoteModal({{ $student->id }}, '{{ $student->full_name }}')"><i class="fas fa-arrow-up"></i></button>
                                        <button type="button" class="btn btn-danger" onclick="showDemoteModal({{ $student->id }}, '{{ $student->full_name }}')"><i class="fas fa-arrow-down"></i></button>
                                        <button type="button" class="btn btn-info" onclick="showGraduateModal({{ $student->id }}, '{{ $student->full_name }}')"><i class="fas fa-graduation-cap"></i></button>
                                        @endif
                                        <form action="{{ route('admin.students.destroy', $student) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete?');">@csrf @method('DELETE')<button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i></button></form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3"><div class="row"><div class="col-md-6"><span id="selectedCount">0 selected</span></div><div class="col-md-6 text-right"><button type="button" class="btn btn-success btn-sm" onclick="showBulkPromoteModal()">Promote Selected</button><button type="button" class="btn btn-warning btn-sm" onclick="showBulkDemoteModal()">Demote Selected</button><button type="button" class="btn btn-info btn-sm" onclick="showBulkGraduateModal()">Graduate Selected</button></div></div></div>
            </form>
            @else
            <div class="text-center py-5"><i class="fas fa-user-graduate fa-4x text-muted mb-3"></i><p>No students found.</p><a href="{{ route('admin.students.create') }}" class="btn btn-primary">Add First Student</a></div>
            @endif
        </div>
        @if($students->hasPages())<div class="card-footer">{{ $students->links() }}</div>@endif
    </div>
</div>

<!-- Modals -->
<div class="modal fade" id="promoteModal"><div class="modal-dialog"><div class="modal-content"><form id="promoteForm" method="POST">@csrf<div class="modal-header"><h5>Promote</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div><div class="modal-body"><p id="promoteStudentName"></p><input type="hidden" id="promoteStudentId" name="student_ids[]"><select class="form-control" name="to_class_id" required>@foreach($classes as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button><button type="submit" class="btn btn-success">Promote</button></div></form></div></div></div>

<div class="modal fade" id="demoteModal"><div class="modal-dialog"><div class="modal-content"><form id="demoteForm" method="POST">@csrf<div class="modal-header"><h5>Demote</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div><div class="modal-body"><p id="demoteStudentName"></p><input type="hidden" id="demoteStudentId" name="student_ids[]"><select class="form-control" name="to_class_id" required>@foreach($classes as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button><button type="submit" class="btn btn-warning">Demote</button></div></form></div></div></div>

<div class="modal fade" id="graduateModal"><div class="modal-dialog"><div class="modal-content"><form id="graduateForm" method="POST">@csrf<div class="modal-header"><h5>Graduate</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div><div class="modal-body"><p id="graduateStudentName"></p><p class="text-warning">Mark as graduated?</p><input type="hidden" id="graduateStudentId" name="student_ids[]"></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button><button type="submit" class="btn btn-info">Graduate</button></div></form></div></div></div>

<div class="modal fade" id="bulkPromoteModal"><div class="modal-dialog"><div class="modal-content"><form action="{{ route('admin.students.bulk-promote') }}" method="POST">@csrf<div class="modal-header"><h5>Bulk Promote</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div><div class="modal-body"><div class="form-group"><label>From Class</label><select class="form-control" name="from_class_id" required>@foreach($classes as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select></div><div class="form-group"><label>To Class</label><select class="form-control" name="to_class_id" required>@foreach($classes as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select></div><div id="bulkPromoteStudentIds"></div></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button><button type="submit" class="btn btn-success">Promote</button></div></form></div></div></div>

<div class="modal fade" id="bulkDemoteModal"><div class="modal-dialog"><div class="modal-content"><form action="{{ route('admin.students.bulk-demote') }}" method="POST">@csrf<div class="modal-header"><h5>Bulk Demote</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div><div class="modal-body"><div class="form-group"><label>From Class</label><select class="form-control" name="from_class_id" required>@foreach($classes as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select></div><div class="form-group"><label>To Class</label><select class="form-control" name="to_class_id" required>@foreach($classes as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select></div><div id="bulkDemoteStudentIds"></div></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button><button type="submit" class="btn btn-warning">Demote</button></div></form></div></div></div>

<div class="modal fade" id="bulkGraduateModal"><div class="modal-dialog"><div class="modal-content"><form action="{{ route('admin.students.bulk-graduate') }}" method="POST">@csrf<div class="modal-header"><h5>Bulk Graduate</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div><div class="modal-body"><p class="text-warning">Confirm graduation?</p><div id="bulkGraduateStudentIds"></div></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button><button type="submit" class="btn btn-info">Graduate</button></div></form></div></div></div>
@endsection

@section('scripts')
<script>
document.getElementById('selectAll').addEventListener('change', function() { document.querySelectorAll('.student-checkbox').forEach(cb => cb.checked = this.checked); updateSelectedCount(); });
document.querySelectorAll('.student-checkbox').forEach(cb => cb.addEventListener('change', updateSelectedCount));
function updateSelectedCount() { document.getElementById('selectedCount').textContent = document.querySelectorAll('.student-checkbox:checked').length + ' selected'; }
function getSelectedStudentIds() { const s=[]; document.querySelectorAll('.student-checkbox:checked').forEach(cb => s.push(cb.value)); return s; }
function showPromoteModal(id, name) { document.getElementById('promoteStudentId').value=id; document.getElementById('promoteStudentName').textContent=name; document.getElementById('promoteForm').action="{{ route('admin.students.bulk-promote') }}"; $('#promoteModal').modal('show'); }
function showDemoteModal(id, name) { document.getElementById('demoteStudentId').value=id; document.getElementById('demoteStudentName').textContent=name; document.getElementById('demoteForm').action="{{ route('admin.students.bulk-demote') }}"; $('#demoteModal').modal('show'); }
function showGraduateModal(id, name) { document.getElementById('graduateStudentId').value=id; document.getElementById('graduateStudentName').textContent=name; document.getElementById('graduateForm').action="{{ route('admin.students.bulk-graduate') }}"; $('#graduateModal').modal('show'); }
function showBulkPromoteModal() { const s=getSelectedStudentIds(); if(!s.length){alert('Select students');return;} document.getElementById('bulkPromoteStudentIds').innerHTML=s.map(i=>'<input type="hidden" name="student_ids[]" value="'+i+'">').join(''); $('#bulkPromoteModal').modal('show'); }
function showBulkDemoteModal() { const s=getSelectedStudentIds(); if(!s.length){alert('Select students');return;} document.getElementById('bulkDemoteStudentIds').innerHTML=s.map(i=>'<input type="hidden" name="student_ids[]" value="'+i+'">').join(''); $('#bulkDemoteModal').modal('show'); }
function showBulkGraduateModal() { const s=getSelectedStudentIds(); if(!s.length){alert('Select students');return;} document.getElementById('bulkGraduateStudentIds').innerHTML=s.map(i=>'<input type="hidden" name="student_ids[]" value="'+i+'">').join(''); $('#bulkGraduateModal').modal('show'); }
</script>
@endsection
