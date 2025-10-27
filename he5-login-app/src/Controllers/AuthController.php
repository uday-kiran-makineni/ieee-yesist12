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
            
            Router::LOGGER()->info("User logged in successfully", ['user_id' => $user['id'], 'email' => $email]);
            
            return [
                'success' => true,
                'message' => 'Login successful!',
                'user' => [
                    'id' => $user['id'],
                    'email' => $email,
                    'name' => $user['full_name']
                ]
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
            
            // Destroy session
            session_destroy();
            
            Router::LOGGER()->info("User logged out", ['user_id' => $userId]);
            
            return [
                'success' => true,
                'message' => 'Logged out successfully'
            ];
            
        } catch (Exception $e) {
            Router::LOGGER()->error("Logout error: " . $e->getMessage());
            throw new He5Exception("Logout failed", 500, 500);
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