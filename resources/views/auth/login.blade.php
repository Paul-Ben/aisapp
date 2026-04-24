<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Alven International Schools') }} - Login</title>

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
            background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 800px;
            height: 800px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
            z-index: 0;
        }

        body::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -10%;
            width: 600px;
            height: 600px;
            background: rgba(255,255,255,0.03);
            border-radius: 50%;
            z-index: 0;
        }

        .login-container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 450px;
        }

        .login-card {
            background: var(--color-white);
            border-radius: var(--border-radius-md);
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            padding: 3rem 2.5rem;
            transition: all 0.3s ease;
        }

        .login-card:hover {
            box-shadow: 0 25px 70px rgba(0,0,0,0.35);
        }

        .login-logo {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-logo img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 50%;
            box-shadow: var(--shadow-card);
            border: 4px solid var(--color-primary);
        }

        .login-title {
            text-align: center;
            margin-bottom: 0.5rem;
        }

        .login-title h2 {
            font-family: var(--font-heading);
            font-weight: 700;
            color: var(--color-text-heading);
            font-size: 1.8rem;
        }

        .login-subtitle {
            text-align: center;
            color: var(--color-text-body);
            margin-bottom: 2rem;
            font-size: 0.95rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            font-family: var(--font-heading);
            font-weight: 600;
            color: var(--color-text-heading);
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .form-control {
            padding: 0.875rem 1.25rem;
            border: 2px solid var(--color-border);
            border-radius: var(--border-radius-sm);
            font-family: var(--font-body);
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--color-primary);
            box-shadow: 0 0 0 0.25rem rgba(0, 51, 153, 0.15);
            outline: none;
        }

        .form-check-input {
            width: 1.1em;
            height: 1.1em;
            border: 2px solid var(--color-border);
        }

        .form-check-input:checked {
            background-color: var(--color-primary);
            border-color: var(--color-primary);
        }

        .form-check-label {
            font-family: var(--font-body);
            color: var(--color-text-body);
            font-size: 0.9rem;
            margin-left: 0.5rem;
        }

        .btn-login {
            background-color: var(--color-primary);
            color: var(--color-white);
            padding: 0.875rem 2rem;
            border-radius: var(--border-radius-pill);
            font-family: var(--font-heading);
            font-weight: 600;
            border: none;
            transition: all 0.3s ease;
            width: 100%;
            font-size: 1rem;
        }

        .btn-login:hover {
            background-color: var(--color-primary-dark);
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
            color: var(--color-white);
        }

        .forgot-password {
            text-align: right;
            margin-bottom: 1.5rem;
        }

        .forgot-password a {
            color: var(--color-primary);
            font-family: var(--font-heading);
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .forgot-password a:hover {
            color: var(--color-primary-dark);
            text-decoration: underline;
        }

        .divider {
            text-align: center;
            margin: 1.5rem 0;
            position: relative;
        }

        .divider::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            width: 100%;
            height: 1px;
            background: var(--color-border);
        }

        .divider span {
            background: var(--color-white);
            padding: 0 1rem;
            position: relative;
            color: var(--color-text-body);
            font-size: 0.85rem;
        }

        .back-home {
            text-align: center;
            margin-top: 1.5rem;
        }

        .back-home a {
            color: var(--color-white);
            font-family: var(--font-heading);
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .back-home a:hover {
            color: var(--color-accent-yellow);
        }

        .alert {
            border-radius: var(--border-radius-sm);
            font-family: var(--font-body);
            font-size: 0.9rem;
            padding: 0.875rem 1.25rem;
            margin-bottom: 1.5rem;
        }

        .text-danger {
            color: #dc3545 !important;
            font-size: 0.85rem;
            margin-top: 0.25rem;
            font-family: var(--font-body);
        }

        .is-invalid {
            border-color: #dc3545 !important;
        }

        .is-invalid:focus {
            box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.15) !important;
        }

        /* Responsive Adjustments */
        @media (max-width: 576px) {
            .login-card {
                padding: 2rem 1.5rem;
            }

            .login-title h2 {
                font-size: 1.5rem;
            }

            .login-logo img {
                width: 80px;
                height: 80px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <!-- Logo -->
            <div class="login-logo">
                <a href="{{ route('home') }}" class="logo-link">
                    <img src="{{ asset('assets/images/logo.jpg') }}" alt="Alven International Schools Logo">
                </a>
            </div>

            <!-- Title -->
            <div class="login-title">
                <h2>Welcome Back</h2>
            </div>
            <p class="login-subtitle">Sign in to access your account</p>

            <!-- Session Status -->
            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Login Form -->
            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email Address -->
                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <input id="email" 
                           type="email" 
                           class="form-control @error('email') is-invalid @enderror" 
                           name="email" 
                           value="{{ old('email') }}" 
                           required 
                           autofocus 
                           autocomplete="username"
                           placeholder="Enter your email">
                    @error('email')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input id="password" 
                           type="password" 
                           class="form-control @error('password') is-invalid @enderror" 
                           name="password" 
                           required 
                           autocomplete="current-password"
                           placeholder="Enter your password">
                    @error('password')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check d-flex align-items-center">
                        <input id="remember_me" 
                               type="checkbox" 
                               class="form-check-input" 
                               name="remember">
                        <label for="remember_me" class="form-check-label">Remember me</label>
                    </div>
                    @if (Route::has('password.request'))
                        <div class="forgot-password">
                            <a href="{{ route('password.request') }}">Forgot Password?</a>
                        </div>
                    @endif
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-login">
                    <i class="fas fa-sign-in-alt me-2"></i>Log in
                </button>
            </form>

            <!-- Back to Home -->
            <div class="back-home">
                <a href="{{ url('/') }}">
                    <i class="fas fa-arrow-left"></i>Back to Home
                </a>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
