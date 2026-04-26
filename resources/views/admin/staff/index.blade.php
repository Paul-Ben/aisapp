@extends('layouts.dashboard')

@section('title', 'Staff Management')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h2><i class="fas fa-users me-2"></i>Staff Management</h2>
            <a href="{{ route('admin.staff.create') }}" class="btn btn-info">
                <i class="fas fa-plus me-1"></i>Add New Staff
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
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

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Staff ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Position</th>
                            <th>Department</th>
                            <th>Classes</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($staff as $member)
                            <tr>
                                <td>{{ $member->staff_id }}</td>
                                <td>{{ $member->full_name }}</td>
                                <td>{{ $member->email }}</td>
                                <td>{{ $member->position ?? 'N/A' }}</td>
                                <td>{{ $member->department ?? 'N/A' }}</td>
                                <td>
                                    @if($member->classes->count() > 0)
                                        <span class="badge bg-info">{{ $member->classes->count() }} Class(es)</span>
                                    @else
                                        <span class="badge bg-secondary">No Classes</span>
                                    @endif
                                </td>
                                <td>
                                    @if($member->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('admin.staff.edit', $member->id) }}" class="btn btn-outline-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.staff.toggle-status', $member->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-{{ $member->is_active ? 'warning' : 'success' }}" 
                                                    title="{{ $member->is_active ? 'Deactivate' : 'Activate' }}"
                                                    onclick="return confirm('Are you sure you want to {{ $member->is_active ? 'deactivate' : 'activate' }} this staff member?')">
                                                <i class="fas fa-{{ $member->is_active ? 'lock' : 'unlock' }}"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.staff.destroy', $member->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" 
                                                    title="Delete"
                                                    onclick="return confirm('Are you sure you want to delete this staff member? This action cannot be undone.')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No staff members found. Add your first staff member.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
