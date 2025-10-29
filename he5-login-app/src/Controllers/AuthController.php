<?php
class AuthController {
    
    // Page Methods (render views)
    public function loginPage(): void {
        $redirect = He5::getParamValue('redirect') ?? SITE_PATH . '/dashboard';
        Router::getInstance()->getView()->render('login', ['redirect' => $redirect]);
    }
    

    
    public function dashboardPage(): void {
        Router::getInstance()->getView()->render('dashboard', [
            'user_id' => Router::getInstance()->getUserId()
        ]);
    }
    
    // API Methods (return arrays)
    public function signup(): array {
        try {
            $email = He5::getParamValue('email');
            $password = He5::getParamValue('password');
            $fullName = He5::getParamValue('fullName');
            $phone = He5::getParamValue('phone');
            
            // Check if user already exists
            $stmt = Router::DB()->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->fetch()) {
                throw new He5Exception("User already exists with this email", 400, 400);
            }
            
            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert user
            $stmt = Router::DB()->prepare("
                INSERT INTO users (email, password, full_name, phone, created_at) 
                VALUES (?, ?, ?, ?, NOW())
            ");
            
            $stmt->execute([$email, $hashedPassword, $fullName, $phone]);
            $userId = Router::DB()->lastInsertId();
            
            Router::LOGGER()->info("User registered successfully", ['user_id' => $userId, 'email' => $email]);
            
            return [
                'success' => true,
                'message' => 'Registration successful! Please login.',
                'user_id' => $userId
            ];
            
        } catch (He5Exception $e) {
            Router::LOGGER()->error("Signup error: " . $e->getMessage());
            throw $e;
        } catch (Exception $e) {
            Router::LOGGER()->error("Signup unexpected error: " . $e->getMessage());
            throw new He5Exception("Registration failed. Please try again.", 500, 500);
        }
    }
    
    public function signin(): array {
        try {
            $email = He5::getParamValue('email');
            $password = He5::getParamValue('password');
            
            // Get user
            $stmt = Router::DB()->prepare("SELECT id, password, full_name FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if (!$user || !password_verify($password, $user['password'])) {
                throw new He5Exception("Invalid email or password", 401, 401);
            }
            
            // Create session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $email;
            $_SESSION['user_name'] = $user['full_name'];
            
            // Generate authentication token
            $authToken = He5::generateUserToken($user['id']);
            
            // Store token in database for tracking
            $stmt = Router::DB()->prepare("
                INSERT INTO user_sessions (user_id, session_token, expires_at) 
                VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 24 HOUR))
                ON DUPLICATE KEY UPDATE 
                session_token = VALUES(session_token), 
                expires_at = VALUES(expires_at)
            ");
            $stmt->execute([$user['id'], hash('sha256', $authToken)]);
            
            // Update last login
            $stmt = Router::DB()->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
            $stmt->execute([$user['id']]);
            
            Router::LOGGER()->info("User logged in successfully", [
                'user_id' => $user['id'], 
                'email' => $email,
                'auth_method' => 'password',
                'token_generated' => true
            ]);
            
            return [
                'success' => true,
                'message' => 'Login successful!',
                'user' => [
                    'id' => $user['id'],
                    'email' => $email,
                    'name' => $user['full_name']
                ],
                'token' => $authToken,
                'expires_in' => 24 * 60 * 60 // 24 hours in seconds
            ];
            
        } catch (He5Exception $e) {
            Router::LOGGER()->error("Signin error: " . $e->getMessage());
            throw $e;
        } catch (Exception $e) {
            Router::LOGGER()->error("Signin unexpected error: " . $e->getMessage());
            throw new He5Exception("Login failed. Please try again.", 500, 500);
        }
    }
    
    public function logout(): array {
        try {
            $userId = Router::getInstance()->getUserId();
            $authMethod = Router::getInstance()->getAuthMethod();
            
            // If using token authentication, invalidate the token
            if ($authMethod === 'token') {
                $token = He5::getTokenFromHeaders();
                if ($token) {
                    $tokenHash = hash('sha256', $token);
                    // Remove token from database
                    $stmt = Router::DB()->prepare("DELETE FROM user_sessions WHERE user_id = ? AND session_token = ?");
                    $stmt->execute([$userId, $tokenHash]);
                }
            }
            
            // Destroy session (if exists)
            if (session_status() === PHP_SESSION_ACTIVE) {
                session_destroy();
            }
            
            Router::LOGGER()->info("User logged out", [
                'user_id' => $userId,
                'auth_method' => $authMethod
            ]);
            
            return [
                'success' => true,
                'message' => 'Logged out successfully'
            ];
            
        } catch (Exception $e) {
            Router::LOGGER()->error("Logout error: " . $e->getMessage());
            
            // For logout, we should still return success even if there are server errors
            // The client should be able to logout locally
            return [
                'success' => true,
                'message' => 'Logged out (with server warnings)',
                'warning' => 'Some cleanup operations failed but logout completed'
            ];
        }
    }
    
    public function validateToken(): array {
        try {
            $userId = Router::getInstance()->getUserId();
            $authMethod = Router::getInstance()->getAuthMethod();
            
            if (!$userId) {
                throw new He5Exception("Invalid or expired token", 401, 401);
            }
            
            // Get user details
            $stmt = Router::DB()->prepare("SELECT id, email, full_name FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch();
            
            return [
                'success' => true,
                'valid' => true,
                'auth_method' => $authMethod,
                'user' => [
                    'id' => $user['id'],
                    'email' => $user['email'],
                    'name' => $user['full_name']
                ]
            ];
            
        } catch (He5Exception $e) {
            return [
                'success' => false,
                'valid' => false,
                'error' => $e->getMessage()
            ];
        } catch (Exception $e) {
            Router::LOGGER()->error("Token validation error: " . $e->getMessage());
            return [
                'success' => false,
                'valid' => false,
                'error' => 'Token validation failed'
            ];
        }
    }
    
    public function getProfile(): array {
        try {
            $userId = Router::getInstance()->getUserId();
            
            $stmt = Router::DB()->prepare("SELECT id, email, full_name, phone, created_at FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch();
            
            if (!$user) {
                throw new He5Exception("User not found", 404, 404);
            }
            
            return [
                'success' => true,
                'data' => $user
            ];
            
        } catch (He5Exception $e) {
            throw $e;
        } catch (Exception $e) {
            Router::LOGGER()->error("Get profile error: " . $e->getMessage());
            throw new He5Exception("Failed to get profile", 500, 500);
        }
    }
}
?>