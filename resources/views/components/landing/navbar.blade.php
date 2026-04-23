<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
            <img src="{{ asset('assets/images/logo.jpg') }}" alt="AIS Logo">
            <span>Alven International Schools</span>
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item">
                    <a class="nav-link" href="#home">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#about">About Us</a>
                </li>
                
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown1" role="button" data-bs-toggle="dropdown">
                        Parent Resources
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">Term Newsletter</a></li>
                        <li><a class="dropdown-item" href="{{ route('calendar.show') }}">Term Calendar</a></li>
                        <li><a class="dropdown-item" href="#">Girls Hairstyles</a></li>
                        <li><a class="dropdown-item" href="#">Uniform Policy</a></li>
                        <li><a class="dropdown-item" href="#">Exam Timetable</a></li>
                        <li><a class="dropdown-item" href="#">Admission Process</a></li>
                    </ul>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link" href="#contact">Contact</a>
                </li>
            
                @if (Route::has('login'))
                    @auth
                        <li class="nav-item ms-lg-3">
                            <a href="{{ url('/dashboard') }}" class="btn btn-primary-custom">Dashboard</a>
                        </li>
                    @else
                        <li class="nav-item ms-lg-3">
                            <a href="{{ route('login') }}" class="nav-link">Log in</a>
                        </li>

                        @if (Route::has('register'))
                            <li class="nav-item">
                                <a href="{{ route('register') }}" class="btn btn-primary-custom">Register</a>
                            </li>
                        @endif
                    @endauth
                @endif
            </ul>
        </div>
    </div>
</nav>
