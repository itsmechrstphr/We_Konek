/**
 * Student Information System - Admin Dashboard
 * Main JavaScript file for dashboard functionality
 * Enhanced with sidebar-aware layout adjustments
 */

// ==========================================
// STATIC DATA (Simulating database content)
// ==========================================

// Event data - Change hasUpcomingEvent to false to see fallback
const eventData = {
    hasUpcomingEvent: true,
    event: {
        title: "Annual Science Fair 2024",
        subtitle: "Innovation and Discovery Showcase",
        date: "March 15, 2024",
        time: "9:00 AM - 5:00 PM",
        location: "Main Auditorium",
        description: "Join us for our biggest academic event of the year featuring student projects from all departments."
    }
};

// Announcements data
const announcements = [
    {
        id: 1,
        title: "Campus Closure - Spring Break",
        description: "The campus will be closed from March 20-24 for spring break. All classes and administrative offices will resume on March 25.",
        category: "suspension",
        categoryLabel: "Suspension"
    },
    {
        id: 2,
        title: "Free Health Checkup for Students",
        description: "The health center is offering free medical checkups for all students this month. Schedule your appointment at the clinic.",
        category: "checkup",
        categoryLabel: "Checkup"
    },
    {
        id: 3,
        title: "Mid-Term Report Cards Available",
        description: "Mid-term grades have been released. Students can view their report cards through the student portal starting today.",
        category: "report",
        categoryLabel: "Report"
    },
    {
        id: 4,
        title: "Emergency Drill - March 8",
        description: "A campus-wide emergency drill will be conducted on March 8 at 10:00 AM. All students and staff must participate.",
        category: "suspension",
        categoryLabel: "Suspension"
    },
    {
        id: 5,
        title: "COVID-19 Vaccination Drive",
        description: "Free COVID-19 booster shots will be available at the health center from March 10-14. Please bring your vaccination card.",
        category: "checkup",
        categoryLabel: "Checkup"
    },
    {
        id: 6,
        title: "Academic Performance Review",
        description: "Faculty members are requested to submit their department performance reports by March 5 for the quarterly review meeting.",
        category: "report",
        categoryLabel: "Report"
    }
];

// Grade requests data
const gradeRequests = [
    {
        id: 1,
        faculty: "Dr. Sarah Johnson",
        subject: "Database Systems",
        requestType: "Grade Submission",
        status: "pending"
    },
    {
        id: 2,
        faculty: "Prof. Michael Chen",
        subject: "Educational Psychology",
        requestType: "Grade Revision",
        status: "approved"
    },
    {
        id: 3,
        faculty: "Dr. Emily Rodriguez",
        subject: "Criminal Law",
        requestType: "Grade Submission",
        status: "pending"
    },
    {
        id: 4,
        faculty: "Prof. David Williams",
        subject: "Web Development",
        requestType: "Grade Correction",
        status: "rejected"
    },
    {
        id: 5,
        faculty: "Dr. Lisa Anderson",
        subject: "Child Development",
        requestType: "Grade Submission",
        status: "approved"
    },
    {
        id: 6,
        faculty: "Prof. James Martinez",
        subject: "Forensic Science",
        requestType: "Grade Revision",
        status: "pending"
    }
];

// ==========================================
// SIDEBAR AWARENESS & LAYOUT MANAGER
// ==========================================

class DashboardLayoutManager {
    constructor() {
        this.sidebar = document.getElementById('sidebar');
        this.body = document.body;
        this.isDesktop = window.innerWidth > 1024;
        
        if (this.sidebar) {
            this.initializeSidebarObserver();
            this.handleWindowResize();
        }
    }

