<?php
session_start();

// Include config for database connection
require_once '../config/config.php';

// Get role from URL parameter or session
$role = isset($_GET['role']) ? $_GET['role'] : (isset($_SESSION['admin_login_role']) ? $_SESSION['admin_login_role'] : 'admin');

// Store role in session for persistence
$_SESSION['admin_login_role'] = $role;

// Normalize role value
$role = strtolower($role);
if ($role !== 'admin' && $role !== 'super_admin') {
    $role = 'admin';
}

// Set role-specific variables
$pageTitle = $role === 'admin' ? 'Admin Login' : 'Super Admin Login';
$idLabel = $role === 'admin' ? 'Admin ID' : 'Super Admin ID';
$idPlaceholder = $role === 'admin' ? 'Enter your Admin ID' : 'Enter your Super Admin ID';
$roleIcon = $role === 'admin' ? 'fa-user-shield' : 'fa-crown';
$roleClass = $role === 'admin' ? 'admin-role' : 'super-admin-role';

// Handle form submission
$show2FA = false;
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['login'])) {
        // Login attempt
        $userId = trim($_POST['userId']);
        $password = $_POST['password'];
        $captchaInput = trim($_POST['captcha']);
        $expectedCaptcha = $_POST['expectedCaptcha'];

        // Validate captcha
        if ($captchaInput != $expectedCaptcha) {
            $errorMessage = 'Invalid captcha. Please try again.';
        } else {
            // Validate credentials against database
            $stmt = $conn->prepare("SELECT id, password, email FROM admins WHERE role = ? AND (admin_id = ? OR super_admin_id = ?)");
            $stmt->bind_param("sss", $role, $userId, $userId);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 1) {
                $user = $result->fetch_assoc();
                if (password_verify($password, $user['password'])) {
                    // Credentials valid, generate OTP
                    $otp = rand(100000, 999999);
                    $_SESSION['otp'] = $otp;
                    $_SESSION['otp_user_id'] = $user['id'];
                    $_SESSION['otp_email'] = $user['email'];
                    $_SESSION['otp_expiry'] = time() + 300; // 5 minutes

                    // Send OTP via email
                    $subject = 'Your OTP for Admin Login';
                    $message = "Your OTP is: $otp\n\nThis code will expire in 5 minutes.";
                    $headers = 'From: noreply@wekonek.edu.ph';

                    if (mail($user['email'], $subject, $message, $headers)) {
                        $show2FA = true;
                    } else {
                        $errorMessage = 'Failed to send OTP. Please try again.';
                    }
                } else {
                    $errorMessage = 'Invalid credentials.';
                }
            } else {
                $errorMessage = 'Invalid credentials.';
            }
            $stmt->close();
        }
    } elseif (isset($_POST['verify_otp'])) {
        // OTP verification
        $enteredOtp = trim($_POST['otp']);

        if (isset($_SESSION['otp']) && isset($_SESSION['otp_expiry']) && time() < $_SESSION['otp_expiry']) {
            if ($enteredOtp == $_SESSION['otp']) {
                // OTP valid, log in user
                $_SESSION['admin_id'] = $_SESSION['otp_user_id'];
                $_SESSION['admin_role'] = $role;
                unset($_SESSION['otp'], $_SESSION['otp_user_id'], $_SESSION['otp_email'], $_SESSION['otp_expiry']);

                // Redirect to dashboard
                header('Location: ../pages/admins/' . ($role === 'super_admin' ? 'super_admin_dashboard.php' : 'admin_dashboard.php'));
                exit;
            } else {
                $errorMessage = 'Invalid OTP.';
            }
        } else {
            $errorMessage = 'OTP expired. Please try logging in again.';
        }
    }
}

// Generate captcha
$num1 = rand(1, 10);
$num2 = rand(1, 10);
$expectedCaptcha = $num1 + $num2;
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo $pageTitle; ?> - We Konek</title>
    <meta name="description" content="Admin Login to We Konek Student Information System" />
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
                    <a href="admin_login.php?role=admin" class="role-toggle-btn <?php echo $role === 'admin' ? 'active' : ''; ?>">
                        <i class="fas fa-user-shield"></i>
                        <span>Admin</span>
                    </a>
                    <a href="admin_login.php?role=super_admin" class="role-toggle-btn <?php echo $role === 'super_admin' ? 'active' : ''; ?>">
                        <i class="fas fa-crown"></i>
                        <span>Super Admin</span>
                    </a>
                </div>

                <?php if (!$show2FA): ?>
                <!-- Login Form -->
                <form id="loginForm" class="login-form" method="POST" action="">
                    <input type="hidden" name="role" value="<?php echo $role; ?>">
                    <input type="hidden" name="expectedCaptcha" value="<?php echo $expectedCaptcha; ?>">
                    
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
                            <span class="captcha-number"><?php echo $num1; ?></span>
                            <span class="captcha-operator">+</span>
                            <span class="captcha-number"><?php echo $num2; ?></span>
                            <span class="captcha-operator">=</span>
                            <input type="text" class="captcha-input" name="captcha" placeholder="?" maxlength="2" required>
                        </div>
                        <span class="error-message" id="error-captcha"></span>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" name="login" class="btn-login">
                        <span class="btn-text">Sign In</span>
                        <i class="fas fa-arrow-right btn-icon"></i>
                    </button>

                    <!-- Error Message Container -->
                    <div id="errorMessage" class="error-message" style="display: <?php echo $errorMessage ? 'block' : 'none'; ?>;"><?php echo $errorMessage; ?></div>
                </form>
                <?php else: ?>
                <!-- 2FA Form -->
                <form id="otpForm" class="login-form" method="POST" action="">
                    <div class="form-group">
                        <label for="otp">Enter OTP</label>
                        <div class="input-with-icon">
                            <i class="fas fa-key"></i>
                            <input
                                type="text"
                                id="otp"
                                name="otp"
                                placeholder="Enter the 6-digit OTP sent to your email"
                                required
                                maxlength="6"
                            >
                        </div>
                    </div>

                    <button type="submit" name="verify_otp" class="btn-login">
                        <span class="btn-text">Verify OTP</span>
                        <i class="fas fa-arrow-right btn-icon"></i>
                    </button>

                    <!-- Error Message Container -->
                    <div id="errorMessage" class="error-message" style="display: <?php echo $errorMessage ? 'block' : 'none'; ?>;"><?php echo $errorMessage; ?></div>
                </form>
                <?php endif; ?>

                <!-- Form Footer -->
                <div class="form-footer">
                    <p>Is there any issues? <a href="#">Contact IT support</a></p>
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
