<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <img src="{{ asset('assets/images/logo.jpg') }}" alt="AIS Logo">
        <div class="brand-text">
            Alven International
            <span>Schools Dashboard</span>
        </div>
    </div>
    
    <div class="sidebar-toggle" id="sidebarToggle" title="Toggle Sidebar">
        <i class="fas fa-chevron-left"></i>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section-title">Main Menu</div>
        
        @if(auth()->user()->isSuperAdmin())
        <!-- Super Admin Navigation -->
        <div class="nav-item">
            <a href="{{ route('superadmin.dashboard') }}" class="nav-link {{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-th-large"></i>
                <span>Dashboard</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="{{ route('superadmin.users.index') }}" class="nav-link {{ request()->routeIs('superadmin.users.*') ? 'active' : '' }}">
                <i class="fas fa-users-cog"></i>
                <span>User Management</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-cogs"></i>
                <span>System Settings</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-history"></i>
                <span>Audit Logs</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="{{ url('/') }}" class="nav-link" target="_blank">
                <i class="fas fa-external-link-alt"></i>
                <span>View Website</span>
            </a>
        </div>
        
        @elseif(auth()->user()->isAdmin())
        <!-- Admin Navigation -->
        <div class="nav-item">
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-th-large"></i>
                <span>Dashboard</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="{{ route('admin.classes.index') }}" class="nav-link">
                <i class="fas fa-chalkboard-teacher"></i>
                <span>Manage Classes</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="{{ route('admin.staff.index') }}" class="nav-link">
                <i class="fas fa-users"></i>
                <span>Manage Staff</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="{{ route('admin.students.index') }}" class="nav-link {{ request()->routeIs('admin.students.index') ? 'active' : '' }}">
                <i class="fas fa-user-graduate"></i>
                <span>Manage Students</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="{{ route('admin.students.graduates') }}" class="nav-link {{ request()->routeIs('admin.students.graduates') ? 'active' : '' }}">
                <i class="fas fa-graduation-cap"></i>
                <span>Graduates</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="{{ url('/') }}" class="nav-link" target="_blank">
                <i class="fas fa-external-link-alt"></i>
                <span>View Website</span>
            </a>
        </div>
        
        @else
        <!-- Other Roles Navigation -->
        <div class="nav-item">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-th-large"></i>
                <span>Dashboard</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="{{ url('/') }}" class="nav-link" target="_blank">
                <i class="fas fa-external-link-alt"></i>
                <span>View Website</span>
            </a>
        </div>
        @endif
    </nav>

    <div class="sidebar-footer">
        <form method="POST" action="{{ route('logout') }}" id="logout-form">
            @csrf
            <button type="submit" class="logout-btn mt-3" onclick="event.preventDefault(); if(confirm('Are you sure you want to logout?')) document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </button>
        </form>
    </div>
</aside>
