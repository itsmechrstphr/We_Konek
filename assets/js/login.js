/**
 * We Konek Login JavaScript
 * Handles login form validation and authentication
 */

// Initialize on DOM content loaded
document.addEventListener('DOMContentLoaded', function() {
  initializeCaptcha();
  initializePasswordToggle();
  initializeFormValidation();
  initializeFormSubmit();
  initializeRememberMe();
  enhanceAccessibility();
});

/**
 * Generate Random Captcha Numbers
 * @returns {Object} - Object with num1 and num2
 */
function generateRandomCaptcha() {
  return {
    num1: Math.floor(Math.random() * 10) + 1,
    num2: Math.floor(Math.random() * 10) + 1
  };
}

/**
 * Initialize Captcha
 * Sets random numbers for captcha
 */
function initializeCaptcha() {
  const captcha = generateRandomCaptcha();
  const captchaNum1Element = document.getElementById('captchaNum1');
  const captchaNum2Element = document.getElementById('captchaNum2');

  if (captchaNum1Element && captchaNum2Element) {
    captchaNum1Element.textContent = captcha.num1;
    captchaNum2Element.textContent = captcha.num2;
  }
}

/**
 * Refresh Captcha
 * Generates new random numbers for captcha
 */
function refreshCaptcha() {
  initializeCaptcha();
}

/**
 * Password Toggle Functionality
 * Shows/hides password field
 */
function initializePasswordToggle() {
  const toggleButton = document.querySelector('.toggle-password');
  
  if (!toggleButton) return;
  
  toggleButton.addEventListener('click', function() {
    const passwordInput = document.getElementById('password');
    const icon = this.querySelector('i');
    
    if (passwordInput.type === 'password') {
      passwordInput.type = 'text';
      icon.classList.remove('fa-eye');
      icon.classList.add('fa-eye-slash');
      this.setAttribute('aria-label', 'Hide password');
    } else {
      passwordInput.type = 'password';
      icon.classList.remove('fa-eye-slash');
      icon.classList.add('fa-eye');
      this.setAttribute('aria-label', 'Show password');
    }
  });
}

/**
 * Form Validation
 * Real-time validation for form fields
 */
function initializeFormValidation() {
  const form = document.getElementById('loginForm');
  if (!form) return;

  const userIdInput = document.getElementById('userId');
  const passwordInput = document.getElementById('password');
  const captchaInput = document.getElementById('captchaInput');

  // Remove error state on input
  [userIdInput, passwordInput, captchaInput].forEach(input => {
    if (input) {
      input.addEventListener('input', function() {
        this.classList.remove('error');
        hideErrorMessage();
        clearCaptchaError();
      });
    }
  });

  // Validate on blur
  if (userIdInput) {
    userIdInput.addEventListener('blur', function() {
      validateUserId(this);
    });
  }

  if (passwordInput) {
    passwordInput.addEventListener('blur', function() {
      validatePassword(this);
    });
  }

  if (captchaInput) {
    captchaInput.addEventListener('blur', function() {
      validateCaptcha(this);
    });
  }
}

/**
 * Validate User ID
 * @param {HTMLElement} input - User ID input element
 * @returns {boolean} - True if valid
 */
function validateUserId(input) {
  if (!input.value.trim()) {
    input.classList.add('error');
    return false;
  }
  
  input.classList.remove('error');
  return true;
}

/**
 * Validate Password
 * @param {HTMLElement} input - Password input element
 * @returns {boolean} - True if valid
 */
function validatePassword(input) {
  if (!input.value) {
    input.classList.add('error');
    return false;
  }

  if (input.value.length < 6) {
    input.classList.add('error');
    showErrorMessage('Password must be at least 6 characters long');
    return false;
  }

  input.classList.remove('error');
  return true;
}

/**
 * Validate Captcha
 * @param {HTMLElement} input - Captcha input element
 * @returns {boolean} - True if valid
 */
function validateCaptcha(input) {
  const num1 = parseInt(document.getElementById('captchaNum1').textContent);
  const num2 = parseInt(document.getElementById('captchaNum2').textContent);
  const correctAnswer = num1 + num2;
  const userAnswer = parseInt(input.value);

  if (userAnswer !== correctAnswer) {
    input.classList.add('error');
    refreshCaptcha(); // Refresh captcha on incorrect answer
    return false;
  }

  input.classList.remove('error');
  return true;
}

