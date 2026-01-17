<?php
/**
 * We Konek - Student Dashboard
 * Student Information System
 *
 * Path: pages/students/student_dashboard.php
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include centralized config
require_once __DIR__ . '/../../config/config.php';

// Set default session variables for demo (remove in production)
if (!isset($_SESSION['role'])) {
    $_SESSION['role'] = 'student';
    $_SESSION['user_name'] = 'Juan Dela Cruz';
    $_SESSION['user_id'] = 1;
}

// Get user information from session
$userRole = isset($_SESSION['role']) ? strtolower($_SESSION['role']) : 'student';
$userName = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Guest User';

// Get role display name using centralized function
$roleDisplay = getRoleDisplay($userRole);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - We Konek SIS</title>

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="../../assets/images/Logo.png">

    <!-- Stylesheets - CRITICAL ORDER: variables FIRST, then components, then dashboard -->
    <link rel="stylesheet" href="../../assets/css/variables.css">
    <link rel="stylesheet" href="../../assets/css/sidebar.css">
    <link rel="stylesheet" href="../../assets/css/header.css">
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
</head>
<body>
    <!-- Include Sidebar Component -->
    <?php include '../../includes/sidebar.php'; ?>

    <!-- Include Header Component -->
    <?php include '../../includes/header.php'; ?>

    <!-- Main Dashboard Container -->
    <div class="dashboard-container">

        <!-- Welcome Section -->
        <section class="welcome-section">
            <div class="welcome-content">
                <h1 class="welcome-title">Welcome back, <?php echo htmlspecialchars($userName); ?>!</h1>
                <p class="welcome-subtitle">Here's your schedule and updates for today</p>
            </div>
            <div class="welcome-date">
                <i class="far fa-calendar-alt"></i>
                <span id="currentDate"></span>
            </div>
        </section>

        <!-- Event Hero Section -->
        <section class="event-hero-section">
            <h2 class="section-title">
                <i class="fas fa-star"></i>
                Featured Event
            </h2>
            <div class="hero-content" id="heroContent">
                <!-- Dynamic content will be injected by JavaScript -->
            </div>
        </section>

        <!-- My Classes Schedule Section -->
        <section class="schedule-section">
            <h2 class="section-title">
                <i class="fas fa-calendar-week"></i>
                My Classes - Weekly Schedule
            </h2>

            <div class="department-schedule">
                <div class="department-header">
                    <div class="department-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div>
                        <h3 class="department-title">BSIT 3A Schedule</h3>
                        <p class="department-subtitle">Bachelor of Science in Information Technology - Third Year</p>
                    </div>
                </div>
                <div class="schedule-table-wrapper">
                    <table class="schedule-table">
                        <thead>
                            <tr>
                                <th>Time</th>
                                <th>Monday</th>
                                <th>Tuesday</th>
                                <th>Wednesday</th>
                                <th>Thursday</th>
                                <th>Friday</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="time-slot">8:00 - 10:00 AM</td>
                                <td>Database Systems<br><small>Dr. Sarah Johnson<br>Room 301</small></td>
                                <td>Web Development<br><small>Prof. Michael Chen<br>Room 302</small></td>
                                <td>Database Systems<br><small>Dr. Sarah Johnson<br>Room 301</small></td>
                                <td>Network Security<br><small>Prof. David Williams<br>Room 305</small></td>
                                <td>Systems Analysis<br><small>Dr. Lisa Anderson<br>Room 304</small></td>
                            </tr>
                            <tr>
                                <td class="time-slot">10:00 - 12:00 PM</td>
                                <td>Programming Logic<br><small>Prof. James Martinez<br>Room 303</small></td>
                                <td class="empty-slot">—</td>
                                <td>Web Development<br><small>Prof. Michael Chen<br>Room 302</small></td>
                                <td>Mobile Development<br><small>Dr. Emily Rodriguez<br>Room 304</small></td>
                                <td class="empty-slot">—</td>
                            </tr>
                            <tr>
                                <td class="time-slot">1:00 - 3:00 PM</td>
                                <td>Software Engineering<br><small>Dr. Sarah Johnson<br>Room 305</small></td>
                                <td>Cloud Computing<br><small>Prof. David Williams<br>Room 303</small></td>
                                <td class="empty-slot">—</td>
                                <td class="empty-slot">—</td>
                                <td>Lab Session<br><small>Various Instructors<br>Room 307</small></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- Announcements Section -->
        <section class="announcements-section">
            <h2 class="section-title">
                <i class="fas fa-bullhorn"></i>
                Recent Announcements
            </h2>
            <div class="announcements-grid" id="announcementsGrid">
                <!-- Announcement cards will be injected by JavaScript -->
            </div>
        </section>

        <!-- Posted Grades Section -->
        <section class="grade-request-section">
            <h2 class="section-title">
                <i class="fas fa-chart-line"></i>
                My Posted Grades
            </h2>
            <div class="table-wrapper">
                <table class="grade-request-table">
                    <thead>
                        <tr>
                            <th>Subject</th>
                            <th>Instructor</th>
                            <th>Midterm</th>
                            <th>Finals</th>
                            <th>Overall</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody id="studentGradesBody">
                        <!-- Grades will be injected by JavaScript -->
                    </tbody>
                </table>
            </div>
        </section>

    </div>

    <!-- JavaScript Files -->
    <script src="../../assets/js/sidebar.js"></script>
    <script src="../../assets/js/header.js"></script>
    <script src="../../assets/js/dashboard.js"></script>
</body>
</html>