class RegisterForm {
  constructor() {
    this.form = document.getElementById('registerForm');
    this.submitBtn = document.getElementById('submitBtn');
    this.captchaNum1 = this.generateRandomNumber();
    this.captchaNum2 = this.generateRandomNumber();
    this.formData = {};
    this.errors = {};
    this.isSubmitting = false;

    this.initializeCaptcha();
    this.initializeEventListeners();
    this.disableStudentNoInitially();
  }

  generateRandomNumber() {
    return Math.floor(Math.random() * 10) + 1;
  }

  initializeCaptcha() {
    this.captchaNum1 = this.generateRandomNumber();
    this.captchaNum2 = this.generateRandomNumber();

    const captchaNum1Element = document.getElementById('captchaNum1');
    const captchaNum2Element = document.getElementById('captchaNum2');

    if (captchaNum1Element && captchaNum2Element) {
      captchaNum1Element.textContent = this.captchaNum1;
      captchaNum2Element.textContent = this.captchaNum2;
    }
  }

  refreshCaptcha() {
    this.initializeCaptcha();
  }

  initializeEventListeners() {
    this.form.addEventListener('submit', (e) => this.handleSubmit(e));

    const inputs = this.form.querySelectorAll('input[data-field]');
    inputs.forEach(input => {
      input.addEventListener('change', (e) => this.handleInputChange(e));
      input.addEventListener('blur', (e) => this.handleInputChange(e));
    });

    const studentTypeRadios = this.form.querySelectorAll('input[name="studentType"]');
    studentTypeRadios.forEach(radio => {
      radio.addEventListener('change', () => this.handleStudentTypeChange());
    });

    const captchaInput = document.getElementById('captchaInput');
    captchaInput.addEventListener('input', () => this.clearCaptchaError());

    // Password strength real-time validation
    const passwordInput = this.form.querySelector('input[name="password"]');
    passwordInput.addEventListener('input', (e) => this.checkPasswordStrength(e.target.value));
  }

  disableStudentNoInitially() {
    const studentNoInput = this.form.querySelector('input[name="studentNo"]');
    studentNoInput.disabled = true;
  }

  handleStudentTypeChange() {
    const studentTypeRadios = this.form.querySelectorAll('input[name="studentType"]');
    const studentNoInput = this.form.querySelector('input[name="studentNo"]');
    const selectedType = Array.from(studentTypeRadios).find(r => r.checked)?.value;

    if (selectedType === 'old') {
      studentNoInput.disabled = false;
      studentNoInput.focus();
    } else {
      studentNoInput.disabled = true;
      studentNoInput.value = '';
    }
  }

  handleInputChange(e) {
    const { name, value } = e.target;
    const field = e.target.getAttribute('data-field');

    if (field) {
      this.formData[field] = value;
      this.clearError(field);
    }
  }

  clearError(fieldName) {
    const errorElement = document.getElementById(`error-${fieldName}`);
    if (errorElement) {
      errorElement.textContent = '';
      const input = this.form.querySelector(`input[data-field="${fieldName}"]`);
      if (input) {
        input.classList.remove('error');
      }
    }
  }

  clearCaptchaError() {
    const errorElement = document.getElementById('error-captcha');
    if (errorElement) {
      errorElement.textContent = '';
    }
    const captchaInput = document.getElementById('captchaInput');
    captchaInput.classList.remove('error');
  }

  setError(fieldName, message) {
    const errorElement = document.getElementById(`error-${fieldName}`);
    if (errorElement) {
      errorElement.textContent = message;
      const input = this.form.querySelector(`input[data-field="${fieldName}"]`);
      if (input) {
        input.classList.add('error');
      }
    }
  }