/**
 * Form Submission Handler
 * Handles form validation and submission
 */
function initializeFormSubmit() {
  const form = document.getElementById('loginForm');
  
  if (!form) return;
  
  form.addEventListener('submit', function(e) {
    e.preventDefault();

    // Hide any existing error messages
    hideErrorMessage();
    clearCaptchaError();

    // Get form values
    const userId = document.getElementById('userId');
    const password = document.getElementById('password');
    const captchaInput = document.getElementById('captchaInput');
    const role = document.querySelector('input[name="role"]').value;

    // Validate inputs
    let isValid = true;

    if (!validateUserId(userId)) {
      isValid = false;
      showErrorMessage(`Please enter your ${role === 'student' ? 'Student' : 'Faculty'} ID`);
    }

    if (!validatePassword(password)) {
      isValid = false;
      if (!userId.classList.contains('error')) {
        showErrorMessage('Please enter a valid password (at least 6 characters)');
      }
    }

    if (!validateCaptcha(captchaInput)) {
      isValid = false;
      if (!userId.classList.contains('error') && !password.classList.contains('error')) {
        showErrorMessage('Please solve the math problem correctly');
      }
    }

    if (!isValid) {
      // Focus on first error field
      const firstError = form.querySelector('.error');
      if (firstError) {
        firstError.focus();
      }
      return;
    }

    // If validation passes, submit the form
    submitLogin(form, {
      userId: userId.value,
      password: password.value,
      role: role,
      remember: document.getElementById('remember').checked
    });
  });
}

/**
 * Submit Login
 * Handles the login submission process
 * @param {HTMLFormElement} form - The login form
 * @param {Object} credentials - User credentials
 */
function submitLogin(form, credentials) {
  const submitButton = form.querySelector('.btn-login');
  
  // Show loading state
  submitButton.classList.add('loading');
  submitButton.disabled = true;
  
  // Simulate API call
  // In production, replace this with actual API call
  setTimeout(() => {
    // Mock authentication logic
    const isAuthenticated = mockAuthenticate(credentials);
    
    // Remove loading state
    submitButton.classList.remove('loading');
    submitButton.disabled = false;
    
    if (isAuthenticated) {
      // Store authentication info
      if (credentials.remember) {
        localStorage.setItem('rememberedUser', credentials.userId);
        localStorage.setItem('rememberedRole', credentials.role);
      }
      
      // Show success notification
      showNotification('Login successful! Redirecting...', 'success');
      
      // Redirect based on role
      setTimeout(() => {
        if (credentials.role === 'student') {
          window.location.href = '../student/dashboard.php';
        } else {
          window.location.href = '../faculty/dashboard.php';
        }
      }, 1500);
    } else {
      // Show error
      showErrorMessage('Invalid credentials. Please try again.');
      
      // Add error class to inputs
      document.getElementById('userId').classList.add('error');
      document.getElementById('password').classList.add('error');
      
      // Focus on user ID field
      document.getElementById('userId').focus();
    }
  }, 1500);
}

/**
 * Mock Authentication
 * Simulates server-side authentication
 * In production, this would be a real API call
 * @param {Object} credentials - User credentials
 * @returns {boolean} - True if authenticated
 */
function mockAuthenticate(credentials) {
  // Mock credentials for testing
  const mockUsers = {
    student: {
      '2024-001': 'student123',
      '2024-002': 'password',
    },
    faculty: {
      'FAC-001': 'faculty123',
      'FAC-002': 'password',
    }
  };
  
  const users = mockUsers[credentials.role];
  return users && users[credentials.userId] === credentials.password;
}

/**
 * Remember Me Functionality
 * Pre-fills user ID if remembered
 */
function initializeRememberMe() {
  const rememberedUser = localStorage.getItem('rememberedUser');
  const rememberedRole = localStorage.getItem('rememberedRole');
  const currentRole = document.querySelector('input[name="role"]').value;
  
  if (rememberedUser && rememberedRole === currentRole) {
    document.getElementById('userId').value = rememberedUser;
    document.getElementById('remember').checked = true;
  }
}

