<?php
// Database Setup for YESIST12 Authentication System

$host = 'localhost';
$username = 'root';
$password = 'root';

try {
    // Connect to MySQL server
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h1>🗄️ Setting up YESIST12 Authentication Database</h1>";
    
    // Create database
    echo "<p>Creating database 'yesist12_auth'...</p>";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS yesist12_auth");
    echo "<p>✅ Database created successfully!</p>";
    
    // Use the database
    $pdo->exec("USE yesist12_auth");
    
    // Create users table
    echo "<p>Creating 'users' table...</p>";
    $pdo->exec("
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
    echo "<p>✅ Users table created successfully!</p>";
    
    // Create OTP codes table
    echo "<p>Creating 'otp_codes' table...</p>";
    $pdo->exec("
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
    echo "<p>✅ OTP codes table created successfully!</p>";
    
    // Create password resets table
    echo "<p>Creating 'password_resets' table...</p>";
    $pdo->exec("
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
    echo "<p>✅ Password resets table created successfully!</p>";
    
    // Create sessions table (optional, for better session management)
    echo "<p>Creating 'user_sessions' table...</p>";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS user_sessions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            session_id VARCHAR(128) NOT NULL,
            ip_address VARCHAR(45),
            user_agent TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            expires_at TIMESTAMP NOT NULL,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            INDEX idx_session_id (session_id),
            INDEX idx_expires_at (expires_at)
        )
    ");
    echo "<p>✅ User sessions table created successfully!</p>";
    
    // Insert sample data
    echo "<p>Inserting sample test user...</p>";
    
    $hashedPassword = password_hash('password123', PASSWORD_DEFAULT);
    $verificationToken = bin2hex(random_bytes(32));
    
    $stmt = $pdo->prepare("
        INSERT IGNORE INTO users (email, password, full_name, phone, is_verified, verification_token) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    
    $stmt->execute([
        'test@yesist12.com',
        $hashedPassword,
        'Test User',
        '+1234567890',
        1, // Already verified for testing
        $verificationToken
    ]);
    
    echo "<p>✅ Sample user created!</p>";
    echo "<div style='background: #f0f8ff; padding: 15px; border-radius: 8px; margin: 20px 0;'>";
    echo "<h3>🔑 Test User Credentials:</h3>";
    echo "<p><strong>Email:</strong> test@yesist12.com</p>";
    echo "<p><strong>Password:</strong> password123</p>";
    echo "</div>";
    
    // Create cleanup procedures
    echo "<p>Creating cleanup procedures...</p>";
    
    // Procedure to clean expired OTPs
    $pdo->exec("
        CREATE EVENT IF NOT EXISTS cleanup_expired_otps
        ON SCHEDULE EVERY 1 HOUR
        DO
        DELETE FROM otp_codes WHERE expires_at < NOW()
    ");
    
    // Procedure to clean expired password resets
    $pdo->exec("
        CREATE EVENT IF NOT EXISTS cleanup_expired_resets
        ON SCHEDULE EVERY 1 HOUR
        DO
        DELETE FROM password_resets WHERE expires_at < NOW()
    ");
    
    // Procedure to clean expired sessions
    $pdo->exec("
        CREATE EVENT IF NOT EXISTS cleanup_expired_sessions
        ON SCHEDULE EVERY 1 HOUR
        DO
        DELETE FROM user_sessions WHERE expires_at < NOW()
    ");
    
    echo "<p>✅ Cleanup procedures created!</p>";
    
    // Display database structure
    echo "<h2>📋 Database Structure Created:</h2>";
    
    $tables = ['users', 'otp_codes', 'password_resets', 'user_sessions'];
    
    foreach ($tables as $table) {
        echo "<h3>Table: $table</h3>";
        $stmt = $pdo->prepare("DESCRIBE $table");
        $stmt->execute();
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<table border='1' style='border-collapse: collapse; width: 100%; margin-bottom: 20px;'>";
        echo "<tr style='background-color: #f0f0f0;'>";
        echo "<th style='padding: 8px;'>Field</th>";
        echo "<th style='padding: 8px;'>Type</th>";
        echo "<th style='padding: 8px;'>Null</th>";
        echo "<th style='padding: 8px;'>Key</th>";
        echo "<th style='padding: 8px;'>Default</th>";
        echo "</tr>";
        
        foreach ($columns as $column) {
            echo "<tr>";
            echo "<td style='padding: 8px;'>{$column['Field']}</td>";
            echo "<td style='padding: 8px;'>{$column['Type']}</td>";
            echo "<td style='padding: 8px;'>{$column['Null']}</td>";
            echo "<td style='padding: 8px;'>{$column['Key']}</td>";
            echo "<td style='padding: 8px;'>{$column['Default']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    echo "<div style='background: #d4edda; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
    echo "<h2>🎉 Database Setup Complete!</h2>";
    echo "<p>Your YESIST12 authentication system is ready to use.</p>";
    echo "<p><strong>Next steps:</strong></p>";
    echo "<ul>";
    echo "<li>Visit <a href='index.html'>index.html</a> to test the login system</li>";
    echo "<li>Configure email/SMS services in auth_handler.php for production</li>";
    echo "<li>Update database credentials as needed</li>";
    echo "<li>Add SSL certificates for secure production deployment</li>";
    echo "</ul>";
    echo "</div>";
    
} catch (PDOException $e) {
    echo "<div style='background: #f8d7da; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
    echo "<h2>❌ Database Setup Failed</h2>";
    echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>Solutions:</strong></p>";
    echo "<ul>";
    echo "<li>Make sure MySQL server is running</li>";
    echo "<li>Check database credentials (host, username, password)</li>";
    echo "<li>Ensure MySQL user has CREATE DATABASE privileges</li>";
    echo "</ul>";
    echo "</div>";
}
?>

<style>
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
    background-color: #f5f5f5;
}

h1, h2, h3 {
    color: #2c3e50;
}

table {
    background-color: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

th {
    background-color: #3498db !important;
    color: white !important;
}

tr:nth-child(even) {
    background-color: #f8f9fa;
}

a {
    color: #007bff;
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}
</style>