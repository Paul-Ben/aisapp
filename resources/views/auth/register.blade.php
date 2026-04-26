<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Alven International Schools') }} - Register</title>

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

        .register-container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 500px;
        }

        .register-card {
            background: var(--color-white);
            border-radius: var(--border-radius-md);
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            padding: 3rem 2.5rem;
            transition: all 0.3s ease;
        }

        .register-card:hover {
            box-shadow: 0 25px 70px rgba(0,0,0,0.35);
        }

        .register-logo {
            text-align: center;
            margin-bottom: 2rem;
        }

        .register-logo img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 50%;
            box-shadow: var(--shadow-card);
            border: 4px solid var(--color-primary);
        }

        .register-title {
            text-align: center;
            margin-bottom: 0.5rem;
        }

        .register-title h2 {
            font-family: var(--font-heading);
            font-weight: 700;
            color: var(--color-text-heading);
            font-size: 1.8rem;
        }

        .register-subtitle {
            text-align: center;
            color: var(--color-text-body);
            margin-bottom: 2rem;
            font-size: 0.95rem;
        }

        .form-group {
            margin-bottom: 1.25rem;
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

        .btn-register {
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
            margin-top: 0.5rem;
        }

        .btn-register:hover {
            background-color: var(--color-primary-dark);
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
            color: var(--color-white);
        }

        .login-link {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--color-border);
        }

        .login-link a {
            color: var(--color-primary);
            font-family: var(--font-heading);
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .login-link a:hover {
            color: var(--color-primary-dark);
            text-decoration: underline;
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
            .register-card {
                padding: 2rem 1.5rem;
            }

            .register-title h2 {
                font-size: 1.5rem;
            }

            .register-logo img {
                width: 80px;
                height: 80px;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-card">
            <!-- Logo -->
            <div class="register-logo">
                <a href="{{ route('home') }}" class="logo-link">
                    <img src="{{ asset('assets/images/logo.jpg') }}" alt="Alven International Schools Logo">
                </a>
            </div>

            <!-- Title -->
            <div class="register-title">
                <h2>Create Account</h2>
            </div>
            <p class="register-subtitle">Register to get started</p>

            <!-- Registration Form -->
            <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- Name -->
                <div class="form-group">
                    <label for="name" class="form-label">Name</label>
                    <input id="name" 
                           type="text" 
                           class="form-control @error('name') is-invalid @enderror" 
                           name="name" 
                           value="{{ old('name') }}" 
                           required 
                           autofocus 
                           autocomplete="name"
                           placeholder="Enter your full name">
                    @error('name')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Email Address -->
                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <input id="email" 
                           type="email" 
                           class="form-control @error('email') is-invalid @enderror" 
                           name="email" 
                           value="{{ old('email') }}" 
                           required 
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
                           autocomplete="new-password"
                           placeholder="Create a password">
                    @error('password')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div class="form-group">
                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                    <input id="password_confirmation" 
                           type="password" 
                           class="form-control @error('password_confirmation') is-invalid @enderror" 
                           name="password_confirmation" 
                           required 
                           autocomplete="new-password"
                           placeholder="Confirm your password">
                    @error('password_confirmation')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-register">
                    <i class="fas fa-user-plus me-2"></i>Register
                </button>
            </form>

            <!-- Already Registered Link -->
            <div class="login-link">
                <a href="{{ route('login') }}">
                    <i class="fas fa-sign-in-alt"></i>Already registered? Log in
                </a>
            </div>

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
