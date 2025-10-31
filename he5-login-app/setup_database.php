<?php
// Database Setup Script for He5 Login App
require_once __DIR__ . "/config.php";

try {
    // Connect to MySQL server (without database name)
    $dsn = "mysql:host=" . DB_HOST . ";charset=utf8mb4";
    $pdo = new PDO($dsn, DB_USERNAME, DB_PASSWORD, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    
    echo "Connected to MySQL server successfully.\n";
    
    // Create database if it doesn't exist
    $createDbSql = "CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    $pdo->exec($createDbSql);
    echo "Database '" . DB_NAME . "' created or verified.\n";
    
    // Connect to the specific database
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $pdo = new PDO($dsn, DB_USERNAME, DB_PASSWORD, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    
    // Create users table
    $createUsersTable = "
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            full_name VARCHAR(255) NOT NULL,
            phone VARCHAR(20),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            last_login TIMESTAMP NULL,
            INDEX idx_email (email)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";
    
    $pdo->exec($createUsersTable);
    echo "Users table created or verified.\n";
    
    // Create user_sessions table
    $createSessionsTable = "
        CREATE TABLE IF NOT EXISTS user_sessions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            session_token VARCHAR(255) NOT NULL,
            session_reference VARCHAR(255),
            expires_at TIMESTAMP NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            INDEX idx_user_id (user_id),
            INDEX idx_session_token (session_token),
            INDEX idx_expires_at (expires_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";
    
    $pdo->exec($createSessionsTable);
    echo "User sessions table created or verified.\n";
    
    // Insert a test user if users table is empty
    $countStmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $count = $countStmt->fetch()['count'];
    
    if ($count == 0) {
        $testPassword = password_hash('password123', PASSWORD_DEFAULT);
        $insertTestUser = "
            INSERT INTO users (email, password, full_name, phone) 
            VALUES ('test@example.com', ?, 'Test User', '+1-234-567-8900')
        ";
        $stmt = $pdo->prepare($insertTestUser);
        $stmt->execute([$testPassword]);
        echo "Test user created (email: test@example.com, password: password123).\n";
    }
    
    echo "\n✅ Database setup completed successfully!\n";
    echo "You can now run the application.\n";
    
} catch (PDOException $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
    exit(1);
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>