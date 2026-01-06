<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>We Konek - Student Information System</title>
    <meta name="description" content="We Konek is a comprehensive Student Information System that connects students, teachers, and administrators seamlessly." />
    <meta property="og:image" content="https://bolt.new/static/og_default.png" />
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:image" content="https://bolt.new/static/og_default.png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/landingPage.css" />
  </head>
  <body>
    <nav class="navbar">
      <div class="container">
        <div class="navbar-content">
          <div class="navbar-brand">
            <div class="logo-container">
              <img src="assets/images/Logo.png" alt="We Konek Logo" class="brand-logo">
            </div>
          </div>

          <div class="navbar-menu">
            <a href="#home" class="nav-link">Home</a>
            <a href="#features" class="nav-link">Features</a>
            <a href="#about" class="nav-link">About</a>
            <a href="#terms" class="nav-link">Terms</a>
            <a href="#contact" class="nav-link">Contact</a>
            <a href="#" class="nav-link notification-link" onclick="openModal(event)" aria-label="Notifications">
              <i class="fas fa-bell"></i>
            </a>
            <button class="btn btn-primary" onclick="openModal(event)">Get Started</button>
          </div>

          <button class="menu-toggle" id="menuToggle" aria-label="Toggle menu">
            <span></span>
            <span></span>
            <span></span>
          </button>
        </div>
      </div>

      <div class="mobile-menu" id="mobileMenu">
        <a href="#home" class="mobile-nav-link">Home</a>
        <a href="#features" class="mobile-nav-link">Features</a>
        <a href="#about" class="mobile-nav-link">About</a>
        <a href="#terms" class="mobile-nav-link">Terms</a>
        <a href="#contact" class="mobile-nav-link">Contact</a>
        <a href="#" class="mobile-nav-link" onclick="openModal(event)">
          <i class="fas fa-bell"></i> Notifications
        </a>
        <button class="btn btn-primary btn-block" onclick="openModal(event)">Get Started</button>
      </div>
    </nav>

    <main>
      <section id="home" class="hero">
        <div class="container">
          <div class="hero-content">
            <div class="hero-text">
              <h1>Smart Connections for <span class="accent">Smart Education</span></h1>
              <p>Your comprehensive Student Information System that connects students, teachers, and administrators seamlessly. Real-time updates, instant communication, and efficient academic management—all in one platform.</p>
              <div class="hero-buttons">
                <button class="btn btn-primary" onclick="openModal(event)">Get Started</button>
                <button class="btn btn-secondary" onclick="scrollToSection('about')">Learn More</button>
              </div>
            </div>

            <div class="hero-logo">
              <div class="logo-box">
                <img src="assets/images/Logo.png" alt="We Konek Platform" class="hero-logo-img">
              </div>
              <div class="logo-glow glow-1"></div>
              <div class="logo-glow glow-2"></div>
            </div>
          </div>
        </div>
      </section>

      <section id="features" class="features">
        <div class="container">
          <div class="section-header">
            <h2>Why Choose We Konek?</h2>
            <p>Powerful features designed for modern education</p>
          </div>

          <div class="features-grid">
            <article class="feature-card">
              <div class="feature-icon icon-yellow">
                <i class="fas fa-users"></i>
              </div>
              <h3>Centralized Communication</h3>
              <p>Connect seamlessly with faculty, students, and administrators in one unified platform for efficient collaboration.</p>
            </article>

            <article class="feature-card">
              <div class="feature-icon icon-green">
                <i class="fas fa-clock"></i>
              </div>
              <h3>Real-Time Updates</h3>
              <p>Stay informed with instant notifications for announcements, schedule changes, and important academic updates.</p>
            </article>

            <article class="feature-card">
              <div class="feature-icon icon-gradient">
                <i class="fas fa-calendar-alt"></i>
              </div>
              <h3>Faster Scheduling</h3>
              <p>Manage and track courses, schedules, and academic requirements with ease and precision in real-time.</p>
            </article>

            <article class="feature-card">
              <div class="feature-icon icon-yellow">
                <i class="fas fa-check-circle"></i>
              </div>
              <h3>Delay-Free Grade Submission</h3>
              <p>Submit and access grades instantly without delays, ensuring transparency and timely academic feedback.</p>
            </article>

            <article class="feature-card">
              <div class="feature-icon icon-green">
                <i class="fas fa-user-graduate"></i>
              </div>
              <h3>Student Management</h3>
              <p>Efficiently manage student information, academic progress, and records all in one centralized location.</p>
            </article>

            <article class="feature-card">
              <div class="feature-icon icon-gradient">
                <i class="fas fa-chart-line"></i>
              </div>
              <h3>Analytics & Reports</h3>
              <p>Generate comprehensive reports and gain valuable insights into academic performance instantly.</p>
            </article>
          </div>
        </div>
      </section>

      <section id="about" class="about">
        <div class="container">
          <h2>About We Konek</h2>
          <div class="about-content">
            <p>At We Konek, we are dedicated to transforming the way educational institutions manage communication, scheduling, and academic processes. Our platform was developed to address common challenges such as late grade submissions, delayed announcements, disorganized schedules, and fragmented communication between faculty, students, and administrators.</p>
            
            <p>We Konek provides a centralized digital environment where users can connect seamlessly and access real-time updates. With features that enable instant announcements, real-time schedule changes, and delay-free grade submissions, our system ensures that everyone stays informed and up-to-date—anytime, anywhere.</p>
            
            <p>Our mission is to bridge the gap between efficiency and accessibility in education by creating a platform that supports transparency, timeliness, and collaboration. We Konek is more than just a system—it's a step toward a more connected and responsive academic community.</p>
          </div>
        </div>
      </section>

      <section id="terms" class="terms">
        <div class="container">
          <div class="section-header">
            <h2>Terms & Conditions</h2>
            <p>Please read our terms carefully before using our services</p>
          </div>

          <div class="terms-grid">
            <article class="terms-card">
              <div class="terms-icon">
                <i class="fas fa-shield-alt"></i>
              </div>
              <h3>Privacy Policy</h3>
              <p>We collect and process personal information including names, email addresses, student/faculty IDs, and academic records solely for educational purposes. Your data is stored securely and never shared with third parties without your explicit consent.</p>
            </article>

            <article class="terms-card">
              <div class="terms-icon">
                <i class="fas fa-lock"></i>
              </div>
              <h3>Data Security</h3>
              <p>We implement industry-standard security measures including SSL encryption, secure authentication protocols, and regular security audits to protect your information.</p>
            </article>

            <article class="terms-card">
              <div class="terms-icon">
                <i class="fas fa-user-check"></i>
              </div>
              <h3>User Responsibilities</h3>
              <p>Users must maintain the confidentiality of their login credentials and are responsible for all activities under their account. You agree not to share your password or use the platform for any unlawful purposes.</p>
            </article>

            <article class="terms-card">
              <div class="terms-icon">
                <i class="fas fa-database"></i>
              </div>
              <h3>Data Retention</h3>
              <p>Academic records and user data are retained for the duration of your enrollment or employment, plus an additional period as required by educational regulations.</p>
            </article>

            <article class="terms-card">
              <div class="terms-icon">
                <i class="fas fa-cookie-bite"></i>
              </div>
              <h3>Cookies & Tracking</h3>
              <p>We use essential cookies to maintain your session and improve user experience. You can manage cookie preferences through your browser settings.</p>
            </article>

            <article class="terms-card">
              <div class="terms-icon">
                <i class="fas fa-file-contract"></i>
              </div>
              <h3>Acceptable Use</h3>
              <p>Users must use We Konek exclusively for legitimate educational purposes. Prohibited activities include harassment, uploading malicious content, or attempting to breach security measures.</p>
            </article>
          </div>

          <div class="terms-footer">
            <p><strong>Questions or Concerns?</strong></p>
            <p>If you have any questions about our Terms & Conditions or Privacy Policy, please contact us at <a href="mailto:privacy@wekonek.edu.ph">privacy@wekonek.edu.ph</a></p>
            <p class="terms-update">Last Updated: December 24, 2024</p>
          </div>
        </div>
      </section>

      <section id="contact" class="newsletter">
        <div class="container">
          <div class="newsletter-content">
            <div class="newsletter-text">
              <h2>Sign Up to Get New Updates</h2>
              <p>Get email updates about our new announcements, features, and schedule changes</p>
            </div>
            <div class="newsletter-form">
              <form id="newsletterForm">
                <input type="email" placeholder="Your email address" required aria-label="Email address">
                <button type="submit" class="btn btn-accent">Sign Up</button>
              </form>
            </div>
          </div>
        </div>
      </section>

      <section class="cta">
        <div class="container">
          <h2>Ready to Get Started?</h2>
          <button class="btn btn-accent" onclick="openModal(event)">Get Started Today</button>
        </div>
      </section>
    </main>

    <footer class="footer">
      <div class="container">
        <div class="footer-content">
          <div class="footer-brand">
            <div class="footer-logo-container">
              <img src="assets/images/Logo.png" alt="We Konek Logo" class="footer-logo">
            </div>
          </div>
          <p>&copy; 2024 We Konek. All rights reserved.</p>
        </div>
      </div>
    </footer>

    <!-- Login Role Modal -->
    <div id="loginModal" class="modal">
      <div class="modal-content">
        <span class="close-modal" onclick="closeModal()">&times;</span>
        <h2>Login As</h2>
        <p>Select your role to continue</p>
        <div class="modal-buttons">
          <a href="auth/login.php?role=faculty" class="modal-btn faculty-btn">
            <i class="fas fa-chalkboard-user"></i>
            <span>Faculty</span>
          </a>
          <a href="auth/login.php?role=student" class="modal-btn student-btn">
            <i class="fas fa-graduation-cap"></i>
            <span>Student</span>
          </a>
        </div>
      </div>
    </div>

    <script src="assets/js/landingPage.js"></script>
  </body>
</html>