<?php
require_once 'config.php';

try {
    $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USERNAME, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if user_sessions table exists
    $result = $pdo->query("SHOW TABLES LIKE 'user_sessions'");
    
    if ($result->rowCount() > 0) {
        echo "✅ user_sessions table exists\n";
        echo "\nCurrent structure:\n";
        $desc = $pdo->query("DESCRIBE user_sessions");
        while ($row = $desc->fetch(PDO::FETCH_ASSOC)) {
            echo "- {$row['Field']}: {$row['Type']} ({$row['Null']}, {$row['Key']})\n";
        }
        
        // Check if session_token column exists
        $columns = $pdo->query("SHOW COLUMNS FROM user_sessions LIKE 'session_token'");
        if ($columns->rowCount() == 0) {
            echo "\n❌ Missing session_token column - Adding it...\n";
            $pdo->exec("ALTER TABLE user_sessions ADD COLUMN session_token VARCHAR(255) NOT NULL AFTER user_id");
            echo "✅ Added session_token column\n";
        } else {
            echo "\n✅ session_token column exists\n";
        }
        
    } else {
        echo "❌ user_sessions table does not exist - Creating it...\n";
        $pdo->exec("
            CREATE TABLE user_sessions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                session_token VARCHAR(255) NOT NULL,
                expires_at DATETIME NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY unique_user_session (user_id),
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            )
        ");
        echo "✅ user_sessions table created successfully!\n";
    }
    
    echo "\n✅ Database check completed!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}