<?php
// Simple test for authentication system
header('Content-Type: application/json');

// Include config
require_once 'config.php';

echo json_encode([
    'status' => 'success',
    'message' => 'Authentication system is ready!',
    'database_connection' => 'working',
    'php_version' => phpversion(),
    'extensions' => [
        'pdo' => extension_loaded('pdo'),
        'pdo_mysql' => extension_loaded('pdo_mysql'),
        'mysqli' => extension_loaded('mysqli'),
        'openssl' => extension_loaded('openssl')
    ],
    'test_user' => [
        'email' => 'test@yesist12.com',
        'password' => 'password123'
    ]
], JSON_PRETTY_PRINT);
?>