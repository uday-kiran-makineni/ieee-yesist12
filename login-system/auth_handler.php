<?php
// Start output buffering to catch any unexpected output
ob_start();

// Suppress all PHP warnings and notices for clean JSON output
error_reporting(0);
ini_set('display_errors', 0);

session_start();

// Clean any previous output
if (ob_get_length()) {
    ob_clean();
}

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    ob_end_clean();
    exit(0);
}

// Debug logging
error_log("Auth Handler Called - Method: " . $_SERVER['REQUEST_METHOD']);

// Include configuration
try {
    require_once 'config.php';
} catch (Exception $e) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Configuration error: ' . $e->getMessage()]);
    exit();
}

try {
    $pdo = getDBConnection();
    error_log("Database connection successful");
} catch(Exception $e) {
    ob_end_clean();
    error_log("Database connection failed: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit();
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';

error_log("Action received: " . $action);
error_log("Input data: " . json_encode($input));

// Clean output buffer before sending JSON
ob_end_clean();

switch($action) {
    case 'signin':
        handleSignIn($pdo, $input);
        break;
    case 'signup':
        handleSignUp($pdo, $input);
        break;
    case 'send_otp':
        handleSendOTP($pdo, $input);
        break;
    case 'verify_otp':
        handleVerifyOTP($pdo, $input);
        break;
    case 'forgot_password':
        handleForgotPassword($pdo, $input);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

function handleSignIn($pdo, $input) {
    $email = $input['email'] ?? '';
    $password = $input['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Email and password are required']);
        return;
    }
    
    try {
        $stmt = $pdo->prepare("SELECT id, email, password, full_name, is_verified FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            if (!$user['is_verified']) {
                echo json_encode(['success' => false, 'message' => 'Please verify your email before signing in']);
                return;
            }
            
            // Set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['full_name'];
            
            // Update last login
            $updateStmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
            $updateStmt->execute([$user['id']]);
            
            echo json_encode([
                'success' => true, 
                'message' => 'Sign in successful',
                'redirect' => 'dashboard.php'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
}

function handleSignUp($pdo, $input) {
    error_log("=== SIGNUP PROCESS STARTED ===");
    error_log("Input received: " . json_encode($input));
    
    $email = $input['email'] ?? '';
    $password = $input['password'] ?? '';
    $fullName = $input['fullName'] ?? '';
    $phone = $input['phone'] ?? '';
    
    error_log("Extracted data - Email: $email, FullName: $fullName, Phone: $phone");
    
    if (empty($email) || empty($password) || empty($fullName) || empty($phone)) {
        error_log("Validation failed: Missing required fields");
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        return;
    }
    
    if (strlen($password) < 8) {
        error_log("Validation failed: Password too short");
        echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters long']);
        return;
    }
    
    try {
        error_log("Starting database operations...");
        
        // Check if user already exists
        $checkStmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $checkStmt->execute([$email]);
        
        if ($checkStmt->fetch()) {
            error_log("User already exists with email: $email");
            echo json_encode(['success' => false, 'message' => 'User with this email already exists']);
            return;
        }
        
        error_log("User doesn't exist, proceeding with creation...");
        
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        error_log("Password hashed successfully");
        
        // Generate verification token
        $verificationToken = bin2hex(random_bytes(32));
        error_log("Verification token generated: " . substr($verificationToken, 0, 10) . "...");
        
        // Insert user
        $stmt = $pdo->prepare("
            INSERT INTO users (email, password, full_name, phone, verification_token, created_at) 
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        
        error_log("Executing INSERT statement...");
        $result = $stmt->execute([$email, $hashedPassword, $fullName, $phone, $verificationToken]);
        
        if ($result) {
            $userId = $pdo->lastInsertId();
            error_log("User inserted successfully with ID: $userId");
            
            // Send verification email (simulate)
            sendVerificationEmail($email, $verificationToken);
            
            echo json_encode([
                'success' => true, 
                'message' => 'Account created successfully! Please check your email for verification.',
                'userId' => $userId
            ]);
            error_log("=== SIGNUP PROCESS COMPLETED SUCCESSFULLY ===");
        } else {
            error_log("INSERT statement failed");
            echo json_encode(['success' => false, 'message' => 'Failed to create account. Please try again.']);
        }
        
    } catch (PDOException $e) {
        error_log("Database error in signup: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    } catch (Exception $e) {
        error_log("General error in signup: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
    }
}

function handleSendOTP($pdo, $input) {
    $contact = $input['contact'] ?? '';
    
    if (empty($contact)) {
        echo json_encode(['success' => false, 'message' => 'Email or phone number is required']);
        return;
    }
    
    // Generate 6-digit OTP
    $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    $expiry = date('Y-m-d H:i:s', strtotime('+10 minutes'));
    
    try {
        // Check if user exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR phone = ?");
        $stmt->execute([$contact, $contact]);
        $user = $stmt->fetch();
        
        if (!$user) {
            echo json_encode(['success' => false, 'message' => 'User not found']);
            return;
        }
        
        // Store OTP
        $otpStmt = $pdo->prepare("
            INSERT INTO otp_codes (user_id, otp_code, contact, expires_at, created_at) 
            VALUES (?, ?, ?, ?, NOW())
            ON DUPLICATE KEY UPDATE 
            otp_code = VALUES(otp_code), 
            expires_at = VALUES(expires_at), 
            created_at = NOW()
        ");
        
        $otpStmt->execute([$user['id'], $otp, $contact, $expiry]);
        
        // Store OTP in session for verification
        $_SESSION['otp_user_id'] = $user['id'];
        $_SESSION['otp_contact'] = $contact;
        
        // Send OTP (simulate)
        sendOTP($contact, $otp);
        
        echo json_encode(['success' => true, 'message' => 'OTP sent successfully']);
        
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
}

function handleVerifyOTP($pdo, $input) {
    $otp = $input['otp'] ?? '';
    
    if (empty($otp) || strlen($otp) !== 6) {
        echo json_encode(['success' => false, 'message' => 'Please enter a valid 6-digit OTP']);
        return;
    }
    
    if (!isset($_SESSION['otp_user_id'])) {
        echo json_encode(['success' => false, 'message' => 'OTP session expired. Please request a new OTP']);
        return;
    }
    
    try {
        $stmt = $pdo->prepare("
            SELECT o.*, u.email, u.full_name 
            FROM otp_codes o 
            JOIN users u ON o.user_id = u.id 
            WHERE o.user_id = ? AND o.otp_code = ? AND o.expires_at > NOW()
            ORDER BY o.created_at DESC 
            LIMIT 1
        ");
        
        $stmt->execute([$_SESSION['otp_user_id'], $otp]);
        $otpRecord = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($otpRecord) {
            // Set user session
            $_SESSION['user_id'] = $otpRecord['user_id'];
            $_SESSION['user_email'] = $otpRecord['email'];
            $_SESSION['user_name'] = $otpRecord['full_name'];
            
            // Mark OTP as used
            $deleteStmt = $pdo->prepare("DELETE FROM otp_codes WHERE user_id = ?");
            $deleteStmt->execute([$otpRecord['user_id']]);
            
            // Clean up OTP session variables
            unset($_SESSION['otp_user_id']);
            unset($_SESSION['otp_contact']);
            
            echo json_encode([
                'success' => true, 
                'message' => 'OTP verified successfully',
                'redirect' => 'dashboard.php'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid or expired OTP']);
        }
        
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
}

function handleForgotPassword($pdo, $input) {
    $email = $input['email'] ?? '';
    
    if (empty($email)) {
        echo json_encode(['success' => false, 'message' => 'Email is required']);
        return;
    }
    
    try {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user) {
            // Generate reset token
            $resetToken = bin2hex(random_bytes(32));
            $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Store reset token
            $tokenStmt = $pdo->prepare("
                INSERT INTO password_resets (user_id, token, expires_at, created_at) 
                VALUES (?, ?, ?, NOW())
                ON DUPLICATE KEY UPDATE 
                token = VALUES(token), 
                expires_at = VALUES(expires_at), 
                created_at = NOW()
            ");
            
            $tokenStmt->execute([$user['id'], $resetToken, $expiry]);
            
            // Send reset email (simulate)
            sendPasswordResetEmail($email, $resetToken);
        }
        
        // Always return success to prevent email enumeration
        echo json_encode(['success' => true, 'message' => 'Password reset link sent']);
        
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
}

// Utility functions (simulate email/SMS sending)
function sendVerificationEmail($email, $token) {
    // In a real application, integrate with email service like SendGrid, Mailgun, etc.
    error_log("Verification email sent to $email with token: $token");
}

function sendOTP($contact, $otp) {
    // In a real application, integrate with SMS service like Twilio, or email service
    error_log("OTP sent to $contact: $otp");
}

function sendPasswordResetEmail($email, $token) {
    // In a real application, integrate with email service
    error_log("Password reset email sent to $email with token: $token");
}
?>