/**
 * Clear Captcha Error Message
 * Clears the captcha-specific error message
 */
function clearCaptchaError() {
  const captchaError = document.getElementById('error-captcha');
  if (captchaError) {
    captchaError.textContent = '';
    captchaError.style.display = 'none';
  }
}

/**
 * Show Captcha Error Message
 * Displays error message for captcha
 * @param {string} message - Error message to display
 */
function showCaptchaError(message) {
  const captchaError = document.getElementById('error-captcha');
  if (captchaError) {
    captchaError.textContent = message;
    captchaError.style.display = 'flex';
  }
}

/**
 * Validate Captcha
 * @param {HTMLElement} input - Captcha input element
 * @returns {boolean} - True if valid
 */
function validateCaptcha(input) {
  const num1 = parseInt(document.getElementById('captchaNum1').textContent);
  const num2 = parseInt(document.getElementById('captchaNum2').textContent);
  const correctAnswer = num1 + num2;
  const userAnswer = parseInt(input.value);

  if (isNaN(userAnswer) || userAnswer !== correctAnswer) {
    input.classList.add('error');
    showCaptchaError('Incorrect answer. Please try again.');
    refreshCaptcha(); // Refresh captcha on incorrect answer
    return false;
  }

  input.classList.remove('error');
  clearCaptchaError();
  return true;
}

/**
 * Show Notification
 * Displays a notification message
 * @param {string} message - Message to display
 * @param {string} type - Type of notification (success/error)
 */
function showNotification(message, type) {
  // Remove existing notifications
  const existingNotification = document.querySelector('.notification');
  if (existingNotification) {
    existingNotification.remove();
  }
  
  // Create notification element
  const notification = document.createElement('div');
  notification.className = `notification ${type}`;
  
  const icon = document.createElement('i');
  icon.className = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle';
  
  const text = document.createElement('span');
  text.textContent = message;
  
  notification.appendChild(icon);
  notification.appendChild(text);
  
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
 * Handle Forgot Password
 * Displays modal or redirects to password recovery
 * @param {Event} event - Click event
 */
function handleForgotPassword(event) {
  event.preventDefault();
  
  const role = document.querySelector('input[name="role"]').value;
  const roleText = role === 'student' ? 'Student' : 'Faculty';
  
  showNotification(
    `Password recovery for ${roleText} accounts will be available soon. Please contact your administrator.`,
    'error'
  );
}

/**
 * Enhance Accessibility
 * Adds keyboard navigation and ARIA attributes
 */
function enhanceAccessibility() {
  // Add keyboard navigation for role toggle
  const roleToggles = document.querySelectorAll('.role-toggle-btn');
  
  roleToggles.forEach((toggle, index) => {
    toggle.setAttribute('tabindex', toggle.classList.contains('active') ? '0' : '-1');
    
    toggle.addEventListener('keydown', function(e) {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        this.click();
      }
      
      // Arrow key navigation
      if (e.key === 'ArrowLeft' || e.key === 'ArrowRight') {
        e.preventDefault();
        const nextIndex = e.key === 'ArrowRight' ? 
          (index + 1) % roleToggles.length : 
          (index - 1 + roleToggles.length) % roleToggles.length;
        roleToggles[nextIndex].focus();
      }
    });
  });
  
  // Add live region for error messages
  const errorContainer = document.getElementById('errorMessage');
  if (errorContainer) {
    errorContainer.setAttribute('role', 'alert');
    errorContainer.setAttribute('aria-live', 'polite');
  }
}

/**
 * Handle Enter Key on Remember Me
 * Allows toggling checkbox with Enter key
 */
document.addEventListener('DOMContentLoaded', function() {
  const rememberLabel = document.querySelector('.remember-me');
  
  if (rememberLabel) {
    rememberLabel.addEventListener('keydown', function(e) {
      if (e.key === 'Enter') {
        e.preventDefault();
        const checkbox = this.querySelector('input[type="checkbox"]');
        checkbox.checked = !checkbox.checked;
      }
    });
  }
});

// Make function globally accessible for inline onclick handler
window.handleForgotPassword = handleForgotPassword;