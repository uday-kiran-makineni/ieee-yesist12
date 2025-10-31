<?php

/**
 * AuthController - IEEE YESIST12 Authentication System
 * 
 * Uses He5 Framework PHAR file for He5ED encryption and other utilities
 * 
 * @requires He5-Frame-work-1.0.3.phar
 * @uses He5ED - Encryption/Decryption class from He5 Framework
 * @uses TOKEN_ENCRYPTION_KEY - Encryption key from config.php
 */

// Import He5 Framework classes
require_once __DIR__ . '/../../He5-Frame-work-1.0.3.phar';
require_once __DIR__ . '/../../config.php';

// Include stubs for IDE support (actual classes come from PHAR)
require_once __DIR__ . '/../../he5_stubs.php';

class AuthController {
    
    public function showLogin() {
        include __DIR__ . '/../Views/login.php';
    }
    
    public function showSignup() {
        include __DIR__ . '/../Views/signup.php';
    }
    
    public function showDashboard() {
        // Check if user is authenticated
        if (!$this->isAuthenticated()) {
            header('Location: /login');
            exit;
        }
        
        include __DIR__ . '/../Views/dashboard.php';
    }
    
    public function login() {
        try {
            // Get JSON data from request body
            $input = json_decode(file_get_contents('php://input'), true);
            
            // Fallback to $_POST if JSON is not available
            $email = $input['email'] ?? $_POST['email'] ?? '';
            $password = $input['password'] ?? $_POST['password'] ?? '';
            
            // Validate required fields
            if (empty($email) || empty($password)) {
                throw new Exception('Email and password are required');
            }
            
            // Find user in database
            $db = SimpleRouter::DB();
            $stmt = $db->prepare("SELECT id, email, password, full_name, phone FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if (!$user) {
                throw new Exception('Invalid email or password');
            }
            
            // Verify password using He5ED decryption only
            try {
                /** @noinspection PhpUndefinedClassInspection */
                $decryptedPassword = He5ED::decryptData($user['password'], TOKEN_ENCRYPTION_KEY);
                if ($decryptedPassword !== $password) {
                    throw new Exception('Invalid email or password');
                }
            } catch (Exception $e) {
                throw new Exception('Invalid email or password');
            }
            
            // Create session token using He5ED encryption
            /** @noinspection PhpUndefinedClassInspection */
            $sessionToken = He5ED::encryptData(json_encode([
                'user_id' => $user['id'],
                'email' => $user['email'],
                'created_at' => time()
            ]), TOKEN_ENCRYPTION_KEY);
            
            // Start session and store data
            session_start();
            $_SESSION['auth_token'] = $sessionToken;
            $_SESSION['user_data'] = [
                'id' => $user['id'],
                'email' => $user['email'],
                'full_name' => $user['full_name'],
                'phone' => $user['phone']
            ];
            
            // Return JSON response
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Login successful',
                'token' => $sessionToken,
                'expires_in' => 3600, // 1 hour
                'redirect' => '/dashboard'
            ]);
            
        } catch (Exception $e) {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    
    public function signup() {
        try {
            // Get JSON data from request body
            $input = json_decode(file_get_contents('php://input'), true);
            
            // Fallback to $_POST if JSON is not available
            $email = $input['email'] ?? $_POST['email'] ?? '';
            $password = $input['password'] ?? $_POST['password'] ?? '';
            $fullName = $input['fullName'] ?? $_POST['fullName'] ?? '';
            $phone = $input['phone'] ?? $_POST['phone'] ?? '';
            
            // Validate required fields
            if (empty($email) || empty($password) || empty($fullName) || empty($phone)) {
                throw new Exception('All fields are required');
            }
            
            // Check if user already exists and create user
            $db = SimpleRouter::DB();
            
            // Check if user exists
            $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                throw new Exception('User with this email already exists');
            }
            
            // Encrypt password using He5ED encryption
            /** @noinspection PhpUndefinedClassInspection */
            $encryptedPassword = He5ED::encryptData($password, TOKEN_ENCRYPTION_KEY);
            
            // Insert new user
            $stmt = $db->prepare("INSERT INTO users (email, password, full_name, phone, created_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([$email, $encryptedPassword, $fullName, $phone]);
            $userId = $db->lastInsertId();
            
            // Create session token using He5ED encryption
            /** @noinspection PhpUndefinedClassInspection */
            $sessionToken = He5ED::encryptData(json_encode([
                'user_id' => $userId,
                'email' => $email,
                'created_at' => time()
            ]), TOKEN_ENCRYPTION_KEY);
            
            // Start session and store data
            session_start();
            $_SESSION['auth_token'] = $sessionToken;
            $_SESSION['user_data'] = [
                'id' => $userId,
                'email' => $email,
                'full_name' => $fullName,
                'phone' => $phone
            ];
            
            // Return JSON response
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Registration successful',
                'token' => $sessionToken,
                'expires_in' => 3600, // 1 hour
                'redirect' => '/dashboard'
            ]);
            
        } catch (Exception $e) {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    
    public function logout() {
        try {
            session_start();
            session_destroy();
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Logged out successfully',
                'redirect' => '/login'
            ]);
            
        } catch (Exception $e) {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'System error occurred'
            ]);
        }
    }
    
    public function apiLogin() {
        $this->login();
    }
    
    public function apiSignup() {
        $this->signup();
    }
    
    public function apiLogout() {
        $this->logout();
    }
    
    private function isAuthenticated() {
        session_start();
        return isset($_SESSION['auth_token']) && isset($_SESSION['user_data']);
    }
}
?>