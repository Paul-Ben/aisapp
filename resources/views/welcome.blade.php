@extends('layouts.landing')

@section('content')
    <x-landing.navbar />

    <!-- Hero Section -->
    <section id="home" class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 hero-content fade-in">
                    <span class="hero-tagline">Building The Future</span>
                    <h1>Welcome to Alven International Schools</h1>
                    <p>Nurturing young minds with excellence in education. We provide a holistic learning environment where every child discovers their potential and builds a foundation for lifelong success.</p>
                    <div class="d-flex gap-3 flex-wrap justify-content-center justify-content-lg-start">
                        <a href="#about" class="btn btn-primary-custom btn-lg">Learn More</a>
                        <a href="#contact" class="btn btn-accent btn-lg">Book a Tour</a>
                    </div>
                </div>
                <div class="col-lg-6 hero-image fade-in">
                    <img src="https://images.unsplash.com/photo-1503454537195-1dcabb73ffb9?w=800&h=600&fit=crop" alt="Happy Students" class="img-fluid">
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="section-padding">
        <div class="container">
            <div class="section-title">
                <h2>Why Choose AIS?</h2>
                <p>We provide exceptional education with a focus on character development, academic excellence, and preparing students for the future.</p>
            </div>
            
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card nursery">
                        <div class="feature-icon">
                            <i class="fas fa-child"></i>
                        </div>
                        <h3>Nursery Program</h3>
                        <p>Early childhood education that fosters creativity, social skills, and foundational learning in a nurturing environment.</p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="feature-card primary">
                        <div class="feature-icon">
                            <i class="fas fa-book-reader"></i>
                        </div>
                        <h3>Primary Education</h3>
                        <p>Comprehensive curriculum designed to develop critical thinking, problem-solving skills, and academic excellence.</p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <h3>Expert Faculty</h3>
                        <p>Highly qualified and passionate teachers dedicated to bringing out the best in every student.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="section-padding about-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <div class="about-image">
                        <img src="https://images.unsplash.com/photo-1524178232363-1fb2b075b655?w=800&h=600&fit=crop" alt="About AIS" class="img-fluid">
                    </div>
                </div>
                <div class="col-lg-6 about-content">
                    <h2>About Alven International Schools</h2>
                    <p>At Alven International Schools, we believe in building the future by providing quality education that goes beyond textbooks. Our institution is committed to developing well-rounded individuals who are equipped to face the challenges of tomorrow.</p>
                    <p>With state-of-the-art facilities, experienced educators, and a student-centered approach, we create an environment where learning becomes an exciting journey of discovery.</p>
                    
                    <div class="row">
                        <div class="col-6">
                            <div class="stats-box">
                                <h3>15+</h3>
                                <p>Years of Excellence</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stats-box">
                                <h3>1000+</h3>
                                <p>Happy Students</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Newsletter Section -->
    <section class="newsletter-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h2>Subscribe to Our Newsletter</h2>
                    <p class="mb-4">Stay updated with the latest news, events, and announcements from Alven International Schools.</p>
                    
                    <form class="newsletter-form" onsubmit="event.preventDefault(); alert('Thank you for subscribing!');">
                        <div class="row g-2">
                            <div class="col-md-8">
                                <input type="email" class="form-control" placeholder="Enter your email address" required>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-accent w-100">Subscribe</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="section-padding">
        <div class="container">
            <div class="section-title">
                <h2>Get In Touch</h2>
                <p>We'd love to hear from you. Reach out to us for admissions, inquiries, or just to say hello.</p>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="contact-card">
                        <div class="contact-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="contact-info">
                            <h4>Visit Us</h4>
                            <p>No3A FMH&E Quaters<br>High Level Makurdi, Benue State</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="contact-card">
                        <div class="contact-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div class="contact-info">
                            <h4>Call Us</h4>
                            <p>+ (234) 7030000000<br>+ (234) 7030000001</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="contact-card">
                        <div class="contact-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="contact-info">
                            <h4>Email Us</h4>
                            <p>info@alvenschools.edu<br>admissions@alvenschools.edu</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <x-landing.footer />
@endsection
