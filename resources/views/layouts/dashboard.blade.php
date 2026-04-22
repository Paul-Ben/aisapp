<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'AIS Dashboard - Alven International Schools')</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Open+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* =========================================
           AIS Design System - CSS Variables
           ========================================= */
        :root {
            --color-primary: #003399;
            --color-primary-dark: #001F4D;
            --color-primary-light: #E8EEF7;
            --color-white: #FFFFFF;
            
            --color-accent-blue: #0093D0;
            --color-accent-magenta: #E9007B;
            --color-accent-yellow: #F7941D;
            --color-accent-green: #10B981;
            --color-accent-red: #EF4444;
            
            --color-bg-light: #F4F7F6;
            --color-bg-main: #F5F8FA;
            --color-text-body: #4A5568;
            --color-text-heading: #1A202C;
            --color-text-muted: #A0AEC0;
            --color-border: #E2E8F0;

            --font-heading: 'Poppins', sans-serif;
            --font-body: 'Open Sans', sans-serif;
            
            --sidebar-width: 260px;
            --sidebar-collapsed-width: 70px;
            --header-height: 70px;
            
            --shadow-card: 0 1px 3px rgba(0,0,0,0.05), 0 1px 2px rgba(0,0,0,0.03);
            --shadow-card-hover: 0 10px 15px rgba(0,0,0,0.08), 0 4px 6px rgba(0,0,0,0.03);
            --shadow-sm: 0 1px 2px rgba(0,0,0,0.04);
            
            --radius-sm: 6px;
            --radius-md: 12px;
            --radius-lg: 16px;
            --radius-xl: 20px;
            --radius-pill: 50px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-body);
            color: var(--color-text-body);
            background-color: var(--color-bg-main);
            line-height: 1.6;
            overflow-x: hidden;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: var(--font-heading);
            color: var(--color-text-heading);
            font-weight: 600;
        }

        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--color-white);
            border-right: 1px solid var(--color-border);
            z-index: 1000;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .sidebar::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: var(--color-border);
            border-radius: 4px;
        }

        .sidebar-brand {
            padding: 1.25rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid var(--color-border);
            min-height: var(--header-height);
            text-decoration: none;
        }

        .sidebar-brand img {
            width: 40px;
            height: 40px;
            border-radius: var(--radius-sm);
        }

        .sidebar-brand .brand-text {
            font-family: var(--font-heading);
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--color-primary);
            white-space: nowrap;
        }

        .sidebar-brand .brand-text span {
            display: block;
            font-size: 0.7rem;
            font-weight: 500;
            color: var(--color-text-muted);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .sidebar-toggle {
            position: absolute;
            right: -14px;
            top: 22px;
            width: 28px;
            height: 28px;
            background: var(--color-white);
            border: 1px solid var(--color-border);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 1001;
            transition: all 0.3s ease;
            color: var(--color-text-muted);
            font-size: 0.7rem;
        }

        .sidebar-toggle:hover {
            background: var(--color-primary);
            color: var(--color-white);
            border-color: var(--color-primary);
        }

        .sidebar-nav {
            padding: 1rem 0;
            flex: 1;
        }

        .nav-section-title {
            padding: 0.75rem 1.5rem 0.5rem;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--color-text-muted);
        }

        .nav-item {
            padding: 0 0.75rem;
            margin-bottom: 2px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0.75rem 1rem;
            color: var(--color-text-body);
            text-decoration: none;
            border-radius: var(--radius-sm);
            transition: all 0.2s ease;
            font-size: 0.9rem;
            font-weight: 500;
            white-space: nowrap;
        }

        .nav-link:hover {
            background: var(--color-primary-light);
            color: var(--color-primary);
        }

        .nav-link.active {
            background: var(--color-primary-light);
            color: var(--color-primary);
            font-weight: 600;
        }

        .nav-link i {
            width: 20px;
            text-align: center;
            font-size: 1rem;
            flex-shrink: 0;
        }

        .nav-link .badge {
            margin-left: auto;
            background: var(--color-accent-green);
            color: white;
            font-size: 0.7rem;
            padding: 2px 8px;
            border-radius: var(--radius-pill);
            font-weight: 600;
        }

        .logout-btn {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0.75rem 1.5rem;
            color: var(--color-accent-red);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
        }

        .logout-btn:hover {
            background: rgba(239, 68, 68, 0.08);
        }

        /* Main Content Area */
        .main-content {
            margin-left: var(--sidebar-width);
            transition: all 0.3s ease;
            min-height: 100vh;
        }

        .main-content.expanded {
            margin-left: var(--sidebar-collapsed-width);
        }

        /* Header */
        .top-header {
            background: var(--color-white);
            height: var(--header-height);
            border-bottom: 1px solid var(--color-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .mobile-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.25rem;
            color: var(--color-text-heading);
            cursor: pointer;
        }

        .search-box {
            position: relative;
            width: 300px;
        }

        .search-box input {
            width: 100%;
            padding: 0.6rem 1rem 0.6rem 2.5rem;
            border: 1px solid var(--color-border);
            border-radius: var(--radius-pill);
            background: var(--color-bg-main);
            font-size: 0.9rem;
            font-family: var(--font-body);
            color: var(--color-text-body);
            transition: all 0.2s ease;
        }

        .search-box input:focus {
            outline: none;
            border-color: var(--color-primary);
            box-shadow: 0 0 0 3px rgba(0, 51, 153, 0.1);
            background: var(--color-white);
        }

        .search-box i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--color-text-muted);
            font-size: 0.85rem;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .header-icon-btn {
            width: 40px;
            height: 40px;
            border-radius: var(--radius-sm);
            border: none;
            background: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            color: var(--color-text-body);
            position: relative;
            font-size: 1.1rem;
        }

        .header-icon-btn:hover {
            background: var(--color-bg-main);
            color: var(--color-primary);
        }

        .header-icon-btn .notification-dot {
            position: absolute;
            top: 8px;
            right: 8px;
            width: 8px;
            height: 8px;
            background: var(--color-accent-red);
            border-radius: 50%;
            border: 2px solid var(--color-white);
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0.4rem 0.75rem;
            border-radius: var(--radius-sm);
            cursor: pointer;
            transition: all 0.2s ease;
            margin-left: 0.5rem;
            text-decoration: none;
        }

        .user-profile:hover {
            background: var(--color-bg-main);
        }

        .user-profile img {
            width: 36px;
            height: 36px;
            border-radius: var(--radius-sm);
            object-fit: cover;
        }

        .user-info {
            line-height: 1.2;
        }

        .user-info .name {
            font-family: var(--font-heading);
            font-weight: 600;
            font-size: 0.85rem;
            color: var(--color-text-heading);
        }

        .user-info .role {
            font-size: 0.75rem;
            color: var(--color-text-muted);
        }

        /* Dashboard Content */
        .dashboard-content {
            padding: 1.5rem;
        }

        /* Sidebar Collapsed State */
        .sidebar.collapsed {
            width: var(--sidebar-collapsed-width);
        }

        .sidebar.collapsed .brand-text,
        .sidebar.collapsed .nav-section-title,
        .sidebar.collapsed .nav-link span,
        .sidebar.collapsed .nav-link .badge,
        .sidebar.collapsed .sidebar-upgrade-card,
        .sidebar.collapsed .logout-btn span {
            display: none;
        }

        .sidebar.collapsed .nav-link {
            justify-content: center;
            padding: 0.75rem;
        }

        .sidebar.collapsed .nav-link i {
            margin: 0;
        }

        .sidebar.collapsed .sidebar-toggle {
            transform: rotate(180deg);
        }

        /* Mobile Responsive */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 999;
        }

        @media (max-width: 991px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.mobile-open {
                transform: translateX(0);
            }

            .sidebar-overlay.active {
                display: block;
            }

            .main-content {
                margin-left: 0;
            }

            .main-content.expanded {
                margin-left: 0;
            }

            .mobile-toggle {
                display: block;
            }

            .search-box {
                display: none;
            }
        }

        /* Copy additional component styles from dash.html as needed here or in a separate file */
        @yield('styles')
    </style>
</head>
<body>

    <!-- Sidebar Overlay (Mobile) -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    @include('layouts.dashboard-sidebar')

    <!-- Main Content -->
    <main class="main-content" id="mainContent">
        @include('layouts.dashboard-header')

        <!-- Dashboard Content -->
        <div class="dashboard-content">
            @yield('content')
        </div>
    </main>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
        // Sidebar Toggle
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const mobileToggle = document.getElementById('mobileToggle');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', () => {
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');
            });
        }

        if (mobileToggle) {
            mobileToggle.addEventListener('click', () => {
                sidebar.classList.toggle('mobile-open');
                sidebarOverlay.classList.toggle('active');
            });
        }

        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', () => {
                sidebar.classList.remove('mobile-open');
                sidebarOverlay.classList.remove('active');
            });
        }
    </script>
    @yield('scripts')
</body>
</html>
