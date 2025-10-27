<?php
require_once __DIR__ . "/config.php";

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== Users Table Structure ===\n";
    $stmt = $pdo->query('DESCRIBE users');
    while($row = $stmt->fetch()) {
        echo $row['Field'] . ' - ' . $row['Type'] . "\n";
    }
    
    echo "\n=== Sample User Data ===\n";
    $stmt = $pdo->query('SELECT * FROM users LIMIT 1');
    $user = $stmt->fetch();
    if ($user) {
        foreach ($user as $key => $value) {
            if (is_string($key)) {
                echo "$key: " . (strpos($key, 'password') !== false ? '[HIDDEN]' : $value) . "\n";
            }
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>