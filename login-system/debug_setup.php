<?php
echo "<h1>🐛 Debug Database Setup</h1>";

// Database configuration
$host = 'localhost';
$username = 'root';
$password = 'root';

echo "<h2>Step-by-Step Database Setup Debug</h2>";

try {
    echo "<p><strong>Step 1:</strong> Connecting to MySQL server...</p>";
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p>✅ Connected to MySQL server successfully!</p>";
    
    echo "<p><strong>Step 2:</strong> Creating database 'yesist12_auth'...</p>";
    $result = $pdo->exec("CREATE DATABASE IF NOT EXISTS yesist12_auth");
    echo "<p>✅ Database creation command executed!</p>";
    
    echo "<p><strong>Step 3:</strong> Selecting database...</p>";
    $pdo->exec("USE yesist12_auth");
    echo "<p>✅ Database selected successfully!</p>";
    
    echo "<p><strong>Step 4:</strong> Creating 'users' table...</p>";
    $create_users = $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
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
    echo "<p>✅ Users table created!</p>";
    
    echo "<p><strong>Step 5:</strong> Creating 'otp_codes' table...</p>";
    $create_otp = $pdo->exec("
        CREATE TABLE IF NOT EXISTS otp_codes (
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
    echo "<p>✅ OTP codes table created!</p>";
    
    echo "<p><strong>Step 6:</strong> Creating 'password_resets' table...</p>";
    $create_resets = $pdo->exec("
        CREATE TABLE IF NOT EXISTS password_resets (
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
    echo "<p>✅ Password resets table created!</p>";
    
    echo "<p><strong>Step 7:</strong> Creating test user...</p>";
    $hashedPassword = password_hash('password123', PASSWORD_DEFAULT);
    $verificationToken = bin2hex(random_bytes(32));
    
    $stmt = $pdo->prepare("
        INSERT IGNORE INTO users (email, password, full_name, phone, is_verified, verification_token) 
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
        echo "<p>✅ Test user created/updated!</p>";
    }
    
    echo "<p><strong>Step 8:</strong> Verifying setup...</p>";
    
    // Check tables
    $tables_stmt = $pdo->query("SHOW TABLES");
    $tables = $tables_stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "<p>✅ Found " . count($tables) . " tables: " . implode(', ', $tables) . "</p>";
    
    // Check users
    $users_stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $user_count = $users_stmt->fetch()['count'];
    echo "<p>✅ Found $user_count user(s) in database</p>";
    
    echo "<div style='background: #d4edda; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
    echo "<h2>🎉 Database Setup Complete!</h2>";
    echo "<p><strong>Test Credentials:</strong></p>";
    echo "<p>Email: test@yesist12.com</p>";
    echo "<p>Password: password123</p>";
    echo "<p><a href='index.html' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🔐 Go to Login Page</a></p>";
    echo "</div>";
    
} catch (PDOException $e) {
    echo "<div style='background: #f8d7da; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
    echo "<h2>❌ Database Setup Failed</h2>";
    echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>Error Code:</strong> " . $e->getCode() . "</p>";
    echo "<p><strong>File:</strong> " . $e->getFile() . "</p>";
    echo "<p><strong>Line:</strong> " . $e->getLine() . "</p>";
    
    echo "<h3>🔧 Troubleshooting Steps:</h3>";
    echo "<ol>";
    echo "<li>Check if MySQL is running: <code>Get-Service -Name '*mysql*'</code></li>";
    echo "<li>Try connecting to MySQL manually</li>";
    echo "<li>Check if username 'root' and password 'root' are correct</li>";
    echo "<li>Try using '127.0.0.1' instead of 'localhost'</li>";
    echo "<li>Check if port 3306 is available</li>";
    echo "</ol>";
    echo "</div>";
}

// Test connection to auth_handler
echo "<h2>🔗 Testing Auth Handler Connection</h2>";
try {
    $test_pdo = new PDO("mysql:host=$host;dbname=yesist12_auth", $username, $password);
    $test_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p>✅ Auth handler can connect to database!</p>";
} catch (PDOException $e) {
    echo "<p>❌ Auth handler cannot connect: " . $e->getMessage() . "</p>";
}
?>

<style>
body {
    font-family: Arial, sans-serif;
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
    background-color: #f5f5f5;
}

h1, h2, h3 {
    color: #2c3e50;
}

p {
    margin: 10px 0;
}

ol, ul {
    margin: 10px 0;
    padding-left: 20px;
}

code {
    background: #f8f9fa;
    padding: 2px 6px;
    border-radius: 3px;
    font-family: monospace;
}
</style>