    /**
     * Initialize MutationObserver to watch sidebar state changes
     */
    initializeSidebarObserver() {
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                    this.adjustBodyPadding();
                }
            });
        });

        observer.observe(this.sidebar, {
            attributes: true,
            attributeFilter: ['class']
        });

        // Initial adjustment
        this.adjustBodyPadding();
    }

    /**
     * Adjust body padding based on sidebar state
     */
    adjustBodyPadding() {
        if (!this.isDesktop) return;

        const isActive = this.sidebar.classList.contains('active');
        const isCollapsed = this.sidebar.classList.contains('collapsed');

        // Force reflow for smooth transitions
        const dashboardContainer = document.querySelector('.dashboard-container');
        if (dashboardContainer) {
            void dashboardContainer.offsetWidth;
        }

        // Dispatch custom event for other components to react
        const event = new CustomEvent('sidebarStateChanged', {
            detail: {
                isActive: isActive,
                isCollapsed: isCollapsed,
                width: isActive ? 280 : 72
            }
        });
        document.dispatchEvent(event);
    }

    /**
     * Handle window resize events
     */
    handleWindowResize() {
        let resizeTimer;

        window.addEventListener('resize', () => {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(() => {
                const wasDesktop = this.isDesktop;
                this.isDesktop = window.innerWidth > 1024;

                if (wasDesktop !== this.isDesktop) {
                    this.adjustBodyPadding();
                    this.adjustGridLayouts();
                }
            }, 150);
        });
    }

    /**
     * Adjust grid layouts based on available space
     */
    adjustGridLayouts() {
        const quickActionsGrid = document.querySelector('.quick-actions-grid');
        const announcementsGrid = document.querySelector('.announcements-grid');

        // Trigger reflow for smooth transitions
        if (quickActionsGrid) {
            void quickActionsGrid.offsetWidth;
        }
        if (announcementsGrid) {
            void announcementsGrid.offsetWidth;
        }
    }
}

// ==========================================
// MODAL MANAGEMENT
// ==========================================

class ModalManager {
    constructor() {
        this.modalOverlay = document.getElementById('modalOverlay');
        this.modalTitle = document.getElementById('modalTitle');
        this.modalBody = document.getElementById('modalBody');
        this.modalClose = document.getElementById('modalClose');
        this.modalCancel = document.getElementById('modalCancel');
        this.modalSubmit = document.getElementById('modalSubmit');

        if (this.modalOverlay) {
            this.initializeEventListeners();
        }
    }

