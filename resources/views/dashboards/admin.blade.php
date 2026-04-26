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

        <!-- Session and Term Management -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-calendar-check fa-3x text-warning mb-3"></i>
                    <h5 class="card-title">Session and Term Management</h5>
                    <p class="card-text">Set active academic session and term</p>
                    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#sessionTermModal">
                        Manage Session & Term
                    </button>
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
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#academicCalendarModal">
                        Manage Calendar
                    </button>
                </div>
            </div>
        </div>

        <!-- Newsletter -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-newspaper fa-3x text-info mb-3"></i>
                    <h5 class="card-title">Newsletter</h5>
                    <p class="card-text">Upload and manage school newsletter</p>
                    <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#newsletterModal">
                        Manage Newsletter
                    </button>
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
                    <button type="button" class="btn btn-pink" data-bs-toggle="modal" data-bs-target="#girlsHairstylesModal">
                        Manage Hairstyles
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Academic Calendar Upload Modal -->
<div class="modal fade" id="academicCalendarModal" tabindex="-1" aria-labelledby="calendarUploadModalLabel" aria-hidden="true">
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
<div class="modal fade" id="girlsHairstylesModal" tabindex="-1" aria-labelledby="hairstyleUploadModalLabel" aria-hidden="true">
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

<!-- Newsletter Upload Modal -->
<div class="modal fade" id="newsletterModal" tabindex="-1" aria-labelledby="newsletterUploadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newsletterUploadModalLabel">
                    <i class="fas fa-newspaper text-info me-2"></i>Upload School Newsletter
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.newsletter.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    @if($currentNewsletter)
                        <div class="alert alert-info">
                            <strong>Current Newsletter:</strong> {{ $currentNewsletter->term ?? 'N/A' }} {{ $currentNewsletter->session ? '(' . $currentNewsletter->session . ')' : '' }}
                        </div>
                        <div class="mb-4 text-center">
                            <i class="fas fa-file-pdf fa-5x text-danger mb-3"></i>
                            <p class="text-muted">PDF Document</p>
                            <a href="{{ asset('storage/' . $currentNewsletter->pdf_path) }}" 
                               target="_blank" 
                               class="btn btn-outline-info btn-sm">
                                <i class="fas fa-eye me-1"></i>View Current PDF
                            </a>
                        </div>
                    @endif
                    
                    <div class="mb-3">
                        <label for="newsletter_pdf" class="form-label">
                            <i class="fas fa-file-pdf me-1"></i>Newsletter PDF
                        </label>
                        <input type="file" 
                               class="form-control @error('newsletter_pdf') is-invalid @enderror" 
                               id="newsletter_pdf" 
                               name="newsletter_pdf" 
                               accept="application/pdf"
                               required>
                        <div class="form-text">Accepted format: PDF only. Max size: 10MB</div>
                        @error('newsletter_pdf')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="newsletter_term" class="form-label">
                                <i class="fas fa-clock me-1"></i>Term (Optional)
                            </label>
                            <input type="text" 
                                   class="form-control @error('term') is-invalid @enderror" 
                                   id="newsletter_term" 
                                   name="term" 
                                   placeholder="e.g., First Term"
                                   value="{{ old('term') }}">
                            @error('term')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="newsletter_session" class="form-label">
                                <i class="fas fa-calendar me-1"></i>Session (Optional)
                            </label>
                            <input type="text" 
                                   class="form-control @error('session') is-invalid @enderror" 
                                   id="newsletter_session" 
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
                        <strong>Note:</strong> Uploading a new newsletter will replace the existing one.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    @if($currentNewsletter)
                        <a href="{{ route('admin.newsletter.delete') }}" class="btn btn-outline-danger me-auto" 
                           onclick="return confirm('Are you sure you want to delete the current newsletter?')">
                            <i class="fas fa-trash me-1"></i>Delete Newsletter
                        </a>
                    @endif
                    <button type="submit" class="btn btn-info">
                        <i class="fas fa-upload me-1"></i>Upload Newsletter
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Session and Term Management Modal -->
    <div class="modal fade" id="sessionTermModal" tabindex="-1" aria-labelledby="sessionTermModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="sessionTermModalLabel">
                        <i class="fas fa-calendar-check text-warning me-2"></i>Session and Term Management
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Current Active Session Display -->
                    @if($activeSession)
                        <div class="alert alert-success mb-4">
                            <strong><i class="fas fa-check-circle me-2"></i>Current Active Session:</strong> 
                            {{ ucfirst($activeSession->term) }} Term, {{ $activeSession->session }}
                        </div>
                    @else
                        <div class="alert alert-warning mb-4">
                            <i class="fas fa-exclamation-triangle me-2"></i>No active session set. Please add and activate a session.
                        </div>
                    @endif

                    <!-- Add New Session Form -->
                    <div class="card mb-4">
                        <div class="card-header bg-warning text-dark">
                            <i class="fas fa-plus-circle me-2"></i>Add New Session
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.admin.session.store') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="session" class="form-label">
                                            <i class="fas fa-calendar me-1"></i>Academic Session
                                        </label>
                                        <input type="text" 
                                               class="form-control @error('session') is-invalid @enderror" 
                                               id="session" 
                                               name="session" 
                                               placeholder="e.g., 2025/2026"
                                               value="{{ old('session') }}"
                                               required>
                                        <div class="form-text">Format: YYYY/YYYY (e.g., 2025/2026)</div>
                                        @error('session')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="term" class="form-label">
                                            <i class="fas fa-clock me-1"></i>Term
                                        </label>
                                        <select class="form-select @error('term') is-invalid @enderror" 
                                                id="term" 
                                                name="term"
                                                required>
                                            <option value="">Select Term</option>
                                            <option value="first" {{ old('term') == 'first' ? 'selected' : '' }}>First Term</option>
                                            <option value="second" {{ old('term') == 'second' ? 'selected' : '' }}>Second Term</option>
                                            <option value="third" {{ old('term') == 'third' ? 'selected' : '' }}>Third Term</option>
                                        </select>
                                        @error('term')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-plus me-1"></i>Add Session
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Existing Sessions List -->
                    <div class="card">
                        <div class="card-header bg-light">
                            <i class="fas fa-list me-2"></i>All Academic Sessions
                        </div>
                        <div class="card-body">
                            @if($allSessions && $allSessions->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Session</th>
                                                <th>Term</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($allSessions as $sess)
                                                <tr>
                                                    <td>{{ $sess->session }}</td>
                                                    <td>{{ ucfirst($sess->term) }} Term</td>
                                                    <td>
                                                        @if($sess->is_active)
                                                            <span class="badge bg-success">
                                                                <i class="fas fa-check me-1"></i>Active
                                                            </span>
                                                        @else
                                                            <span class="badge bg-secondary">Inactive</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if(!$sess->is_active)
                                                            <form action="{{ route('admin.admin.session.set-active', $sess->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="btn btn-sm btn-success" 
                                                                        onclick="return confirm('Set {{ $sess->session }} ({{ ucfirst($sess->term) }}) as active?')">
                                                                    <i class="fas fa-check me-1"></i>Set Active
                                                                </button>
                                                            </form>
                                                        @endif
                                                        @if(!$sess->is_active)
                                                            <form action="{{ route('admin.admin.session.delete', $sess->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                                        onclick="return confirm('Delete this session?')">
                                                                    <i class="fas fa-trash me-1"></i>Delete
                                                                </button>
                                                            </form>
                                                        @endif
                                                        @if($sess->is_active)
                                                            <span class="text-muted small"><i>Cannot modify active session</i></span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted text-center">No sessions added yet.</p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
