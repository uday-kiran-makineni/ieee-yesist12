<?php
// HE5 INTERNAL & SERVICES USE - He5 Framework Configuration
// Database Configuration
define("DB_HOST", 'localhost');
define("DB_NAME", 'yesist12_auth');
define("DB_USERNAME", 'root');
define("DB_PASSWORD", 'root');
define("MYSQL_UTCTIMEZONE", 'UTC');

// Application Configuration
define("SITE_PATH", 'http://localhost:8000');
define("BASE_DIR_PATH", "he5-login-app/public_html");
define("PHP_TIMEZONE", 'Asia/Kolkata');
define("LOGS_PATH", "/logs");

// Security Keys
define("TOKEN_ENCRYPTION_KEY", 'yesist12-encryption-key-2024');
define("DB_KEY", "yesist12-db-key");
define("PRIVATE_KEY", "yesist12-private-key");

// Helper function for consistent defines
function define_once($name, $value) {
    if (!defined($name)) {
        define($name, $value);
    }
}

// Set timezone
date_default_timezone_set(PHP_TIMEZONE);
?>