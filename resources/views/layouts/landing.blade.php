<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Alven International Schools - Building The Future')</title>
    
    {{-- favicon --}}
    <link rel="icon" type="image/png" sizes="32x32" href="{{asset('assets/images/logo.jpg')}}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{asset('assets/images/logo.jpg')}}">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* =========================================
           AIS Design System - CSS Variables
           ========================================= */
        :root {
            /* Brand Colors */
            --color-primary: #003399;
            --color-primary-dark: #001F4D;
            --color-white: #FFFFFF;
            
            /* Accent Colors */
            --color-accent-blue: #0093D0;
            --color-accent-magenta: #E9007B;
            --color-accent-yellow: #F7941D;
            
            /* Neutrals */
            --color-bg-light: #F4F7F6;
            --color-text-body: #4A4A4A;
            --color-text-heading: #001F4D;
            --color-border: #E0E0E0;

            /* Typography */
            --font-heading: 'Poppins', sans-serif;
            --font-body: 'Open Sans', sans-serif;
            
            /* Spacing */
            --spacing-xs: 0.5rem;
            --spacing-sm: 1rem;
            --spacing-md: 2rem;
            --spacing-lg: 4rem;
            
            /* Border Radius */
            --border-radius-sm: 8px;
            --border-radius-md: 12px;
            --border-radius-pill: 50px;
            
            /* Shadows */
            --shadow-card: 0 4px 15px rgba(0, 51, 153, 0.1);
            --shadow-hover: 0 8px 25px rgba(0, 51, 153, 0.15);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-body);
            color: var(--color-text-body);
            background-color: var(--color-white);
            line-height: 1.6;
            overflow-x: hidden;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: var(--font-heading);
            color: var(--color-text-heading);
            font-weight: 700;
        }

        /* Navigation Styles */
        .navbar {
            background-color: var(--color-white);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 1rem 0;
            transition: all 0.3s ease;
        }

        .navbar.scrolled {
            padding: 0.5rem 0;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }

        .navbar-brand {
            font-family: var(--font-heading);
            font-weight: 700;
            color: var(--color-primary) !important;
            font-size: 1.5rem;
        }

        .navbar-brand img {
            height: 50px;
            margin-right: 10px;
        }

        .nav-link {
            font-family: var(--font-heading);
            font-weight: 600;
            color: var(--color-text-heading) !important;
            margin: 0 0.5rem;
            transition: color 0.3s ease;
        }

        .nav-link:hover {
            color: var(--color-primary) !important;
        }

        .dropdown-menu {
            border: none;
            box-shadow: var(--shadow-card);
            border-radius: var(--border-radius-md);
            margin-top: 0.5rem;
        }

        .dropdown-item {
            font-family: var(--font-body);
            padding: 0.75rem 1.5rem;
            transition: all 0.3s ease;
        }

        .dropdown-item:hover {
            background-color: var(--color-bg-light);
            color: var(--color-primary);
        }

        .btn-primary-custom {
            background-color: var(--color-primary);
            color: var(--color-white);
            padding: 0.75rem 2rem;
            border-radius: var(--border-radius-pill);
            font-family: var(--font-heading);
            font-weight: 600;
            border: none;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary-custom:hover {
            background-color: var(--color-primary-dark);
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
            color: var(--color-white);
        }

        .btn-accent {
            background-color: var(--color-accent-magenta);
            color: var(--color-white);
            padding: 0.75rem 2rem;
            border-radius: var(--border-radius-pill);
            font-family: var(--font-heading);
            font-weight: 600;
            border: none;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-accent:hover {
            background-color: #c40068;
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
            color: var(--color-white);
        }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 100%);
            min-height: 90vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
            padding-top: 80px;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 800px;
            height: 800px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
        }

        .hero-content h1 {
            color: var(--color-white);
            font-size: 3.5rem;
            margin-bottom: 1.5rem;
            line-height: 1.2;
        }

        .hero-content p {
            color: rgba(255,255,255,0.9);
            font-size: 1.25rem;
            margin-bottom: 2rem;
        }

        .hero-tagline {
            color: var(--color-accent-yellow);
            font-family: var(--font-heading);
            font-weight: 600;
            font-size: 1.5rem;
            margin-bottom: 1rem;
            display: block;
        }

        .hero-image {
            position: relative;
            z-index: 1;
        }

        .hero-image img {
            border-radius: var(--border-radius-md);
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }

        /* Section Styling */
        .section-padding {
            padding: var(--spacing-lg) 0;
        }

        .section-title {
            text-align: center;
            margin-bottom: var(--spacing-md);
        }

        .section-title h2 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            position: relative;
            display: inline-block;
        }

        .section-title h2::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: linear-gradient(90deg, var(--color-accent-blue), var(--color-accent-magenta), var(--color-accent-yellow));
            border-radius: 2px;
        }

        .section-title p {
            color: var(--color-text-body);
            font-size: 1.1rem;
            max-width: 600px;
            margin: 1.5rem auto 0;
        }

        /* Cards */
        .feature-card {
            background: var(--color-white);
            border-radius: var(--border-radius-md);
            padding: var(--spacing-md);
            box-shadow: var(--shadow-card);
            transition: all 0.3s ease;
            height: 100%;
            border-top: 4px solid var(--color-accent-blue);
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-hover);
        }

        .feature-card.nursery {
            border-top-color: var(--color-accent-yellow);
        }

        .feature-card.primary {
            border-top-color: var(--color-accent-magenta);
        }

        .feature-icon {
            width: 70px;
            height: 70px;
            background: var(--color-bg-light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            font-size: 2rem;
            color: var(--color-primary);
        }

        .feature-card h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        /* About Section */
        .about-section {
            background-color: var(--color-bg-light);
        }

        .about-image {
            position: relative;
            border-radius: var(--border-radius-md);
            overflow: hidden;
            box-shadow: var(--shadow-card);
        }

        .about-image img {
            width: 100%;
            height: auto;
            transition: transform 0.5s ease;
        }

        .about-image:hover img {
            transform: scale(1.05);
        }

        .about-content h2 {
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
        }

        .about-content p {
            margin-bottom: 1.5rem;
            font-size: 1.1rem;
        }

        .stats-box {
            background: var(--color-white);
            padding: 2rem;
            border-radius: var(--border-radius-md);
            box-shadow: var(--shadow-card);
            text-align: center;
            margin-top: 2rem;
        }

        .stats-box h3 {
            color: var(--color-primary);
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }

        /* Newsletter Section */
        .newsletter-section {
            background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 100%);
            color: var(--color-white);
            padding: var(--spacing-lg) 0;
        }

        .newsletter-section h2 {
            color: var(--color-white);
        }

        .newsletter-form {
            max-width: 600px;
            margin: 0 auto;
        }

        .newsletter-form .form-control {
            padding: 1rem 1.5rem;
            border-radius: var(--border-radius-pill);
            border: none;
            font-size: 1rem;
            margin-bottom: 1rem;
        }

        .newsletter-form .form-control:focus {
            box-shadow: 0 0 0 0.25rem rgba(233, 0, 123, 0.25);
        }

        /* Contact Section */
        .contact-card {
            background: var(--color-white);
            border-radius: var(--border-radius-md);
            padding: var(--spacing-md);
            box-shadow: var(--shadow-card);
            margin-bottom: 2rem;
            text-align: center;
            transition: all 0.3s ease;
        }

        .contact-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
        }

        .contact-icon {
            width: 60px;
            height: 60px;
            background: var(--color-bg-light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            color: var(--color-primary);
            font-size: 1.5rem;
        }

        .contact-info h4 {
            margin-bottom: 0.5rem;
        }

        /* Footer */
        footer {
            background-color: var(--color-primary-dark);
            color: var(--color-white);
            padding: var(--spacing-lg) 0 var(--spacing-md);
        }

        footer h4 {
            color: var(--color-white);
            margin-bottom: 1.5rem;
            font-size: 1.3rem;
        }

        footer a {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: color 0.3s ease;
            display: block;
            margin-bottom: 0.75rem;
        }

        footer a:hover {
            color: var(--color-accent-yellow);
        }

        .footer-logo {
            font-family: var(--font-heading);
            font-weight: 700;
            font-size: 1.8rem;
            color: var(--color-white);
            margin-bottom: 1rem;
            display: block;
        }

        .social-links a {
            display: inline-block;
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            text-align: center;
            line-height: 40px;
            margin-right: 0.5rem;
            transition: all 0.3s ease;
        }

        .social-links a:hover {
            background: var(--color-accent-magenta);
            transform: translateY(-3px);
        }

        .footer-bottom {
            border-top: 1px solid rgba(255,255,255,0.1);
            margin-top: var(--spacing-md);
            padding-top: var(--spacing-md);
            text-align: center;
            color: rgba(255,255,255,0.6);
        }

        /* Responsive Adjustments */
        @media (max-width: 991px) {
            .hero-content h1 {
                font-size: 2.5rem;
            }
            
            .hero-section {
                text-align: center;
                padding: 6rem 0;
            }
            
            .hero-image {
                margin-top: 3rem;
            }
        }

        @media (max-width: 768px) {
            .hero-content h1 {
                font-size: 2rem;
            }
            
            .section-title h2 {
                font-size: 2rem;
            }
            
            .navbar-collapse {
                background: var(--color-white);
                padding: 1rem;
                border-radius: var(--border-radius-md);
                margin-top: 1rem;
                box-shadow: var(--shadow-card);
            }
        }

        /* Animation */
        .fade-in {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.6s ease-out, transform 0.6s ease-out;
        }

        .fade-in.visible {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>
<body>

    @yield('content')

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (navbar) {
                if (window.scrollY > 50) {
                    navbar.classList.add('scrolled');
                } else {
                    navbar.classList.remove('scrolled');
                }
            }
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;
                const target = document.querySelector(targetId);
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Close mobile menu on link click
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', () => {
                const navbarCollapse = document.querySelector('.navbar-collapse');
                if (navbarCollapse && navbarCollapse.classList.contains('show')) {
                    const bsCollapse = bootstrap.Collapse.getInstance(navbarCollapse);
                    if (bsCollapse) bsCollapse.hide();
                }
            });
        });

        // Animation on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: "0px 0px -50px 0px"
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        // Observe elements
        document.querySelectorAll('.fade-in, .feature-card, .contact-card, .about-content, .about-image').forEach(el => {
            if (!el.classList.contains('fade-in')) {
                el.classList.add('fade-in');
            }
            observer.observe(el);
        });
    </script>
</body>
</html>