    /**
     * Initialize modal event listeners
     */
    initializeEventListeners() {
        // Close button
        this.modalClose?.addEventListener('click', () => this.closeModal());

        // Cancel button
        this.modalCancel?.addEventListener('click', () => this.closeModal());

        // Click outside modal to close
        this.modalOverlay.addEventListener('click', (e) => {
            if (e.target === this.modalOverlay) {
                this.closeModal();
            }
        });

        // Submit button - placeholder action
        this.modalSubmit?.addEventListener('click', () => {
            this.showSuccessNotification('Form submitted successfully!');
            this.closeModal();
        });

        // Close modal on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.modalOverlay.classList.contains('active')) {
                this.closeModal();
            }
        });
    }

    /**
     * Open modal with specific content
     * @param {string} title - Modal title
     * @param {string} content - Modal body HTML content
     */
    openModal(title, content) {
        this.modalTitle.textContent = title;
        this.modalBody.innerHTML = content;
        this.modalOverlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    /**
     * Close modal
     */
    closeModal() {
        this.modalOverlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    /**
     * Show success notification
     * @param {string} message - Notification message
     */
    showSuccessNotification(message) {
        alert(message + ' (This is a static demo)');
    }

    /**
     * Get form content based on action type
     * @param {string} action - Action type
     * @returns {string} HTML form content
     */
    getFormContent(action) {
        const forms = {
            'add-user': `
                <div class="form-group">
                    <label class="form-label">User Type</label>
                    <select class="form-select">
                        <option>Student</option>
                        <option>Faculty</option>
                        <option>Admin</option>
                        <option>Staff</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <input type="text" class="form-input" placeholder="Enter full name">
                </div>
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" class="form-input" placeholder="Enter email address">
                </div>
                <div class="form-group">
                    <label class="form-label">Department</label>
                    <select class="form-select">
                        <option>CBAT.COM</option>
                        <option>COTE</option>
                        <option>CRIM</option>
                    </select>
                </div>
            `,
            'create-schedule': `
                <div class="form-group">
                    <label class="form-label">Department</label>
                    <select class="form-select">
                        <option>CBAT.COM</option>
                        <option>COTE</option>
                        <option>CRIM</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Course Name</label>
                    <input type="text" class="form-input" placeholder="Enter course name">
                </div>
                <div class="form-group">
                    <label class="form-label">Day</label>
                    <select class="form-select">
                        <option>Monday</option>
                        <option>Tuesday</option>
                        <option>Wednesday</option>
                        <option>Thursday</option>
                        <option>Friday</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Time Slot</label>
                    <select class="form-select">
                        <option>8:00 - 10:00 AM</option>
                        <option>10:00 - 12:00 PM</option>
                        <option>1:00 - 3:00 PM</option>
                        <option>3:00 - 5:00 PM</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Room</label>
                    <input type="text" class="form-input" placeholder="Enter room number">
                </div>
            `,
            'add-event': `
                <div class="form-group">
                    <label class="form-label">Event Title</label>
                    <input type="text" class="form-input" placeholder="Enter event title">
                </div>
                <div class="form-group">
                    <label class="form-label">Event Date</label>
                    <input type="date" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Event Time</label>
                    <input type="time" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Location</label>
                    <input type="text" class="form-input" placeholder="Enter location">
                </div>
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea class="form-textarea" placeholder="Enter event description"></textarea>
                </div>
            `,
            'post-announcement': `
                <div class="form-group">
                    <label class="form-label">Announcement Title</label>
                    <input type="text" class="form-input" placeholder="Enter announcement title">
                </div>
                <div class="form-group">
                    <label class="form-label">Category</label>
                    <select class="form-select">
                        <option>School Suspension</option>
                        <option>Student Checkup</option>
                        <option>School Report</option>
                        <option>General</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Target Audience</label>
                    <select class="form-select">
                        <option>All Users</option>
                        <option>Students Only</option>
                        <option>Faculty Only</option>
                        <option>Specific Department</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Message</label>
                    <textarea class="form-textarea" placeholder="Enter announcement message"></textarea>
                </div>
            `,
            'generate-report': `
                <div class="form-group">
                    <label class="form-label">Report Type</label>
                    <select class="form-select">
                        <option>Student Enrollment Report</option>
                        <option>Grade Summary Report</option>
                        <option>Attendance Report</option>
                        <option>Faculty Performance Report</option>
                        <option>Department Analytics</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Date Range</label>
                    <select class="form-select">
                        <option>Current Semester</option>
                        <option>Last 30 Days</option>
                        <option>Last Quarter</option>
                        <option>Last Year</option>
                        <option>Custom Range</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Department (Optional)</label>
                    <select class="form-select">
                        <option>All Departments</option>
                        <option>CBAT.COM</option>
                        <option>COTE</option>
                        <option>CRIM</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Export Format</label>
                    <select class="form-select">
                        <option>PDF</option>
                        <option>Excel (XLSX)</option>
                        <option>CSV</option>
                    </select>
                </div>
            `
        };

        return forms[action] || '<p>Form not available</p>';
    }
}

// ==========================================
// DATE DISPLAY
// ==========================================

/**
 * Display current date in welcome section
 */
function displayCurrentDate() {
    const dateElement = document.getElementById('currentDate');
    if (!dateElement) return;

    const options = { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    };
    
    const today = new Date();
    dateElement.textContent = today.toLocaleDateString('en-US', options);
}

// ==========================================
// EVENT HERO SECTION
// ==========================================

/**
 * Render event hero section based on event data
 */
function renderEventHero() {
    const heroContent = document.getElementById('heroContent');
    
    if (!heroContent) return;

    if (eventData.hasUpcomingEvent) {
        heroContent.innerHTML = `
            <h2 class="hero-title">${eventData.event.title}</h2>
            <p class="hero-subtitle">${eventData.event.subtitle}</p>
            <div class="hero-date">
                <i class="far fa-calendar"></i>
                <span>${eventData.event.date} | ${eventData.event.time}</span>
            </div>
            <div class="hero-date">
                <i class="fas fa-map-marker-alt"></i>
                <span>${eventData.event.location}</span>
            </div>
        `;
        heroContent.classList.remove('no-event');
    } else {
        heroContent.innerHTML = `
            <h2 class="hero-title">No Upcoming Events</h2>
            <p class="hero-description">There are currently no scheduled events. Check back later for updates on upcoming school activities and announcements.</p>
        `;
        heroContent.classList.add('no-event');
    }
}

// ==========================================
// ANNOUNCEMENTS SECTION
// ==========================================

/**
 * Render announcements grid
 */
function renderAnnouncements() {
    const grid = document.getElementById('announcementsGrid');
    
    if (!grid) return;

    const announcementsHTML = announcements.map(announcement => `
        <div class="announcement-card">
            <div class="announcement-header">
                <div>
                    <h3 class="announcement-title">${announcement.title}</h3>
                </div>
                <span class="announcement-badge badge-${announcement.category}">
                    ${announcement.categoryLabel}
                </span>
            </div>
            <p class="announcement-description">${announcement.description}</p>
        </div>
    `).join('');

    grid.innerHTML = announcementsHTML;
}

// ==========================================
// GRADE REQUESTS SECTION
// ==========================================

/**
 * Render grade requests table
 */
function renderGradeRequests() {
    const tbody = document.getElementById('gradeRequestsBody');
    
    if (!tbody) return;

    const requestsHTML = gradeRequests.map(request => `
        <tr>
            <td>${request.faculty}</td>
            <td>${request.subject}</td>
            <td>${request.requestType}</td>
            <td>
                <span class="status-badge status-${request.status}">
                    ${request.status.charAt(0).toUpperCase() + request.status.slice(1)}
                </span>
            </td>
            <td>
                <div class="action-buttons">
                    <button class="btn-view" onclick="viewRequest(${request.id})">View</button>
                    <button class="btn-approve" onclick="approveRequest(${request.id})">Approve</button>
                    <button class="btn-reject" onclick="rejectRequest(${request.id})">Reject</button>
                </div>
            </td>
        </tr>
    `).join('');

    tbody.innerHTML = requestsHTML;
}

// ==========================================
// GRADE REQUEST ACTIONS
// ==========================================

/**
 * View grade request details
 * @param {number} id - Request ID
 */
function viewRequest(id) {
    const request = gradeRequests.find(r => r.id === id);
    if (request) {
        alert(`Viewing request from ${request.faculty}\nSubject: ${request.subject}\nType: ${request.requestType}\nStatus: ${request.status}`);
    }
}

/**
 * Approve grade request
 * @param {number} id - Request ID
 */
function approveRequest(id) {
    const request = gradeRequests.find(r => r.id === id);
    if (request) {
        if (confirm(`Approve grade request from ${request.faculty}?`)) {
            request.status = 'approved';
            renderGradeRequests();
            alert('Request approved successfully!');
        }
    }
}

/**
 * Reject grade request
 * @param {number} id - Request ID
 */
function rejectRequest(id) {
    const request = gradeRequests.find(r => r.id === id);
    if (request) {
        if (confirm(`Reject grade request from ${request.faculty}?`)) {
            request.status = 'rejected';
            renderGradeRequests();
            alert('Request rejected.');
        }
    }
}

// ==========================================
// INITIALIZATION
// ==========================================

/**
 * Initialize dashboard on page load
 */
document.addEventListener('DOMContentLoaded', () => {
    // Initialize layout manager for sidebar awareness
    const layoutManager = new DashboardLayoutManager();
    
    // Initialize modal manager
    const modalManager = new ModalManager();

    // Display current date
    displayCurrentDate();

    // Render dynamic content
    renderEventHero();
    renderAnnouncements();
    renderGradeRequests();

    // Setup quick action buttons
    const actionButtons = document.querySelectorAll('.action-btn');
    actionButtons.forEach(button => {
        button.addEventListener('click', () => {
            const action = button.getAttribute('data-action');
            const actionText = button.querySelector('.action-text').textContent;
            const formContent = modalManager.getFormContent(action);
            modalManager.openModal(actionText, formContent);
        });
    });

    // Listen for sidebar state changes
    document.addEventListener('sidebarStateChanged', (e) => {
        console.log('Sidebar state changed:', e.detail);
    });

    console.log('Dashboard initialized with sidebar-aware layout management');
});

// ==========================================
// UTILITY FUNCTIONS
// ==========================================

/**
 * Format date to readable string
 * @param {Date} date - Date object
 * @returns {string} Formatted date string
 */
function formatDate(date) {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return date.toLocaleDateString('en-US', options);
}

/**
 * Simulate data refresh (for future implementation)
 */
function refreshDashboard() {
    console.log('Refreshing dashboard data...');
    displayCurrentDate();
    renderEventHero();
    renderAnnouncements();
    renderGradeRequests();
}

// Export functions for inline onclick handlers
window.viewRequest = viewRequest;
window.approveRequest = approveRequest;
window.rejectRequest = rejectRequest;