<?php
echo "<h1>🔍 Comprehensive Database Check for yesist12_auth</h1>";

// Database configuration
$host = 'localhost';
$username = 'root';
$password = 'root';
$database = 'yesist12_auth';

echo "<h2>Step 1: Connection Test</h2>";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p>✅ Successfully connected to database '$database'</p>";
    
    echo "<h2>Step 2: Existing Tables Check</h2>";
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (count($tables) > 0) {
        echo "<p>✅ Found " . count($tables) . " existing tables:</p>";
        echo "<ul>";
        foreach ($tables as $table) {
            echo "<li><strong>$table</strong></li>";
            
            // Check table structure
            $descStmt = $pdo->query("DESCRIBE $table");
            $columns = $descStmt->fetchAll(PDO::FETCH_ASSOC);
            echo "<ul>";
            foreach ($columns as $column) {
                echo "<li>{$column['Field']} ({$column['Type']})</li>";
            }
            echo "</ul>";
        }
        echo "</ul>";
    } else {
        echo "<p>⚠️ No tables found in database</p>";
    }
    
    echo "<h2>Step 3: Required Tables Check</h2>";
    $requiredTables = ['users', 'otp_codes', 'password_resets'];
    $missingTables = [];
    
    foreach ($requiredTables as $table) {
        if (in_array($table, $tables)) {
            echo "<p>✅ Table '$table' exists</p>";
            
            // Check if users table has data
            if ($table === 'users') {
                $countStmt = $pdo->query("SELECT COUNT(*) as count FROM users");
                $userCount = $countStmt->fetch()['count'];
                echo "<p>   📊 Contains $userCount user(s)</p>";
                
                if ($userCount > 0) {
                    echo "<p>   👥 Users in database:</p>";
                    $usersStmt = $pdo->query("SELECT id, email, full_name, is_verified, created_at FROM users");
                    $users = $usersStmt->fetchAll(PDO::FETCH_ASSOC);
                    echo "<ul>";
                    foreach ($users as $user) {
                        $verified = $user['is_verified'] ? '✅ Verified' : '❌ Not Verified';
                        echo "<li>ID: {$user['id']} | {$user['email']} | {$user['full_name']} | $verified</li>";
                    }
                    echo "</ul>";
                }
            }
        } else {
            echo "<p>❌ Table '$table' is missing</p>";
            $missingTables[] = $table;
        }
    }
    
    if (!empty($missingTables)) {
        echo "<h2>Step 4: Creating Missing Tables</h2>";
        
        if (in_array('users', $missingTables)) {
            echo "<p>Creating 'users' table...</p>";
            $pdo->exec("
                CREATE TABLE users (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    email VARCHAR(255) UNIQUE NOT NULL,
                    password VARCHAR(255) NOT NULL,
                    full_name VARCHAR(255) NOT NULL,
                    phone VARCHAR(20),
                    is_verified BOOLEAN DEFAULT FALSE,
                    verification_token VARCHAR(64),
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    last_login TIMESTAMP NULL,
                    INDEX idx_email (email),
                    INDEX idx_verification_token (verification_token)
                )
            ");
            echo "<p>✅ Users table created</p>";
        }
        
        if (in_array('otp_codes', $missingTables)) {
            echo "<p>Creating 'otp_codes' table...</p>";
            $pdo->exec("
                CREATE TABLE otp_codes (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    user_id INT NOT NULL,
                    otp_code VARCHAR(6) NOT NULL,
                    contact VARCHAR(255) NOT NULL,
                    expires_at TIMESTAMP NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                    UNIQUE KEY unique_user_otp (user_id),
                    INDEX idx_expires_at (expires_at)
                )
            ");
            echo "<p>✅ OTP codes table created</p>";
        }
        
        if (in_array('password_resets', $missingTables)) {
            echo "<p>Creating 'password_resets' table...</p>";
            $pdo->exec("
                CREATE TABLE password_resets (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    user_id INT NOT NULL,
                    token VARCHAR(64) NOT NULL,
                    expires_at TIMESTAMP NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                    UNIQUE KEY unique_user_reset (user_id),
                    INDEX idx_token (token),
                    INDEX idx_expires_at (expires_at)
                )
            ");
            echo "<p>✅ Password resets table created</p>";
        }
    }
    
    echo "<h2>Step 5: Test User Check</h2>";
    $testUserStmt = $pdo->prepare("SELECT * FROM users WHERE email = 'test@yesist12.com'");
    $testUserStmt->execute();
    $testUser = $testUserStmt->fetch();
    
    if ($testUser) {
        echo "<p>✅ Test user exists</p>";
        echo "<p>   📧 Email: {$testUser['email']}</p>";
        echo "<p>   👤 Name: {$testUser['full_name']}</p>";
        echo "<p>   ✅ Verified: " . ($testUser['is_verified'] ? 'Yes' : 'No') . "</p>";
    } else {
        echo "<p>❌ Test user doesn't exist, creating...</p>";
        
        $hashedPassword = password_hash('password123', PASSWORD_DEFAULT);
        $verificationToken = bin2hex(random_bytes(32));
        
        $stmt = $pdo->prepare("
            INSERT INTO users (email, password, full_name, phone, is_verified, verification_token) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        $result = $stmt->execute([
            'test@yesist12.com',
            $hashedPassword,
            'Test User',
            '+1234567890',
            1, // Already verified for testing
            $verificationToken
        ]);
        
        if ($result) {
            echo "<p>✅ Test user created successfully</p>";
        } else {
            echo "<p>❌ Failed to create test user</p>";
        }
    }
    
    echo "<h2>Step 6: Authentication Test</h2>";
    
    // Test password verification
    if ($testUser || $result) {
        $loginStmt = $pdo->prepare("SELECT * FROM users WHERE email = 'test@yesist12.com'");
        $loginStmt->execute();
        $user = $loginStmt->fetch();
        
        if ($user && password_verify('password123', $user['password'])) {
            echo "<p>✅ Password verification works correctly</p>";
        } else {
            echo "<p>❌ Password verification failed</p>";
        }
    }
    
    echo "<div style='background: #d4edda; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
    echo "<h2>🎉 Database Check Complete!</h2>";
    echo "<p><strong>Your yesist12_auth database is properly configured!</strong></p>";
    echo "<p><strong>Test Credentials:</strong></p>";
    echo "<p>📧 Email: test@yesist12.com</p>";
    echo "<p>🔑 Password: password123</p>";
    echo "<p><a href='index.html' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🔐 Go to Login Page</a></p>";
    echo "</div>";
    
} catch (PDOException $e) {
    echo "<div style='background: #f8d7da; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
    echo "<h2>❌ Database Connection Failed</h2>";
    echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>Error Code:</strong> " . $e->getCode() . "</p>";
    
    echo "<h3>🔧 Possible Solutions:</h3>";
    echo "<ul>";
    echo "<li>Check if MySQL service is running</li>";
    echo "<li>Verify database credentials (username: root, password: root)</li>";
    echo "<li>Make sure yesist12_auth database exists</li>";
    echo "<li>Check if port 3306 is available</li>";
    echo "</ul>";
    echo "</div>";
}
?>

<style>
body {
    font-family: Arial, sans-serif;
    max-width: 900px;
    margin: 0 auto;
    padding: 20px;
    background-color: #f5f5f5;
}

h1, h2 {
    color: #2c3e50;
}

p {
    margin: 8px 0;
}

ul {
    margin: 8px 0;
    padding-left: 20px;
}

li {
    margin: 4px 0;
}

a {
    display: inline-block;
    margin: 5px 10px 5px 0;
}
</style>