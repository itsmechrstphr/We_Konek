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
    if (!data.password) {
      this.setError('password', 'Password is required');
    }
    if (data.password && data.password.length < 6) {
      this.setError('password', 'Password must be at least 6 characters');
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
      this.refreshCaptcha(); // Refresh captcha on incorrect answer
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
