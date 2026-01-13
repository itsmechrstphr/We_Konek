<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>We Konek - Student Registration</title>
  <link rel="stylesheet" href="../assets/css/register.css">
</head>
<body>

    <div class="register-container">
      <div class="register-box">
        <div class="register-title">
          <div class="role-icon">
            <img src="../assets/images/LogoOnly.png" alt="Logo">
          </div>
          <h1>REGISTER TO WE-KONEK</h1>
          <p class="register-subtitle">STUDENT REGISTRATION</p>
        </div>

        <form class="register-form" id="registerForm">
          <!-- School Year -->
          <div class="form-row">
            <div class="form-group">
              <label>SCHOOL YEAR</label>
              <div class="school-year-inputs">
                <input type="text" name="schoolYear" placeholder="20__" maxlength="4" data-field="schoolYear">
                <span>—</span>
                <input type="text" placeholder="20__" maxlength="4">
              </div>
              <span class="error-message" id="error-schoolYear"></span>
            </div>
          </div>

          <!-- Term Selection -->
          <div class="form-row">
            <div class="form-group full-width">
              <label>TERM</label>
              <div class="radio-group">
                <label class="radio-label">
                  <input type="radio" name="term" value="1st" data-field="term">
                  <span>1st Term</span>
                </label>
                <label class="radio-label">
                  <input type="radio" name="term" value="2nd" data-field="term">
                  <span>2nd Term</span>
                </label>
                <label class="radio-label">
                  <input type="radio" name="term" value="3rd" data-field="term">
                  <span>3rd Term</span>
                </label>
                <label class="radio-label">
                  <input type="radio" name="term" value="summer" data-field="term">
                  <span>Summer</span>
                </label>
              </div>
              <span class="error-message" id="error-term"></span>
            </div>
          </div>

          <!-- Student Type -->
          <div class="form-row">
            <div class="form-group full-width">
              <div class="radio-group">
                <label class="radio-label">
                  <input type="radio" name="studentType" value="new" data-field="studentType">
                  <span>New Student</span>
                </label>
                <label class="radio-label">
                  <input type="radio" name="studentType" value="old" data-field="studentType">
                  <span>Old Student</span>
                </label>
                <label class="radio-label student-no-label">
                  <span>Student No.</span>
                  <input type="text" name="studentNo" class="student-no-input" data-field="studentNo" disabled>
                </label>
              </div>
              <span class="error-message" id="error-studentType"></span>
              <span class="error-message" id="error-studentNo"></span>
            </div>
          </div>

          <!-- Course and Major -->
          <div class="form-row">
            <div class="form-group">
              <label>COURSE</label>
              <input type="text" name="course" data-field="course">
              <span class="error-message" id="error-course"></span>
            </div>
            <div class="form-group">
              <label>MAJOR IN</label>
              <input type="text" name="major" data-field="major">
            </div>
          </div>

          <!-- Names -->
          <div class="form-row">
            <div class="form-group">
              <label>LAST NAME</label>
              <input type="text" name="lastName" placeholder="Last Name" data-field="lastName">
              <span class="error-message" id="error-lastName"></span>
            </div>
            <div class="form-group">
              <label>FIRST NAME</label>
              <input type="text" name="firstName" placeholder="First Name" data-field="firstName">
              <span class="error-message" id="error-firstName"></span>
            </div>
          </div>

          <!-- Middle Name and Password -->
          <div class="form-row">
            <div class="form-group">
              <label>MIDDLE NAME</label>
              <input type="text" name="middleName" placeholder="Middle Name" data-field="middleName">
            </div>
            <div class="form-group">
              <label>PASSWORD</label>
              <input type="password" name="password" placeholder="Enter your Password" data-field="password">
              <span class="error-message" id="error-password"></span>
            </div>
          </div>

          <!-- Confirm Password and Contact -->
          <div class="form-row">
            <div class="form-group">
              <label>CONFIRM PASSWORD</label>
              <input type="password" name="confirmPassword" placeholder="Enter your Password" data-field="confirmPassword">
              <span class="error-message" id="error-confirmPassword"></span>
            </div>
            <div class="form-group">
              <label>CONTACT NO</label>
              <input type="tel" name="contactNo" placeholder="Enter Contact No." data-field="contactNo">
              <span class="error-message" id="error-contactNo"></span>
            </div>
          </div>

          <!-- Date and Place of Birth -->
          <div class="form-row">
            <div class="form-group">
              <label>DATE OF BIRTH</label>
              <input type="text" name="dateOfBirth" placeholder="MM/DD/YY" data-field="dateOfBirth">
              <span class="error-message" id="error-dateOfBirth"></span>
            </div>
            <div class="form-group">
              <label>PLACE OF BIRTH</label>
              <input type="text" name="placeOfBirth" placeholder="Enter Place of Birth" data-field="placeOfBirth">
            </div>
          </div>

          <!-- Sex and Civil Status -->
          <div class="form-row">
            <div class="form-group">
              <label>SEX</label>
              <div class="radio-group">
                <label class="radio-label">
                  <input type="radio" name="sex" value="male" data-field="sex">
                  <span>Male</span>
                </label>
                <label class="radio-label">
                  <input type="radio" name="sex" value="female" data-field="sex">
                  <span>Female</span>
                </label>
              </div>
              <span class="error-message" id="error-sex"></span>
            </div>
            <div class="form-group">
              <label>CHILD STATUS</label>
              <div class="radio-group">
                <label class="radio-label">
                  <input type="radio" name="childStatus" value="single" data-field="childStatus">
                  <span>Single</span>
                </label>
                <label class="radio-label">
                  <input type="radio" name="childStatus" value="married" data-field="childStatus">
                  <span>Married</span>
                </label>
                <label class="radio-label">
                  <input type="radio" name="childStatus" value="widowed" data-field="childStatus">
                  <span>Widowed</span>
                </label>
              </div>
              <span class="error-message" id="error-childStatus"></span>
            </div>
          </div>

          <!-- Father and Parents Address -->
          <div class="form-row">
            <div class="form-group">
              <label>NAME OF FATHER</label>
              <input type="text" name="nameOfFather" placeholder="First Name, Middle, Last" data-field="nameOfFather">
            </div>
            <div class="form-group">
              <label>ADDRESS OF PARENTS</label>
              <input type="text" name="addressOfParents" placeholder="House No, Street, Barangay, Town, Province" data-field="addressOfParents">
            </div>
          </div>

          <!-- Guardian Information -->
          <div class="form-row">
            <div class="form-group">
              <label>NAME OF GUARDIAN</label>
              <input type="text" name="nameOfGuardian" placeholder="Enter Guardian's Name" data-field="nameOfGuardian">
            </div>
            <div class="form-group">
              <label>RELATIONSHIP TO GUARDIAN</label>
              <input type="text" name="relationshipToGuardian" placeholder="Relationship to Guardian" data-field="relationshipToGuardian">
            </div>
          </div>

          <!-- Guardian Address and Mailing Address -->
          <div class="form-row">
            <div class="form-group">
              <label>ADDRESS OF GUARDIAN</label>
              <input type="text" name="addressOfGuardian" placeholder="House No, Street, Barangay, Town, Province" data-field="addressOfGuardian">
            </div>
            <div class="form-group">
              <label>COMPLETE MAILING ADDRESS</label>
              <input type="text" name="completeMailingAddress" placeholder="House No, Street, Barangay, Town, Province" data-field="completeMailingAddress">
            </div>
          </div>

          <!-- Email and Permanent Home Address -->
          <div class="form-row">
            <div class="form-group">
              <label>EMAIL ADDRESS</label>
              <input type="email" name="emailAddress" placeholder="Enter a valid Email" data-field="emailAddress">
              <span class="error-message" id="error-emailAddress"></span>
            </div>
            <div class="form-group">
              <label>PERMANENT HOME ADDRESS</label>
              <input type="text" name="permanentHomeAddress" placeholder="House No, Street, Barangay, Town, Province" data-field="permanentHomeAddress">
            </div>
          </div>

          <!-- Captcha -->
          <div class="captcha-section">
            <div class="captcha-box">
              <span class="captcha-number" id="captchaNum1">25</span>
              <span class="captcha-operator">+</span>
              <span class="captcha-number" id="captchaNum2">25</span>
              <span class="captcha-operator">=</span>
              <input type="text" class="captcha-input" id="captchaInput" placeholder="?" maxlength="2">
            </div>
            <span class="error-message" id="error-captcha"></span>
          </div>

          <!-- Submit Button -->
          <div class="form-actions">
            <button type="submit" class="register-button" id="submitBtn">Register</button>
          </div>

          <!-- Form Footer -->
          <div class="form-footer">
            <p>Already have an Account? <a href="/We_Konek/auth/login.php">Login</a></p>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script src="../assets/js/register.js"></script>
</body>
</html>
