<?php
session_start();

// Get role from URL parameter or session
$role = isset($_GET['role']) ? $_GET['role'] : (isset($_SESSION['login_role']) ? $_SESSION['login_role'] : 'student');

// Store role in session for persistence
$_SESSION['login_role'] = $role;

// Normalize role value
$role = strtolower($role);
if ($role !== 'student' && $role !== 'faculty') {
    $role = 'student';
}

// Set role-specific variables
$pageTitle = $role === 'student' ? 'Student Login' : 'Faculty Login';
$idLabel = $role === 'student' ? 'Student ID' : 'Faculty ID';
$idPlaceholder = $role === 'student' ? 'Enter your Student ID' : 'Enter your Faculty ID';
$roleIcon = $role === 'student' ? 'fa-graduation-cap' : 'fa-chalkboard-user';
$roleClass = $role === 'student' ? 'student-role' : 'faculty-role';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo $pageTitle; ?> - We Konek</title>
    <meta name="description" content="Login to We Konek Student Information System" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/login.css" />
</head>
<body>
    <div class="login-container">
        <!-- Main Login Form -->
        <main class="login-main">
            <div class="form-wrapper <?php echo $roleClass; ?>">
                <!-- Role Indicator -->
                <div class="role-indicator">
                    <div class="role-icon">
                        <img src="../assets/images/LogoOnly.png" alt="Logo">
                    </div>
                    <h1><?php echo $pageTitle; ?></h1>
                    <p class="form-subtitle">SIGN IN TO YOUR ACCOUNT</p>
                </div>

                <!-- Role Toggle -->
                <div class="role-toggle">
                    <a href="login.php?role=student" class="role-toggle-btn <?php echo $role === 'student' ? 'active' : ''; ?>">
                        <i class="fas fa-graduation-cap"></i>
                        <span>Student</span>
                    </a>
                    <a href="login.php?role=faculty" class="role-toggle-btn <?php echo $role === 'faculty' ? 'active' : ''; ?>">
                        <i class="fas fa-chalkboard-user"></i>
                        <span>Faculty</span>
                    </a>
                </div>

                <!-- Login Form -->
                <form id="loginForm" class="login-form" method="POST" action="">
                    <input type="hidden" name="role" value="<?php echo $role; ?>">
                    
                    <!-- ID Field -->
                    <div class="form-group">
                        <label for="userId"><?php echo $idLabel; ?></label>
                        <div class="input-with-icon">
                            <i class="fas fa-id-card"></i>
                            <input 
                                type="text" 
                                id="userId" 
                                name="userId" 
                                placeholder="<?php echo $idPlaceholder; ?>" 
                                required 
                                autocomplete="username"
                            >
                        </div>
                    </div>

                    <!-- Password Field -->
                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-with-icon">
                            <i class="fas fa-lock"></i>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                placeholder="Enter your password"
                                required
                                autocomplete="current-password"
                            >
                            <button type="button" class="toggle-password" aria-label="Toggle password visibility">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Captcha -->
                    <div class="captcha-section">
                        <div class="captcha-box">
                            <span class="captcha-number" id="captchaNum1">5</span>
                            <span class="captcha-operator">+</span>
                            <span class="captcha-number" id="captchaNum2">3</span>
                            <span class="captcha-operator">=</span>
                            <input type="text" class="captcha-input" id="captchaInput" placeholder="?" maxlength="2">
                        </div>
                        <span class="error-message" id="error-captcha"></span>
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="form-options">
                        <label class="remember-me">
                            <input type="checkbox" name="remember" id="remember">
                            <span>Remember me</span>
                        </label>
                        <a href="#" class="forgot-password" onclick="handleForgotPassword(event)">Forgot Password?</a>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn-login">
                        <span class="btn-text">Sign In</span>
                        <i class="fas fa-arrow-right btn-icon"></i>
                    </button>

                    <!-- Error Message Container -->
                    <div id="errorMessage" class="error-message" style="display: none;"></div>
                </form>

                <!-- Form Footer -->
                <div class="form-footer">
                    <?php if ($role === 'faculty'): ?>
                        <p>Do you have any problem? <a href="#">Contact admin</a></p>
                    <?php else: ?>
                        <p>Don't have an account? <a href="/WE_KONEK/auth/register.php">Register here</a></p>
                    <?php endif; ?>
                    <p class="back-home"><a href="/WE_KONEK/index.php"><i class="fas fa-home"></i> Back to Home</a></p>
                </div>
            </div>
        </main>

        <!-- Decorative Elements -->
        <div class="decoration decoration-1"></div>
        <div class="decoration decoration-2"></div>
    </div>

    <script src="../assets/js/login.js"></script>
</body>
</html>