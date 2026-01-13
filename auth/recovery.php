<?php
session_start();

// Database configuration - UPDATE THESE WITH YOUR ACTUAL DATABASE CREDENTIALS
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'we_konek');

// Email configuration - UPDATE THESE WITH YOUR GMAIL CREDENTIALS
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'your-email@gmail.com'); // Your Gmail address
define('SMTP_PASS', 'your-app-password'); // Gmail App Password (not regular password)
define('SMTP_FROM_EMAIL', 'your-email@gmail.com');
define('SMTP_FROM_NAME', 'We Konek Support');

// Token expiration time (15 minutes)
define('TOKEN_EXPIRY', 900);

// Rate limiting (60 seconds between requests)
define('RATE_LIMIT_SECONDS', 60);

// Initialize variables
$step = isset($_GET['step']) ? $_GET['step'] : 'request';
$token = isset($_GET['token']) ? $_GET['token'] : '';
$message = '';
$messageType = '';

// Database connection
function getDBConnection() {
    try {
        $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch(PDOException $e) {
        return null;
    }
}

// Generate secure token
function generateToken() {
    return bin2hex(random_bytes(32));
}

// Send email using PHP mail function with SMTP simulation
function sendRecoveryEmail($email, $token, $userName) {
    $resetLink = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/recovery.php?step=reset&token=" . $token;
    
    $subject = "Password Reset Request - We Konek";
    
    $message = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #225B13 0%, #1a4a0f 100%); color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
            .content { background: #f9fafb; padding: 30px; border: 1px solid #e5e7eb; }
            .button { display: inline-block; background: #225B13; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; margin: 20px 0; }
            .footer { background: #f3f4f6; padding: 20px; text-align: center; font-size: 12px; color: #6b7280; border-radius: 0 0 8px 8px; }
            .warning { background: #fef3c7; border-left: 4px solid #f59e0b; padding: 12px; margin: 15px 0; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>Password Reset Request</h1>
            </div>
            <div class='content'>
                <p>Hello <strong>" . htmlspecialchars($userName) . "</strong>,</p>
                <p>We received a request to reset your password for your We Konek account.</p>
                <p>Click the button below to reset your password:</p>
                <p style='text-align: center;'>
                    <a href='" . $resetLink . "' class='button'>Reset Password</a>
                </p>
                <p>Or copy and paste this link into your browser:</p>
                <p style='word-break: break-all; background: white; padding: 10px; border: 1px solid #e5e7eb; border-radius: 4px;'>" . $resetLink . "</p>
                <div class='warning'>
                    <strong>⚠️ Security Notice:</strong>
                    <ul>
                        <li>This link will expire in 15 minutes</li>
                        <li>If you didn't request this reset, please ignore this email</li>
                        <li>Never share this link with anyone</li>
                    </ul>
                </div>
            </div>
            <div class='footer'>
                <p>This is an automated message from We Konek Student Information System</p>
                <p>&copy; 2025 We Konek. All rights reserved.</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: " . SMTP_FROM_NAME . " <" . SMTP_FROM_EMAIL . ">" . "\r\n";
    $headers .= "Reply-To: " . SMTP_FROM_EMAIL . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();
    
    return mail($email, $subject, $message, $headers);
}

// Check rate limiting
function checkRateLimit($email) {
    if (!isset($_SESSION['last_request'])) {
        return true;
    }
    
    if (isset($_SESSION['last_request'][$email])) {
        $timeDiff = time() - $_SESSION['last_request'][$email];
        return $timeDiff >= RATE_LIMIT_SECONDS;
    }
    
    return true;
}

// Set rate limit
function setRateLimit($email) {
    if (!isset($_SESSION['last_request'])) {
        $_SESSION['last_request'] = [];
    }
    $_SESSION['last_request'][$email] = time();
}

// Handle password reset request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    
    if ($_POST['action'] === 'request_reset') {
        $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = "Please enter a valid email address.";
            $messageType = "error";
        } else {
            // Check rate limiting
            if (!checkRateLimit($email)) {
                $remainingTime = RATE_LIMIT_SECONDS - (time() - $_SESSION['last_request'][$email]);
                $message = "Please wait " . $remainingTime . " seconds before requesting another reset.";
                $messageType = "error";
            } else {
                $conn = getDBConnection();
                
                if ($conn) {
                    // Search for user in all tables
                    $userFound = false;
                    $userName = '';
                    $userTable = '';
                    $userId = '';
                    
                    // Check students table
                    $stmt = $conn->prepare("SELECT student_id, CONCAT(first_name, ' ', last_name) as name FROM students WHERE email = :email LIMIT 1");
                    $stmt->execute(['email' => $email]);
                    $student = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($student) {
                        $userFound = true;
                        $userName = $student['name'];
                        $userTable = 'students';
                        $userId = $student['student_id'];
                    }
                    
                    // Check faculty table if not found
                    if (!$userFound) {
                        $stmt = $conn->prepare("SELECT faculty_id, CONCAT(first_name, ' ', last_name) as name FROM faculty WHERE email = :email LIMIT 1");
                        $stmt->execute(['email' => $email]);
                        $faculty = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        if ($faculty) {
                            $userFound = true;
                            $userName = $faculty['name'];
                            $userTable = 'faculty';
                            $userId = $faculty['faculty_id'];
                        }
                    }
                    
                    // Check admin table if not found
                    if (!$userFound) {
                        $stmt = $conn->prepare("SELECT admin_id, username as name FROM admins WHERE email = :email LIMIT 1");
                        $stmt->execute(['email' => $email]);
                        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        if ($admin) {
                            $userFound = true;
                            $userName = $admin['name'];
                            $userTable = 'admins';
                            $userId = $admin['admin_id'];
                        }
                    }
                    
                    // Always show success message to prevent email enumeration
                    if ($userFound) {
                        // Generate token
                        $token = generateToken();
                        $tokenHash = hash('sha256', $token);
                        $expiryTime = date('Y-m-d H:i:s', time() + TOKEN_EXPIRY);
                        
                        // Store token in database
                        $stmt = $conn->prepare("INSERT INTO password_resets (email, token, user_table, user_id, expires_at, created_at) VALUES (:email, :token, :user_table, :user_id, :expires_at, NOW())");
                        $stmt->execute([
                            'email' => $email,
                            'token' => $tokenHash,
                            'user_table' => $userTable,
                            'user_id' => $userId,
                            'expires_at' => $expiryTime
                        ]);
                        
                        // Send email
                        sendRecoveryEmail($email, $token, $userName);
                        
                        // Set rate limit
                        setRateLimit($email);
                    }
                    
                    $message = "If an account exists with this email, a password reset link has been sent. Please check your inbox.";
                    $messageType = "success";
                } else {
                    $message = "Database connection failed. Please try again later.";
                    $messageType = "error";
                }
            }
        }
    }
    
    if ($_POST['action'] === 'reset_password') {
        $token = $_POST['token'];
        $newPassword = $_POST['new_password'];
        $confirmPassword = $_POST['confirm_password'];
        
        if (strlen($newPassword) < 6) {
            $message = "Password must be at least 6 characters long.";
            $messageType = "error";
        } elseif ($newPassword !== $confirmPassword) {
            $message = "Passwords do not match.";
            $messageType = "error";
        } else {
            $conn = getDBConnection();
            
            if ($conn) {
                $tokenHash = hash('sha256', $token);
                
                // Verify token
                $stmt = $conn->prepare("SELECT * FROM password_resets WHERE token = :token AND expires_at > NOW() AND used = 0 LIMIT 1");
                $stmt->execute(['token' => $tokenHash]);
                $resetRecord = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($resetRecord) {
                    // Update password based on user table
                    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                    
                    if ($resetRecord['user_table'] === 'students') {
                        $stmt = $conn->prepare("UPDATE students SET password = :password WHERE student_id = :id");
                    } elseif ($resetRecord['user_table'] === 'faculty') {
                        $stmt = $conn->prepare("UPDATE faculty SET password = :password WHERE faculty_id = :id");
                    } elseif ($resetRecord['user_table'] === 'admins') {
                        $stmt = $conn->prepare("UPDATE admins SET password = :password WHERE admin_id = :id");
                    }
                    
                    $stmt->execute([
                        'password' => $hashedPassword,
                        'id' => $resetRecord['user_id']
                    ]);
                    
                    // Mark token as used
                    $stmt = $conn->prepare("UPDATE password_resets SET used = 1 WHERE id = :id");
                    $stmt->execute(['id' => $resetRecord['id']]);
                    
                    $message = "Password reset successful! You can now login with your new password.";
                    $messageType = "success";
                    $step = 'complete';
                } else {
                    $message = "Invalid or expired reset link. Please request a new one.";
                    $messageType = "error";
                    $step = 'request';
                }
            } else {
                $message = "Database connection failed. Please try again later.";
                $messageType = "error";
            }
        }
    }
}

// Verify token for reset page
if ($step === 'reset' && !empty($token)) {
    $conn = getDBConnection();
    if ($conn) {
        $tokenHash = hash('sha256', $token);
        $stmt = $conn->prepare("SELECT * FROM password_resets WHERE token = :token AND expires_at > NOW() AND used = 0 LIMIT 1");
        $stmt->execute(['token' => $tokenHash]);
        
        if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
            $message = "Invalid or expired reset link. Please request a new one.";
            $messageType = "error";
            $step = 'request';
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Password Recovery - We Konek</title>
    <meta name="description" content="Reset your We Konek account password" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/recovery.css" />
</head>
<body>
    <div class="recovery-container">
        <main class="recovery-main">
            <div class="form-wrapper">
                
                <?php if ($step === 'request'): ?>
                <!-- Step 1: Request Password Reset -->
                <div class="form-header">
                    <div class="logo-icon">
                        <img src="../assets/images/LogoOnly.png" alt="Logo">
                    </div>
                    <h1>Forgot Password?</h1>
                    <p class="form-subtitle">Enter your email to reset your password</p>
                </div>

                <?php if ($message): ?>
                <div class="message-box <?php echo $messageType; ?>" id="messageBox">
                    <i class="fas <?php echo $messageType === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
                    <span><?php echo htmlspecialchars($message); ?></span>
                </div>
                <?php endif; ?>

                <form id="requestForm" class="recovery-form" method="POST" action="">
                    <input type="hidden" name="action" value="request_reset">
                    
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <div class="input-with-icon">
                            <i class="fas fa-envelope"></i>
                            <input 
                                type="email" 
                                id="email" 
                                name="email" 
                                placeholder="Enter your registered email" 
                                required 
                                autocomplete="email"
                            >
                        </div>
                        <span class="field-hint">We'll send a password reset link to this email</span>
                    </div>

                    <button type="submit" class="btn-submit" id="submitBtn">
                        <span class="btn-text">Send Reset Link</span>
                        <i class="fas fa-paper-plane btn-icon"></i>
                    </button>
                </form>

                <?php elseif ($step === 'reset'): ?>
                <!-- Step 2: Reset Password -->
                <div class="form-header">
                    <div class="logo-icon">
                        <img src="../assets/images/LogoOnly.png" alt="Logo">
                    </div>
                    <h1>Set New Password</h1>
                    <p class="form-subtitle">Enter your new password below</p>
                </div>

                <?php if ($message): ?>
                <div class="message-box <?php echo $messageType; ?>" id="messageBox">
                    <i class="fas <?php echo $messageType === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
                    <span><?php echo htmlspecialchars($message); ?></span>
                </div>
                <?php endif; ?>

                <form id="resetForm" class="recovery-form" method="POST" action="">
                    <input type="hidden" name="action" value="reset_password">
                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                    
                    <div class="form-group">
                        <label for="new_password">New Password</label>
                        <div class="input-with-icon">
                            <i class="fas fa-lock"></i>
                            <input
                                type="password"
                                id="new_password"
                                name="new_password"
                                placeholder="Enter new password"
                                required
                                autocomplete="new-password"
                                minlength="6"
                            >
                            <button type="button" class="toggle-password" data-target="new_password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <span class="field-hint">Minimum 6 characters</span>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Confirm Password</label>
                        <div class="input-with-icon">
                            <i class="fas fa-lock"></i>
                            <input
                                type="password"
                                id="confirm_password"
                                name="confirm_password"
                                placeholder="Confirm new password"
                                required
                                autocomplete="new-password"
                                minlength="6"
                            >
                            <button type="button" class="toggle-password" data-target="confirm_password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="password-strength" id="passwordStrength">
                        <div class="strength-bar">
                            <div class="strength-fill"></div>
                        </div>
                        <span class="strength-text">Password strength: <strong>-</strong></span>
                    </div>

                    <button type="submit" class="btn-submit" id="submitBtn">
                        <span class="btn-text">Reset Password</span>
                        <i class="fas fa-check btn-icon"></i>
                    </button>
                </form>

                <?php else: ?>
                <!-- Step 3: Success -->
                <div class="form-header">
                    <div class="success-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h1>Password Reset Complete!</h1>
                    <p class="form-subtitle">Your password has been successfully reset</p>
                </div>

                <div class="success-content">
                    <p>You can now log in to your account using your new password.</p>
                    <a href="login.php" class="btn-submit">
                        <span class="btn-text">Go to Login</span>
                        <i class="fas fa-sign-in-alt btn-icon"></i>
                    </a>
                </div>
                <?php endif; ?>

                <!-- Footer -->
                <div class="form-footer">
                    <p class="back-link">
                        <a href="login.php">
                            <i class="fas fa-arrow-left"></i> Back to Login
                        </a>
                    </p>
                    <p class="help-text">
                        Need help? <a href="/WE_KONEK/index.php#contact">Contact Support</a>
                    </p>
                </div>
            </div>
        </main>

        <!-- Decorative Elements -->
        <div class="decoration decoration-1"></div>
        <div class="decoration decoration-2"></div>
    </div>

    <script src="../assets/js/recovery.js"></script>
</body>
</html>