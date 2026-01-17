<?php
/**
 * We Konek - Faculty Dashboard
 * Student Information System
 *
 * Path: pages/faculty/faculty_dashboard.php
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include centralized config
require_once __DIR__ . '/../../config/config.php';

// Set default session variables for demo (remove in production)
if (!isset($_SESSION['role'])) {
    $_SESSION['role'] = 'faculty';
    $_SESSION['user_name'] = 'Dr. Sarah Johnson';
    $_SESSION['user_id'] = 1;
}

// Get user information from session
$userRole = isset($_SESSION['role']) ? strtolower($_SESSION['role']) : 'faculty';
$userName = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Guest User';

// Get role display name using centralized function
$roleDisplay = getRoleDisplay($userRole);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Dashboard - We Konek SIS</title>

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
                <p class="welcome-subtitle">Here's your teaching schedule and updates for today</p>
            </div>
            <div class="welcome-date">
                <i class="far fa-calendar-alt"></i>
                <span id="currentDate"></span>
            </div>
        </section>

        <!-- Quick Actions Section -->
        <section class="quick-actions-section">
            <h2 class="section-title">
                <i class="fas fa-bolt"></i>
                Quick Actions
            </h2>
            <div class="quick-actions-grid faculty-actions">
                <button class="action-btn" data-action="submit-grades">
                    <div class="action-icon-wrapper">
                        <i class="fas fa-file-upload"></i>
                    </div>
                    <span class="action-text">Submit Grades</span>
                </button>
                <button class="action-btn" data-action="update-attendance">
                    <div class="action-icon-wrapper">
                        <i class="fas fa-clipboard-check"></i>
                    </div>
                    <span class="action-text">Update Attendance</span>
                </button>
                <button class="action-btn" data-action="post-assignment">
                    <div class="action-icon-wrapper">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <span class="action-text">Post Assignment</span>
                </button>
                <button class="action-btn" data-action="view-students">
                    <div class="action-icon-wrapper">
                        <i class="fas fa-users"></i>
                    </div>
                    <span class="action-text">View My Students</span>
                </button>
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
                <i class="fas fa-chalkboard-teacher"></i>
                My Classes - Weekly Schedule
            </h2>

            <div class="department-schedule">
                <div class="department-header">
                    <div class="department-icon">
                        <i class="fas fa-laptop-code"></i>
                    </div>
                    <div>
                        <h3 class="department-title">CBAT.COM Department</h3>
                        <p class="department-subtitle">Computer Science & Information Technology</p>
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
                                <td>Database Systems<br><small>Room 301 • BSIT 3A</small></td>
                                <td class="empty-slot">—</td>
                                <td>Database Systems<br><small>Room 301 • BSIT 3B</small></td>
                                <td class="empty-slot">—</td>
                                <td class="empty-slot">—</td>
                            </tr>
                            <tr>
                                <td class="time-slot">10:00 - 12:00 PM</td>
                                <td class="empty-slot">—</td>
                                <td>Data Structures<br><small>Room 301 • BSCS 2A</small></td>
                                <td class="empty-slot">—</td>
                                <td class="empty-slot">—</td>
                                <td>Capstone Advising<br><small>Room 306 • BSIT 4A</small></td>
                            </tr>
                            <tr>
                                <td class="time-slot">1:00 - 3:00 PM</td>
                                <td>Software Engineering<br><small>Room 305 • BSCS 3A</small></td>
                                <td class="empty-slot">—</td>
                                <td>Algorithm Design<br><small>Room 301 • BSCS 3B</small></td>
                                <td class="empty-slot">—</td>
                                <td>Consultation Hours<br><small>Faculty Office</small></td>
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

        <!-- Grade Submission Requests Section -->
        <section class="grade-request-section">
            <h2 class="section-title">
                <i class="fas fa-clipboard-list"></i>
                My Grade Submission Requests
            </h2>
            <div class="table-wrapper">
                <table class="grade-request-table">
                    <thead>
                        <tr>
                            <th>Subject</th>
                            <th>Class</th>
                            <th>Request Type</th>
                            <th>Submitted Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="facultyGradeRequestsBody">
                        <!-- Grade requests will be injected by JavaScript -->
                    </tbody>
                </table>
            </div>
        </section>

    </div>

    <!-- Reusable Modal -->
    <div class="modal-overlay" id="modalOverlay">
        <div class="modal-container">
            <div class="modal-header">
                <h3 class="modal-title" id="modalTitle">Modal Title</h3>
                <button class="modal-close" id="modalClose" aria-label="Close modal">&times;</button>
            </div>
            <div class="modal-body" id="modalBody">
                <!-- Dynamic content will be injected here -->
            </div>
            <div class="modal-footer">
                <button class="btn-secondary" id="modalCancel">Cancel</button>
                <button class="btn-primary" id="modalSubmit">Submit</button>
            </div>
        </div>
    </div>

    <!-- JavaScript Files -->
    <script src="../../assets/js/sidebar.js"></script>
    <script src="../../assets/js/header.js"></script>
    <script src="../../assets/js/dashboard.js"></script>
</body>
</html>