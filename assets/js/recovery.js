/**
 * We Konek Password Recovery JavaScript
 * Handles password recovery form validation and interactions
 */

// Initialize on DOM content loaded
document.addEventListener('DOMContentLoaded', function() {
  initializePasswordToggles();
  initializeFormValidation();
  initializePasswordStrength();
  enhanceAccessibility();
  autoHideMessages();
});

/**
 * Initialize Password Toggle Functionality
 * Shows/hides password fields
 */
function initializePasswordToggles() {
  const toggleButtons = document.querySelectorAll('.toggle-password');
  
  toggleButtons.forEach(button => {
    button.addEventListener('click', function() {
      const targetId = this.getAttribute('data-target');
      const passwordInput = document.getElementById(targetId);
      const icon = this.querySelector('i');
      
      if (!passwordInput) return;
      
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
  });
}

/**
 * Initialize Form Validation
 * Real-time validation for form fields
 */
function initializeFormValidation() {
  // Request form validation
  const requestForm = document.getElementById('requestForm');
  if (requestForm) {
    const emailInput = document.getElementById('email');
    
    // Remove error state on input
    if (emailInput) {
      emailInput.addEventListener('input', function() {
        this.classList.remove('error');
        removeFieldError(this);
      });
      
      // Validate on blur
      emailInput.addEventListener('blur', function() {
        validateEmail(this);
      });
    }
    
    // Form submission
    requestForm.addEventListener('submit', function(e) {
      const emailInput = document.getElementById('email');
      
      if (!validateEmail(emailInput)) {
        e.preventDefault();
        emailInput.focus();
        return false;
      }
      
      // Show loading state
      const submitBtn = this.querySelector('.btn-submit');
      if (submitBtn) {
        submitBtn.classList.add('loading');
        submitBtn.disabled = true;
      }
    });
  }
  
  // Reset form validation
  const resetForm = document.getElementById('resetForm');
  if (resetForm) {
    const newPasswordInput = document.getElementById('new_password');
    const confirmPasswordInput = document.getElementById('confirm_password');
    
    // Remove error state on input
    [newPasswordInput, confirmPasswordInput].forEach(input => {
      if (input) {
        input.addEventListener('input', function() {
          this.classList.remove('error');
          this.classList.remove('success');
          removeFieldError(this);
        });
      }
    });
    
    // Validate on blur
    if (newPasswordInput) {
      newPasswordInput.addEventListener('blur', function() {
        validatePassword(this);
      });
    }
    
    if (confirmPasswordInput) {
      confirmPasswordInput.addEventListener('blur', function() {
        validatePasswordMatch();
      });
    }
    
    // Form submission
    resetForm.addEventListener('submit', function(e) {
      let isValid = true;
      
      if (!validatePassword(newPasswordInput)) {
        isValid = false;
      }
      
      if (!validatePasswordMatch()) {
        isValid = false;
      }
      
      if (!isValid) {
        e.preventDefault();
        const firstError = this.querySelector('.error');
        if (firstError) {
          firstError.focus();
        }
        return false;
      }
      
      // Show loading state
      const submitBtn = this.querySelector('.btn-submit');
      if (submitBtn) {
        submitBtn.classList.add('loading');
        submitBtn.disabled = true;
      }
    });
  }
}

/**
 * Validate Email
 * @param {HTMLElement} input - Email input element
 * @returns {boolean} - True if valid
 */
function validateEmail(input) {
  if (!input) return false;
  
  const email = input.value.trim();
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  
  if (!email) {
    input.classList.add('error');
    showFieldError(input, 'Email address is required');
    return false;
  }
  
  if (!emailRegex.test(email)) {
    input.classList.add('error');
    showFieldError(input, 'Please enter a valid email address');
    return false;
  }
  
  input.classList.remove('error');
  input.classList.add('success');
  removeFieldError(input);
  return true;
}

/**
 * Validate Password
 * @param {HTMLElement} input - Password input element
 * @returns {boolean} - True if valid
 */
function validatePassword(input) {
  if (!input) return false;
  
  const password = input.value;
  
  if (!password) {
    input.classList.add('error');
    showFieldError(input, 'Password is required');
    return false;
  }
  
  if (password.length < 6) {
    input.classList.add('error');
    showFieldError(input, 'Password must be at least 6 characters long');
    return false;
  }
  
  input.classList.remove('error');
  input.classList.add('success');
  removeFieldError(input);
  return true;
}

/**
 * Validate Password Match
 * @returns {boolean} - True if passwords match
 */
function validatePasswordMatch() {
  const newPasswordInput = document.getElementById('new_password');
  const confirmPasswordInput = document.getElementById('confirm_password');
  
  if (!newPasswordInput || !confirmPasswordInput) return false;
  
  const newPassword = newPasswordInput.value;
  const confirmPassword = confirmPasswordInput.value;
  
  if (!confirmPassword) {
    confirmPasswordInput.classList.add('error');
    showFieldError(confirmPasswordInput, 'Please confirm your password');
    return false;
  }
  
  if (newPassword !== confirmPassword) {
    confirmPasswordInput.classList.add('error');
    showFieldError(confirmPasswordInput, 'Passwords do not match');
    return false;
  }
  
  confirmPasswordInput.classList.remove('error');
  confirmPasswordInput.classList.add('success');
  removeFieldError(confirmPasswordInput);
  return true;
}

/**
 * Show Field Error
 * @param {HTMLElement} input - Input element
 * @param {string} message - Error message
 */
function showFieldError(input, message) {
  if (!input) return;
  
  removeFieldError(input);
  
  const errorElement = document.createElement('span');
  errorElement.className = 'error-text';
  errorElement.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${message}`;
  
  const formGroup = input.closest('.form-group');
  if (formGroup) {
    formGroup.appendChild(errorElement);
  }
}

/**
 * Remove Field Error
 * @param {HTMLElement} input - Input element
 */
function removeFieldError(input) {
  if (!input) return;
  
  const formGroup = input.closest('.form-group');
  if (formGroup) {
    const errorElement = formGroup.querySelector('.error-text');
    if (errorElement) {
      errorElement.remove();
    }
  }
}

/**
 * Initialize Password Strength Indicator
 */
function initializePasswordStrength() {
  const newPasswordInput = document.getElementById('new_password');
  const strengthIndicator = document.getElementById('passwordStrength');
  
  if (!newPasswordInput || !strengthIndicator) return;
  
  const strengthFill = strengthIndicator.querySelector('.strength-fill');
  const strengthText = strengthIndicator.querySelector('.strength-text strong');
  
  newPasswordInput.addEventListener('input', function() {
    const password = this.value;
    const strength = calculatePasswordStrength(password);
    
    // Remove previous classes
    strengthFill.classList.remove('weak', 'medium', 'strong');
    strengthText.classList.remove('weak', 'medium', 'strong');
    
    if (password.length === 0) {
      strengthFill.style.width = '0%';
      strengthText.textContent = '-';
      return;
    }
    
    // Apply new classes and text
    strengthFill.classList.add(strength.level);
    strengthText.classList.add(strength.level);
    strengthText.textContent = strength.text;
  });
}

/**
 * Calculate Password Strength
 * @param {string} password - Password to evaluate
 * @returns {Object} - Strength level and text
 */
function calculatePasswordStrength(password) {
  let score = 0;
  
  if (password.length >= 6) score++;
  if (password.length >= 8) score++;
  if (password.length >= 12) score++;
  if (/[a-z]/.test(password)) score++;
  if (/[A-Z]/.test(password)) score++;
  if (/[0-9]/.test(password)) score++;
  if (/[^a-zA-Z0-9]/.test(password)) score++;
  
  if (score <= 2) {
    return { level: 'weak', text: 'Weak' };
  } else if (score <= 4) {
    return { level: 'medium', text: 'Medium' };
  } else {
    return { level: 'strong', text: 'Strong' };
  }
}

/**
 * Show Notification Toast
 * @param {string} message - Message to display
 * @param {string} type - Type of notification (success/error)
 */
function showNotification(message, type) {
  // Remove existing notifications
  const existingNotification = document.querySelector('.notification-toast');
  if (existingNotification) {
    existingNotification.remove();
  }
  
  // Create notification element
  const notification = document.createElement('div');
  notification.className = `notification-toast ${type}`;
  
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
    notification.style.animation = 'slideOutRight 0.3s ease';
    setTimeout(() => {
      notification.remove();
    }, 300);
  }, 5000);
}

/**
 * Auto-hide Messages
 * Automatically hide success/error messages after delay
 */
function autoHideMessages() {
  const messageBox = document.getElementById('messageBox');
  
  if (messageBox && messageBox.classList.contains('success')) {
    setTimeout(() => {
      messageBox.style.animation = 'slideDown 0.3s ease reverse';
      setTimeout(() => {
        messageBox.style.display = 'none';
      }, 300);
    }, 10000); // Hide after 10 seconds
  }
}

/**
 * Enhance Accessibility
 * Adds keyboard navigation and ARIA attributes
 */
function enhanceAccessibility() {
  // Add live region for error messages
  const formGroups = document.querySelectorAll('.form-group');
  formGroups.forEach(group => {
    group.setAttribute('role', 'group');
  });
  
  // Add aria-describedby for inputs with hints
  const inputs = document.querySelectorAll('.input-with-icon input');
  inputs.forEach(input => {
    const hint = input.parentElement.nextElementSibling;
    if (hint && hint.classList.contains('field-hint')) {
      const hintId = 'hint-' + input.id;
      hint.id = hintId;
      input.setAttribute('aria-describedby', hintId);
    }
  });
  
  // Handle Enter key on links
  const links = document.querySelectorAll('a');
  links.forEach(link => {
    link.addEventListener('keydown', function(e) {
      if (e.key === 'Enter') {
        this.click();
      }
    });
  });
}

/**
 * Prevent Form Resubmission
 * Prevents duplicate submissions on page refresh
 */
window.addEventListener('load', function() {
  if (window.performance && window.performance.navigation.type === 1) {
    // Page was refreshed
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
      const submitBtn = form.querySelector('.btn-submit');
      if (submitBtn) {
        submitBtn.classList.remove('loading');
        submitBtn.disabled = false;
      }
    });
  }
});

/**
 * Handle Browser Back Button
 * Reset form state when navigating back
 */
window.addEventListener('pageshow', function(event) {
  if (event.persisted) {
    // Page was loaded from cache
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
      const submitBtn = form.querySelector('.btn-submit');
      if (submitBtn) {
        submitBtn.classList.remove('loading');
        submitBtn.disabled = false;
      }
    });
  }
});

/**
 * Copy to Clipboard Functionality
 * For sharing reset links (if needed in admin panel)
 */
function copyToClipboard(text) {
  if (navigator.clipboard && window.isSecureContext) {
    return navigator.clipboard.writeText(text).then(() => {
      showNotification('Copied to clipboard!', 'success');
    }).catch(() => {
      showNotification('Failed to copy', 'error');
    });
  } else {
    // Fallback for older browsers
    const textArea = document.createElement('textarea');
    textArea.value = text;
    textArea.style.position = 'fixed';
    textArea.style.left = '-999999px';
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    
    try {
      document.execCommand('copy');
      showNotification('Copied to clipboard!', 'success');
    } catch (error) {
      showNotification('Failed to copy', 'error');
    }
    
    document.body.removeChild(textArea);
  }
}

// Make copyToClipboard globally accessible
window.copyToClipboard = copyToClipboard;