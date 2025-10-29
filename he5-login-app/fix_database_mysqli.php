<?php
// Database fix script using mysqli
require_once 'config.php';

try {
    // Connect using mysqli (same as original login system)
    $mysqli = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
    
    if ($mysqli->connect_error) {
        die("❌ Connection failed: " . $mysqli->connect_error);
    }
    
    echo "✅ Connected to database successfully\n";
    
    // Check if user_sessions table exists
    $result = $mysqli->query("SHOW TABLES LIKE 'user_sessions'");
    
    if ($result->num_rows > 0) {
        echo "✅ user_sessions table exists\n";
        
        // Check current structure
        echo "\nCurrent structure:\n";
        $desc = $mysqli->query("DESCRIBE user_sessions");
        $hasSessionToken = false;
        
        while ($row = $desc->fetch_assoc()) {
            echo "- {$row['Field']}: {$row['Type']}\n";
            if ($row['Field'] == 'session_token') {
                $hasSessionToken = true;
            }
        }
        
        if (!$hasSessionToken) {
            echo "\n❌ Missing session_token column - Adding it...\n";
            $mysqli->query("ALTER TABLE user_sessions ADD COLUMN session_token VARCHAR(255) NOT NULL AFTER user_id");
            echo "✅ Added session_token column\n";
        } else {
            echo "\n✅ session_token column exists\n";
        }
        
    } else {
        echo "❌ user_sessions table does not exist - Creating it...\n";
        $createTable = "
            CREATE TABLE user_sessions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                session_token VARCHAR(255) NOT NULL,
                expires_at DATETIME NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY unique_user_session (user_id),
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            )
        ";
        
        if ($mysqli->query($createTable)) {
            echo "✅ user_sessions table created successfully!\n";
        } else {
            echo "❌ Error creating table: " . $mysqli->error . "\n";
        }
    }
    
    // Test with a simple select to make sure everything works
    $testResult = $mysqli->query("SELECT COUNT(*) as count FROM users");
    if ($testResult) {
        $row = $testResult->fetch_assoc();
        echo "\n✅ Database test successful - Found {$row['count']} users\n";
    }
    
    $mysqli->close();
    echo "\n✅ Database fix completed successfully!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}