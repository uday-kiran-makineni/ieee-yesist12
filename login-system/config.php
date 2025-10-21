<?php
// Configuration file for YESIST12 Authentication System

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'root');
define('DB_NAME', 'yesist12_auth');

// Application Configuration
define('APP_NAME', 'YESIST12');
define('APP_URL', 'http://localhost:8000');
define('APP_EMAIL', 'no-reply@yesist12.com');

// Security Configuration
define('PASSWORD_MIN_LENGTH', 8);
define('OTP_EXPIRY_MINUTES', 10);
define('RESET_TOKEN_EXPIRY_HOURS', 1);
define('SESSION_TIMEOUT_MINUTES', 60);

// Email Configuration (for production)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your-email@gmail.com');
define('SMTP_PASSWORD', 'your-app-password');
define('SMTP_ENCRYPTION', 'tls');

// SMS Configuration (for production)
define('TWILIO_SID', 'your-twilio-sid');
define('TWILIO_TOKEN', 'your-twilio-token');
define('TWILIO_PHONE', 'your-twilio-phone');

// Social Login Configuration
define('GOOGLE_CLIENT_ID', 'your-google-client-id');
define('GOOGLE_CLIENT_SECRET', 'your-google-client-secret');
define('GITHUB_CLIENT_ID', 'your-github-client-id');
define('GITHUB_CLIENT_SECRET', 'your-github-client-secret');

// Development/Production Mode
define('DEBUG_MODE', true); // Set to false in production
define('LOG_ERRORS', true);
define('ERROR_LOG_FILE', 'logs/error.log');

// File Upload Configuration
define('UPLOAD_MAX_SIZE', 5 * 1024 * 1024); // 5MB
define('UPLOAD_ALLOWED_TYPES', ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx']);
define('UPLOAD_PATH', 'uploads/');

// Utility function to get database connection
function getDBConnection() {
    try {
        // First check if required extensions are loaded
        if (!extension_loaded('pdo_mysql')) {
            throw new Exception('PDO MySQL extension is not loaded. Please enable it in php.ini');
        }
        
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
        ];
        
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        return $pdo;
    } catch (PDOException $e) {
        if (DEBUG_MODE) {
            throw new Exception("Database connection failed: " . $e->getMessage());
        } else {
            error_log("Database connection failed: " . $e->getMessage());
            throw new Exception("Database connection failed. Please try again later.");
        }
    } catch (Exception $e) {
        if (DEBUG_MODE) {
            throw $e;
        } else {
            error_log("Database setup error: " . $e->getMessage());
            throw new Exception("Database setup error. Please contact administrator.");
        }
    }
}

// Utility function to log errors
function logError($message, $context = []) {
    if (LOG_ERRORS) {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] $message";
        if (!empty($context)) {
            $logMessage .= " Context: " . json_encode($context);
        }
        $logMessage .= PHP_EOL;
        
        // Create logs directory if it doesn't exist
        $logDir = dirname(ERROR_LOG_FILE);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        file_put_contents(ERROR_LOG_FILE, $logMessage, FILE_APPEND | LOCK_EX);
    }
}

// Utility function to sanitize input
function sanitizeInput($input) {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

// Utility function to validate email
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Utility function to validate phone number
function isValidPhone($phone) {
    // Basic phone validation - adjust regex as needed
    return preg_match('/^\+?[1-9]\d{1,14}$/', $phone);
}

// Utility function to generate secure random token
function generateSecureToken($length = 32) {
    return bin2hex(random_bytes($length));
}

// Utility function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Utility function to require login
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: index.html');
        exit();
    }
}

// Utility function to get current user info
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['user_id'],
        'email' => $_SESSION['user_email'] ?? '',
        'name' => $_SESSION['user_name'] ?? '',
    ];
}

// Utility function to format response
function jsonResponse($success, $message, $data = null, $httpCode = 200) {
    http_response_code($httpCode);
    header('Content-Type: application/json');
    
    $response = [
        'success' => $success,
        'message' => $message,
    ];
    
    if ($data !== null) {
        $response['data'] = $data;
    }
    
    echo json_encode($response);
    exit();
}

// Error reporting settings
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Set timezone
date_default_timezone_set('UTC'); // Change to your timezone

// Set session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
ini_set('session.use_strict_mode', 1);
?>