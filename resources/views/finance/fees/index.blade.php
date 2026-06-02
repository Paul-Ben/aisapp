@extends('layouts.dashboard')

@section('title', 'Fee Management')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h2><i class="fas fa-receipt me-2"></i>Fee Management</h2>
            <a href="{{ route('finance.fees.create') }}" class="btn btn-dark">
                <i class="fas fa-plus me-1"></i>Add Fee Item
            </a>
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

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Amount</th>
                            <th>Assigned To</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($feeItems as $fee)
                            <tr>
                                <td><strong>{{ $fee->name }}</strong></td>
                                <td>{{ \Illuminate\Support\Str::limit($fee->description, 60) ?? '—' }}</td>
                                <td>₦{{ number_format((float) $fee->amount, 2) }}</td>
                                <td>
                                    @if($fee->classes_count > 0)
                                        <span class="badge bg-info">{{ $fee->classes_count }} Class(es)</span>
                                    @endif
                                    @if($fee->class_categories_count > 0)
                                        <span class="badge bg-secondary">{{ $fee->class_categories_count }} Categor{{ $fee->class_categories_count === 1 ? 'y' : 'ies' }}</span>
                                    @endif
                                    @if($fee->classes_count === 0 && $fee->class_categories_count === 0)
                                        <span class="badge bg-light text-dark">Unassigned</span>
                                    @endif
                                </td>
                                <td>
                                    @if($fee->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('finance.fees.edit', $fee) }}" class="btn btn-outline-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('finance.fees.destroy', $fee) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger"
                                                    title="Delete"
                                                    onclick="return confirm('Are you sure you want to delete this fee item? This action cannot be undone.')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No fee items yet. Add your first fee item to get started.</p>
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
