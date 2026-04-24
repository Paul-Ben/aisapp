@extends('layouts.dashboard')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h2 class="card-title"><i class="fas fa-user-shield me-2"></i>Admin Dashboard</h2>
                    <p class="card-text">Welcome, {{ auth()->user()->name }}! Manage daily operations and staff.</p>
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
    
    <div class="row g-4">
        <!-- Staff Management -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-users fa-3x text-info mb-3"></i>
                    <h5 class="card-title">Staff Management</h5>
                    <p class="card-text">Manage teaching and non-teaching staff</p>
                    <a href="#" class="btn btn-info">Manage Staff</a>
                </div>
            </div>
        </div>

        <!-- Student Management -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-user-graduate fa-3x text-primary mb-3"></i>
                    <h5 class="card-title">Student Management</h5>
                    <p class="card-text">Manage student records and admissions</p>
                    <a href="#" class="btn btn-primary">Students</a>
                </div>
            </div>
        </div>

        <!-- Class Management -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-chalkboard-teacher fa-3x text-success mb-3"></i>
                    <h5 class="card-title">Class Management</h5>
                    <p class="card-text">Organize classes and timetables</p>
                    <a href="#" class="btn btn-success">Classes</a>
                </div>
            </div>
        </div>

        <!-- Attendance Tracking -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-clipboard-check fa-3x text-warning mb-3"></i>
                    <h5 class="card-title">Attendance Tracking</h5>
                    <p class="card-text">Monitor staff and student attendance</p>
                    <a href="#" class="btn btn-warning">Attendance</a>
                </div>
            </div>
        </div>

        <!-- Academic Calendar -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-calendar-alt fa-3x text-danger mb-3"></i>
                    <h5 class="card-title">Academic Calendar</h5>
                    <p class="card-text">Upload and manage school calendar</p>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#calendarUploadModal">
                        Manage Calendar
                    </button>
                </div>
            </div>
        </div>

        <!-- Announcements -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-bullhorn fa-3x text-secondary mb-3"></i>
                    <h5 class="card-title">Announcements</h5>
                    <p class="card-text">Post and manage announcements</p>
                    <a href="#" class="btn btn-secondary">Announcements</a>
                </div>
            </div>
        </div>

        <!-- Girls Hairstyles -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-cut fa-3x text-pink mb-3"></i>
                    <h5 class="card-title">Girls Hairstyles</h5>
                    <p class="card-text">Upload hairstyle guide for girls</p>
                    <button type="button" class="btn btn-pink" data-bs-toggle="modal" data-bs-target="#hairstyleUploadModal">
                        Manage Hairstyles
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Academic Calendar Upload Modal -->
<div class="modal fade" id="calendarUploadModal" tabindex="-1" aria-labelledby="calendarUploadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="calendarUploadModalLabel">
                    <i class="fas fa-calendar-alt text-danger me-2"></i>Upload Academic Calendar
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.calendar.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    @if($currentCalendar ?? null)
                        <div class="alert alert-info">
                            <strong>Current Calendar:</strong> {{ $currentCalendar->term ?? 'N/A' }} {{ $currentCalendar->session ? '(' . $currentCalendar->session . ')' : '' }}
                        </div>
                        <div class="mb-4 text-center">
                            <img src="{{ asset('storage/' . $currentCalendar->image_path) }}" 
                                 alt="Current Calendar" 
                                 class="img-fluid rounded" 
                                 style="max-height: 400px;">
                        </div>
                    @endif
                    
                    <div class="mb-3">
                        <label for="calendar_image" class="form-label">
                            <i class="fas fa-image me-1"></i>Calendar Image
                        </label>
                        <input type="file" 
                               class="form-control @error('calendar_image') is-invalid @enderror" 
                               id="calendar_image" 
                               name="calendar_image" 
                               accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                               required>
                        <div class="form-text">Accepted formats: JPEG, PNG, JPG, GIF, WEBP. Max size: 5MB</div>
                        @error('calendar_image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="term" class="form-label">
                                <i class="fas fa-clock me-1"></i>Term (Optional)
                            </label>
                            <input type="text" 
                                   class="form-control @error('term') is-invalid @enderror" 
                                   id="term" 
                                   name="term" 
                                   placeholder="e.g., First Term"
                                   value="{{ old('term') }}">
                            @error('term')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="session" class="form-label">
                                <i class="fas fa-calendar me-1"></i>Session (Optional)
                            </label>
                            <input type="text" 
                                   class="form-control @error('session') is-invalid @enderror" 
                                   id="session" 
                                   name="session" 
                                   placeholder="e.g., 2024/2025"
                                   value="{{ old('session') }}">
                            @error('session')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        <strong>Note:</strong> Uploading a new calendar will replace the existing one.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    @if($currentCalendar ?? null)
                        <a href="{{ route('admin.calendar.delete') }}" class="btn btn-outline-danger me-auto" 
                           onclick="return confirm('Are you sure you want to delete the current calendar?')">
                            <i class="fas fa-trash me-1"></i>Delete Calendar
                        </a>
                    @endif
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-upload me-1"></i>Upload Calendar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Girls Hairstyles Upload Modal -->
<div class="modal fade" id="hairstyleUploadModal" tabindex="-1" aria-labelledby="hairstyleUploadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="hairstyleUploadModalLabel">
                    <i class="fas fa-cut text-pink me-2"></i>Upload Girls Hairstyles Guide
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.hairstyles.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    @if($currentHairstyle)
                        <div class="alert alert-info">
                            <strong>Current Guide:</strong> {{ $currentHairstyle->term ?? 'N/A' }} {{ $currentHairstyle->session ? '(' . $currentHairstyle->session . ')' : '' }}
                        </div>
                        <div class="mb-4 text-center">
                            <img src="{{ asset('storage/' . $currentHairstyle->image_path) }}" 
                                 alt="Current Hairstyles" 
                                 class="img-fluid rounded" 
                                 style="max-height: 400px;">
                        </div>
                    @endif
                    
                    <div class="mb-3">
                        <label for="hairstyle_image" class="form-label">
                            <i class="fas fa-image me-1"></i>Hairstyle Image
                        </label>
                        <input type="file" 
                               class="form-control @error('hairstyle_image') is-invalid @enderror" 
                               id="hairstyle_image" 
                               name="hairstyle_image" 
                               accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                               required>
                        <div class="form-text">Accepted formats: JPEG, PNG, JPG, GIF, WEBP. Max size: 5MB</div>
                        @error('hairstyle_image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="hairstyle_term" class="form-label">
                                <i class="fas fa-clock me-1"></i>Term (Optional)
                            </label>
                            <input type="text" 
                                   class="form-control @error('term') is-invalid @enderror" 
                                   id="hairstyle_term" 
                                   name="term" 
                                   placeholder="e.g., First Term"
                                   value="{{ old('term') }}">
                            @error('term')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="hairstyle_session" class="form-label">
                                <i class="fas fa-calendar me-1"></i>Session (Optional)
                            </label>
                            <input type="text" 
                                   class="form-control @error('session') is-invalid @enderror" 
                                   id="hairstyle_session" 
                                   name="session" 
                                   placeholder="e.g., 2024/2025"
                                   value="{{ old('session') }}">
                            @error('session')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        <strong>Note:</strong> Uploading a new hairstyle guide will replace the existing one.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    @if($currentHairstyle)
                        <a href="{{ route('admin.hairstyles.delete') }}" class="btn btn-outline-danger me-auto" 
                           onclick="return confirm('Are you sure you want to delete the current hairstyle guide?')">
                            <i class="fas fa-trash me-1"></i>Delete Guide
                        </a>
                    @endif
                    <button type="submit" class="btn btn-pink">
                        <i class="fas fa-upload me-1"></i>Upload Hairstyles
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
