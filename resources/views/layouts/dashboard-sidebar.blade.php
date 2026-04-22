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
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-th-large"></i>
                <span>Dashboard</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-file-alt"></i>
                <span>Orders</span>
                <span class="badge">46</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-box"></i>
                <span>Products</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-users"></i>
                <span>Students</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-newspaper"></i>
                <span>Content</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="{{ url('/') }}" class="nav-link">
                <i class="fas fa-store"></i>
                <span>Online Portal</span>
            </a>
        </div>

        <div class="nav-section-title mt-3">Finance</div>
        
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-wallet"></i>
                <span>Finances</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-file-invoice-dollar"></i>
                <span>Invoices</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-exchange-alt"></i>
                <span>Transactions</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-chart-pie"></i>
                <span>Reports</span>
            </a>
        </div>

        <div class="nav-section-title mt-3">Analytics</div>
        
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-chart-line"></i>
                <span>Analytics</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-tags"></i>
                <span>Discounts</span>
            </a>
        </div>
    </nav>

    <div class="sidebar-footer">
        <div class="sidebar-upgrade-card">
            <h5>Upgrade to Premium!</h5>
            <p>Unlock all features and advanced analytics for your school.</p>
            <button class="btn-upgrade">Upgrade Premium</button>
        </div>
        
        <form method="POST" action="{{ route('logout') }}" id="logout-form">
            @csrf
            <button type="submit" class="logout-btn mt-3" onclick="event.preventDefault(); if(confirm('Are you sure you want to logout?')) document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </button>
        </form>
    </div>
</aside>
