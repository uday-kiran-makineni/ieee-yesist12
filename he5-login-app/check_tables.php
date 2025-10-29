<?php
require_once 'He5Framework.php';
require_once 'config.php';

// Initialize logger
$logger = new Logger(LOGS_PATH);

// Create router instance
$router = new Router(__DIR__ . "/src/Views", $logger);

try {
    $db = Router::DB();
    
    // Check if user_sessions table exists
    $result = $db->query("SHOW TABLES LIKE 'user_sessions'");
    echo "user_sessions table exists: " . ($result->rowCount() > 0 ? 'Yes' : 'No') . "\n";
    
    if ($result->rowCount() > 0) {
        echo "\nuser_sessions table structure:\n";
        $desc = $db->query("DESCRIBE user_sessions");
        while ($row = $desc->fetch(PDO::FETCH_ASSOC)) {
            echo "- {$row['Field']}: {$row['Type']} ({$row['Null']}, {$row['Key']})\n";
        }
    } else {
        echo "\nCreating user_sessions table...\n";
        $db->exec("
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
    
    // Check users table structure for compatibility
    echo "\nusers table structure:\n";
    $desc = $db->query("DESCRIBE users");
    while ($row = $desc->fetch(PDO::FETCH_ASSOC)) {
        echo "- {$row['Field']}: {$row['Type']}\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}