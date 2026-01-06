/**
 * We Konek Landing Page JavaScript
 * Handles navigation, modal, and user interactions
 */

// Initialize on DOM content loaded
document.addEventListener('DOMContentLoaded', function() {
  initializeMobileMenu();
  initializeModal();
  initializeNewsletterForm();
  initializeSmoothScroll();
});

/**
 * Mobile Menu Functionality
 * Handles hamburger menu toggle and responsive navigation
 */
function initializeMobileMenu() {
  const menuToggle = document.getElementById('menuToggle');
  const mobileMenu = document.getElementById('mobileMenu');

  if (!menuToggle || !mobileMenu) return;

  // Toggle mobile menu
  menuToggle.addEventListener('click', function(event) {
    event.stopPropagation();
    toggleMenu();
  });

  // Close menu when a link is clicked
  const mobileLinks = document.querySelectorAll('.mobile-nav-link');
  mobileLinks.forEach(link => {
    link.addEventListener('click', function() {
      closeMenu();
    });
  });

  // Close menu when clicking outside
  document.addEventListener('click', function(event) {
    const nav = document.querySelector('nav');
    const isClickInsideNav = nav && nav.contains(event.target);
    const isClickOnHamburger = menuToggle.contains(event.target);

    if (!isClickInsideNav && !isClickOnHamburger && mobileMenu.classList.contains('active')) {
      closeMenu();
    }
  });

  // Close menu on escape key
  document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape' && mobileMenu.classList.contains('active')) {
      closeMenu();
    }
  });

  /**
   * Toggle menu open/close state
   */
  function toggleMenu() {
    mobileMenu.classList.toggle('active');
    menuToggle.classList.toggle('active');
    
    // Update aria-expanded for accessibility
    const isExpanded = mobileMenu.classList.contains('active');
    menuToggle.setAttribute('aria-expanded', isExpanded);
  }

  /**
   * Close mobile menu
   */
  function closeMenu() {
    mobileMenu.classList.remove('active');
    menuToggle.classList.remove('active');
    menuToggle.setAttribute('aria-expanded', 'false');
  }
}

/**
 * Modal Functionality
 * Handles login role selection modal
 */
function initializeModal() {
  const modal = document.getElementById('loginModal');
  
  if (!modal) return;

  // Close modal when clicking on backdrop
  window.addEventListener('click', function(event) {
    if (event.target === modal) {
      closeModal();
    }
  });

  // Close modal on escape key
  document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape' && modal.style.display === 'flex') {
      closeModal();
    }
  });
}

/**
 * Open login modal
 * @param {Event} event - Click event
 */
function openModal(event) {
  if (event) {
    event.preventDefault();
  }
  const modal = document.getElementById('loginModal');
  if (modal) {
    modal.style.display = 'flex';
    // Focus trap for accessibility
    modal.querySelector('.close-modal').focus();
  }
}

/**
 * Close login modal
 */
function closeModal() {
  const modal = document.getElementById('loginModal');
  if (modal) {
    modal.style.display = 'none';
  }
}

/**
 * Newsletter Form Handler
 * Handles email subscription
 */
function initializeNewsletterForm() {
  const form = document.getElementById('newsletterForm');
  
  if (!form) return;

  form.addEventListener('submit', function(event) {
    event.preventDefault();
    
    const emailInput = form.querySelector('input[type="email"]');
    const email = emailInput.value;

    if (validateEmail(email)) {
      // Show success message
      showNotification('Thank you for subscribing! You will receive updates at ' + email, 'success');
      
      // Reset form
      form.reset();
      
      // In a real application, you would send this to a server
      // sendToServer(email);
    } else {
      showNotification('Please enter a valid email address.', 'error');
    }
  });
}

/**
 * Validate email address
 * @param {string} email - Email to validate
 * @returns {boolean} - True if valid
 */
function validateEmail(email) {
  const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return re.test(String(email).toLowerCase());
}

/**
 * Show notification message
 * @param {string} message - Message to display
 * @param {string} type - Type of notification (success/error)
 */
function showNotification(message, type) {
  // Create notification element
  const notification = document.createElement('div');
  notification.className = `notification notification-${type}`;
  notification.textContent = message;
  
  // Style the notification
  Object.assign(notification.style, {
    position: 'fixed',
    top: '80px',
    right: '20px',
    padding: '16px 24px',
    backgroundColor: type === 'success' ? '#225B13' : '#dc2626',
    color: '#ffffff',
    borderRadius: '8px',
    boxShadow: '0 10px 15px -3px rgba(0, 0, 0, 0.1)',
    zIndex: '1000',
    animation: 'slideIn 0.3s ease',
    maxWidth: '400px'
  });

  // Add to document
  document.body.appendChild(notification);

  // Remove after 5 seconds
  setTimeout(() => {
    notification.style.animation = 'slideOut 0.3s ease';
    setTimeout(() => {
      notification.remove();
    }, 300);
  }, 5000);
}

/**
 * Smooth scroll to section
 * @param {string} sectionId - ID of section to scroll to
 */
function scrollToSection(sectionId) {
  const section = document.getElementById(sectionId);
  if (section) {
    const offsetTop = section.offsetTop - 64; // Account for fixed navbar
    window.scrollTo({
      top: offsetTop,
      behavior: 'smooth'
    });
  }
}

/**
 * Initialize smooth scroll for navigation links
 */
function initializeSmoothScroll() {
  const links = document.querySelectorAll('a[href^="#"]');
  
  links.forEach(link => {
    link.addEventListener('click', function(event) {
      const href = this.getAttribute('href');
      
      // Skip if it's just "#" (used for modal triggers)
      if (href === '#') return;
      
      event.preventDefault();
      const targetId = href.substring(1);
      scrollToSection(targetId);
    });
  });
}

/**
 * Add animation styles dynamically
 */
const style = document.createElement('style');
style.textContent = `
  @keyframes slideIn {
    from {
      transform: translateX(100%);
      opacity: 0;
    }
    to {
      transform: translateX(0);
      opacity: 1;
    }
  }

  @keyframes slideOut {
    from {
      transform: translateX(0);
      opacity: 1;
    }
    to {
      transform: translateX(100%);
      opacity: 0;
    }
  }
`;
document.head.appendChild(style);

// Make functions globally accessible for inline onclick handlers
window.openModal = openModal;
window.closeModal = closeModal;
window.scrollToSection = scrollToSection;