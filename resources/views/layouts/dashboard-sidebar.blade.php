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
        
        <div class="nav-item">
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-th-large"></i>
                <span>Dashboard</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="#academicCalendarModal" class="nav-link" data-bs-toggle="modal">
                <i class="fas fa-calendar-alt"></i>
                <span>Academic Calendar</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="#girlsHairstylesModal" class="nav-link" data-bs-toggle="modal">
                <i class="fas fa-cut"></i>
                <span>Girls Hairstyles</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="#newsletterModal" class="nav-link" data-bs-toggle="modal">
                <i class="fas fa-newspaper"></i>
                <span>Newsletter</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="{{ url('/') }}" class="nav-link" target="_blank">
                <i class="fas fa-external-link-alt"></i>
                <span>View Website</span>
            </a>
        </div>
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