  /**
   * Enhanced password strength validation
   * Requirements:
   * - Minimum 8 characters
   * - At least one uppercase letter
   * - At least one lowercase letter
   * - At least one number
   * - At least one special character (!@#$%^&*()_+-=[]{}|;:,.<>?)
   */
  checkPasswordStrength(password) {
    const strengthIndicator = document.getElementById('password-strength-indicator');
    const strengthText = document.getElementById('password-strength-text');
    const requirementsList = document.getElementById('password-requirements');

    if (!password) {
      if (strengthIndicator) {
        strengthIndicator.className = 'strength-indicator';
        strengthIndicator.style.width = '0%';
      }
      if (strengthText) strengthText.textContent = '';
      this.updateRequirements(password);
      return;
    }

    const requirements = {
      length: password.length >= 8,
      uppercase: /[A-Z]/.test(password),
      lowercase: /[a-z]/.test(password),
      number: /[0-9]/.test(password),
      special: /[!@#$%^&*()_+\-=\[\]{}|;:,.<>?]/.test(password)
    };

    const metRequirements = Object.values(requirements).filter(Boolean).length;
    let strength = 'weak';
    let strengthPercentage = 0;

    if (metRequirements === 5) {
      strength = 'strong';
      strengthPercentage = 100;
    } else if (metRequirements >= 3) {
      strength = 'medium';
      strengthPercentage = 60;
    } else {
      strength = 'weak';
      strengthPercentage = 30;
    }

    if (strengthIndicator) {
      strengthIndicator.className = `strength-indicator strength-${strength}`;
      strengthIndicator.style.width = `${strengthPercentage}%`;
    }

    if (strengthText) {
      const strengthLabels = {
        weak: 'Weak',
        medium: 'Medium',
        strong: 'Strong'
      };
      strengthText.textContent = strengthLabels[strength];
      strengthText.className = `strength-text strength-${strength}`;
    }

    this.updateRequirements(password);
  }

  updateRequirements(password) {
    const requirements = {
      'req-length': password.length >= 8,
      'req-uppercase': /[A-Z]/.test(password),
      'req-lowercase': /[a-z]/.test(password),
      'req-number': /[0-9]/.test(password),
      'req-special': /[!@#$%^&*()_+\-=\[\]{}|;:,.<>?]/.test(password)
    };

    Object.entries(requirements).forEach(([id, met]) => {
      const element = document.getElementById(id);
      if (element) {
        if (met) {
          element.classList.add('met');
          element.classList.remove('unmet');
        } else {
          element.classList.remove('met');
          element.classList.add('unmet');
        }
      }
    });
  }

  validatePasswordStrength(password) {
    if (!password) {
      return { valid: false, message: 'Password is required' };
    }

    if (password.length < 8) {
      return { valid: false, message: 'Password must be at least 8 characters long' };
    }

    if (!/[A-Z]/.test(password)) {
      return { valid: false, message: 'Password must contain at least one uppercase letter' };
    }

    if (!/[a-z]/.test(password)) {
      return { valid: false, message: 'Password must contain at least one lowercase letter' };
    }

    if (!/[0-9]/.test(password)) {
      return { valid: false, message: 'Password must contain at least one number' };
    }

    if (!/[!@#$%^&*()_+\-=\[\]{}|;:,.<>?]/.test(password)) {
      return { valid: false, message: 'Password must contain at least one special character (!@#$%^&*...)' };
    }

    return { valid: true, message: '' };
  }

  getFormData() {
    const data = {};
    const inputs = this.form.querySelectorAll('input[data-field]');

    inputs.forEach(input => {
      const field = input.getAttribute('data-field');
      if (input.type === 'radio') {
        if (input.checked) {
          data[field] = input.value;
        }
      } else {
        data[field] = input.value;
      }
    });

    return data;
  }

  validateForm() {
    this.errors = {};
    const data = this.getFormData();

    if (!data.schoolYear) {
      this.setError('schoolYear', 'School year is required');
    }
    if (!data.term) {
      this.setError('term', 'Term is required');
    }
    if (!data.studentType) {
      this.setError('studentType', 'Student type is required');
    }
    if (data.studentType === 'old' && !data.studentNo) {
      this.setError('studentNo', 'Student number is required');
    }
    if (!data.course) {
      this.setError('course', 'Course is required');
    }
    if (!data.lastName) {
      this.setError('lastName', 'Last name is required');
    }
    if (!data.firstName) {
      this.setError('firstName', 'First name is required');
    }
    
    // Enhanced password validation
    const passwordValidation = this.validatePasswordStrength(data.password);
    if (!passwordValidation.valid) {
      this.setError('password', passwordValidation.message);
    }

    if (data.password !== data.confirmPassword) {
      this.setError('confirmPassword', 'Passwords do not match');
    }
    if (!data.contactNo) {
      this.setError('contactNo', 'Contact number is required');
    }
    if (!data.dateOfBirth) {
      this.setError('dateOfBirth', 'Date of birth is required');
    }
    if (!data.sex) {
      this.setError('sex', 'Sex is required');
    }
    if (!data.childStatus) {
      this.setError('childStatus', 'Civil status is required');
    }
    if (!data.emailAddress) {
      this.setError('emailAddress', 'Email address is required');
    }
    if (data.emailAddress && !/\S+@\S+\.\S+/.test(data.emailAddress)) {
      this.setError('emailAddress', 'Email address is invalid');
    }

    const captchaInput = document.getElementById('captchaInput');
    const captchaAnswer = captchaInput.value;
    const correctAnswer = this.captchaNum1 + this.captchaNum2;

    if (captchaAnswer !== correctAnswer.toString()) {
      const errorElement = document.getElementById('error-captcha');
      errorElement.textContent = 'Incorrect answer';
      captchaInput.classList.add('error');
      this.refreshCaptcha();
    }

    return Object.keys(this.errors).length === 0 && captchaAnswer === correctAnswer.toString();
  }

  async handleSubmit(e) {
    e.preventDefault();

    if (!this.validateForm()) {
      return;
    }

    this.isSubmitting = true;
    this.submitBtn.disabled = true;
    this.submitBtn.textContent = 'Registering...';

    try {
      const data = this.getFormData();
      console.log('Form submitted:', data);

      await new Promise(resolve => setTimeout(resolve, 1000));

      alert('Registration successful! Please check your email for verification.');
      this.form.reset();
      this.formData = {};

      // Reset password strength indicator
      const strengthIndicator = document.getElementById('password-strength-indicator');
      const strengthText = document.getElementById('password-strength-text');
      if (strengthIndicator) {
        strengthIndicator.className = 'strength-indicator';
        strengthIndicator.style.width = '0%';
      }
      if (strengthText) strengthText.textContent = '';
      this.updateRequirements('');

    } catch (error) {
      console.error('Registration error:', error);
      alert('Registration failed. Please try again.');
    } finally {
      this.isSubmitting = false;
      this.submitBtn.disabled = false;
      this.submitBtn.textContent = 'Register';
    }
  }
}

document.addEventListener('DOMContentLoaded', () => {
  new RegisterForm();
});

/* Add these styles to your register.css file */

/* Password Strength Indicator Styles */
const styles = `
.password-field-container {
  position: relative;
}

.strength-meter {
  margin-top: 8px;
}

.strength-bar {
  width: 100%;
  height: 6px;
  background-color: var(--gray-200);
  border-radius: 3px;
  overflow: hidden;
  margin-bottom: 8px;
}

.strength-indicator {
  height: 100%;
  transition: all 0.3s ease;
  width: 0%;
}

.strength-indicator.strength-weak {
  background-color: #dc2626;
}

.strength-indicator.strength-medium {
  background-color: #f59e0b;
}

.strength-indicator.strength-strong {
  background-color: #16a34a;
}

.strength-text {
  font-size: 0.75rem;
  font-weight: 600;
  text-align: right;
}

.strength-text.strength-weak {
  color: #dc2626;
}

.strength-text.strength-medium {
  color: #f59e0b;
}

.strength-text.strength-strong {
  color: #16a34a;
}

.password-requirements {
  margin-top: 12px;
  padding: 12px;
  background-color: var(--gray-50);
  border-radius: 6px;
  border: 1px solid var(--gray-200);
}

.password-requirements h4 {
  font-size: 0.75rem;
  font-weight: 600;
  color: var(--gray-700);
  margin-bottom: 8px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.requirement-item {
  font-size: 0.8rem;
  padding: 4px 0;
  display: flex;
  align-items: center;
  gap: 8px;
  transition: color 0.3s ease;
}

.requirement-item::before {
  content: '✗';
  font-weight: bold;
  width: 16px;
  text-align: center;
}

.requirement-item.unmet {
  color: var(--gray-600);
}

.requirement-item.unmet::before {
  content: '✗';
  color: #dc2626;
}

.requirement-item.met {
  color: #16a34a;
}

.requirement-item.met::before {
  content: '✓';
  color: #16a34a;
}
`;