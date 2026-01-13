<?php
/**
 * We Konek - Admin Dashboard
 * Student Information System
 *
 * Path: pages/admins/admin_dashboard.php
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include centralized config
require_once __DIR__ . '/../../config/config.php';

// Set default session variables for demo (remove in production)
if (!isset($_SESSION['role'])) {
    $_SESSION['role'] = 'admin';
    $_SESSION['user_name'] = 'Admin';
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
    <title>Admin Dashboard - We Konek SIS</title>

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
                <p class="welcome-subtitle">Here's what's happening in your institution today</p>
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
            <div class="quick-actions-grid">
                <button class="action-btn" data-action="add-user">
                    <div class="action-icon-wrapper">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <span class="action-text">Add User</span>
                </button>
                <button class="action-btn" data-action="create-schedule">
                    <div class="action-icon-wrapper">
                        <i class="fas fa-calendar-plus"></i>
                    </div>
                    <span class="action-text">Create Schedule</span>
                </button>
                <button class="action-btn" data-action="add-event">
                    <div class="action-icon-wrapper">
                        <i class="fas fa-calendar-star"></i>
                    </div>
                    <span class="action-text">Add Event</span>
                </button>
                <button class="action-btn" data-action="post-announcement">
                    <div class="action-icon-wrapper">
                        <i class="fas fa-bullhorn"></i>
                    </div>
                    <span class="action-text">Post Announcement</span>
                </button>
                <button class="action-btn" data-action="generate-report">
                    <div class="action-icon-wrapper">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <span class="action-text">Generate Report</span>
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

        <!-- Weekly Department Schedule Section -->
        <section class="schedule-section">
            <h2 class="section-title">
                <i class="fas fa-calendar-week"></i>
                Weekly Department Schedule
            </h2>

            <!-- CBAT.COM Department -->
            <div class="department-schedule">
                <div class="department-header">
                    <div class="department-icon">
                        <i class="fas fa-laptop-code"></i>
                    </div>
                    <div>
                        <h3 class="department-title">CBAT.COM</h3>
                        <p class="department-subtitle">College of Business Administration, Tourism, and Computer Science</p>
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
                                <td>Database Systems<br><small>Room 301</small></td>
                                <td>Web Development<br><small>Room 302</small></td>
                                <td>Database Systems<br><small>Room 301</small></td>
                                <td>Network Security<br><small>Room 305</small></td>
                                <td>Systems Analysis<br><small>Room 304</small></td>
                            </tr>
                            <tr>
                                <td class="time-slot">10:00 - 12:00 PM</td>
                                <td>Programming Logic<br><small>Room 303</small></td>
                                <td>Data Structures<br><small>Room 301</small></td>
                                <td>Web Development<br><small>Room 302</small></td>
                                <td>Mobile Development<br><small>Room 304</small></td>
                                <td>Capstone Project<br><small>Room 306</small></td>
                            </tr>
                            <tr>
                                <td class="time-slot">1:00 - 3:00 PM</td>
                                <td>Software Engineering<br><small>Room 305</small></td>
                                <td>Cloud Computing<br><small>Room 303</small></td>
                                <td>Algorithm Design<br><small>Room 301</small></td>
                                <td>Business Analytics<br><small>Room 302</small></td>
                                <td>Lab Session<br><small>Room 307</small></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- COTE Department -->
            <div class="department-schedule">
                <div class="department-header">
                    <div class="department-icon">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <div>
                        <h3 class="department-title">COTE</h3>
                        <p class="department-subtitle">College of Teacher Education</p>
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
                                <td>Educational Psychology<br><small>Room 201</small></td>
                                <td>Child Development<br><small>Room 202</small></td>
                                <td>Teaching Methods<br><small>Room 203</small></td>
                                <td>Curriculum Planning<br><small>Room 204</small></td>
                                <td>Assessment Methods<br><small>Room 205</small></td>
                            </tr>
                            <tr>
                                <td class="time-slot">10:00 - 12:00 PM</td>
                                <td>Classroom Management<br><small>Room 206</small></td>
                                <td>Educational Technology<br><small>Room 207</small></td>
                                <td>Special Education<br><small>Room 208</small></td>
                                <td>Literacy Development<br><small>Room 201</small></td>
                                <td>Student Teaching<br><small>Various</small></td>
                            </tr>
                            <tr>
                                <td class="time-slot">1:00 - 3:00 PM</td>
                                <td>Mathematics Education<br><small>Room 202</small></td>
                                <td>Science Education<br><small>Room 203</small></td>
                                <td>Social Studies Ed.<br><small>Room 204</small></td>
                                <td>Arts Integration<br><small>Room 205</small></td>
                                <td>Seminar<br><small>Room 206</small></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- CRIM Department -->
            <div class="department-schedule">
                <div class="department-header">
                    <div class="department-icon">
                        <i class="fas fa-balance-scale"></i>
                    </div>
                    <div>
                        <h3 class="department-title">CRIM</h3>
                        <p class="department-subtitle">Criminology Department</p>
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
                                <td>Criminal Law<br><small>Room 101</small></td>
                                <td>Forensic Science<br><small>Room 102</small></td>
                                <td>Criminal Investigation<br><small>Room 103</small></td>
                                <td>Juvenile Delinquency<br><small>Room 104</small></td>
                                <td>Law Enforcement<br><small>Room 105</small></td>
                            </tr>
                            <tr>
                                <td class="time-slot">10:00 - 12:00 PM</td>
                                <td>Corrections System<br><small>Room 106</small></td>
                                <td>Criminal Psychology<br><small>Room 107</small></td>
                                <td>Evidence Analysis<br><small>Room 102</small></td>
                                <td>Crime Prevention<br><small>Room 101</small></td>
                                <td>Field Training<br><small>Various</small></td>
                            </tr>
                            <tr>
                                <td class="time-slot">1:00 - 3:00 PM</td>
                                <td>Cybercrime<br><small>Room 103</small></td>
                                <td>Criminal Justice System<br><small>Room 104</small></td>
                                <td>Victimology<br><small>Room 105</small></td>
                                <td>Ethics & Conduct<br><small>Room 106</small></td>
                                <td>Practical Exercise<br><small>Gym</small></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- Grade Request Section -->
        <section class="grade-request-section">
            <h2 class="section-title">
                <i class="fas fa-clipboard-list"></i>
                Grade Requests
            </h2>
            <div class="table-wrapper">
                <table class="grade-request-table">
                    <thead>
                        <tr>
                            <th>Faculty Name</th>
                            <th>Subject</th>
                            <th>Request Type</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="gradeRequestsBody">
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
