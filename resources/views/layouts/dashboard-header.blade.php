<header class="top-header">
    <div class="header-left">
        <button class="mobile-toggle" id="mobileToggle">
            <i class="fas fa-bars"></i>
        </button>
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" placeholder="Search anything...">
        </div>
    </div>
    
    <div class="header-right">
        <button class="header-icon-btn" title="Toggle Theme">
            <i class="fas fa-moon"></i>
        </button>
        <button class="header-icon-btn" title="Notifications">
            <i class="fas fa-bell"></i>
            <span class="notification-dot"></span>
        </button>
        
        <a href="{{ route('profile.edit') }}" class="user-profile">
            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=003399&color=fff&size=36" alt="{{ Auth::user()->name }}">
            <div class="user-info">
                <div class="name">{{ Auth::user()->name }}</div>
                <div class="role">{{ Auth::user()->email }}</div>
            </div>
        </a>
    </div>
</header>
