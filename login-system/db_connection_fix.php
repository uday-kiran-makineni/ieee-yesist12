<?php
echo "<h1>🔧 Database Connection Fix & Diagnostics</h1>";

// Include config file
require_once 'config.php';

echo "<h2>Step 1: PHP Environment Check</h2>";

// Check PHP version
echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";

// Check required extensions
$requiredExtensions = ['pdo', 'pdo_mysql', 'mysqli', 'openssl'];
$missingExtensions = [];

foreach ($requiredExtensions as $ext) {
    if (extension_loaded($ext)) {
        echo "<p>✅ Extension '$ext' is loaded</p>";
    } else {
        echo "<p>❌ Extension '$ext' is NOT loaded</p>";
        $missingExtensions[] = $ext;
    }
}

if (!empty($missingExtensions)) {
    echo "<div style='background: #fff3cd; padding: 15px; border-radius: 8px; margin: 20px 0;'>";
    echo "<h3>⚠️ Missing Extensions</h3>";
    echo "<p>The following extensions need to be enabled:</p>";
    echo "<ul>";
    foreach ($missingExtensions as $ext) {
        echo "<li>$ext</li>";
    }
    echo "</ul>";
    echo "<p><strong>To fix this:</strong></p>";
    echo "<ol>";
    echo "<li>Edit your php.ini file</li>";
    echo "<li>Add these lines to enable extensions:</li>";
    echo "<pre>";
    foreach ($missingExtensions as $ext) {
        echo "extension=$ext\n";
    }
    echo "</pre>";
    echo "<li>Restart your web server/PHP</li>";
    echo "</ol>";
    echo "</div>";
}

echo "<h2>Step 2: Database Connection Test</h2>";

try {
    // Test basic database connection
    $pdo = getDBConnection();
    echo "<p>✅ Successfully connected to database!</p>";
    
    // Test database and tables
    echo "<h3>Database Information:</h3>";
    $stmt = $pdo->query("SELECT DATABASE() as current_db");
    $dbInfo = $stmt->fetch();
    echo "<p><strong>Current Database:</strong> " . $dbInfo['current_db'] . "</p>";
    
    // Check tables
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (count($tables) > 0) {
        echo "<p><strong>Tables found:</strong> " . implode(', ', $tables) . "</p>";
        
        // Check users table specifically
        if (in_array('users', $tables)) {
            $stmt = $pdo->query("SELECT COUNT(*) as user_count FROM users");
            $userCount = $stmt->fetch()['user_count'];
            echo "<p><strong>Users in database:</strong> $userCount</p>";
        }
    } else {
        echo "<p>⚠️ No tables found. Database setup may be needed.</p>";
    }
    
    echo "<div style='background: #d4edda; padding: 15px; border-radius: 8px; margin: 20px 0;'>";
    echo "<h3>🎉 Database Connection Successful!</h3>";
    echo "<p>Your database connection is working properly.</p>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 8px; margin: 20px 0;'>";
    echo "<h3>❌ Database Connection Failed</h3>";
    echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
    
    echo "<h4>🔧 Troubleshooting Steps:</h4>";
    echo "<ol>";
    echo "<li><strong>Check MySQL Service:</strong> Ensure MySQL is running</li>";
    echo "<li><strong>Verify Credentials:</strong> Check username/password in config.php</li>";
    echo "<li><strong>Database Existence:</strong> Make sure 'yesist12_auth' database exists</li>";
    echo "<li><strong>PHP Extensions:</strong> Enable required MySQL extensions</li>";
    echo "<li><strong>Firewall:</strong> Check if port 3306 is accessible</li>";
    echo "</ol>";
    echo "</div>";
}

echo "<h2>Step 3: Auto-Fix Attempts</h2>";

// Try to fix common issues
echo "<h3>Checking MySQL Service Status:</h3>";
try {
    $output = [];
    $return_var = 0;
    exec('sc query MySQL80 2>&1', $output, $return_var);
    
    if ($return_var === 0) {
        $status = implode("\n", $output);
        if (strpos($status, 'RUNNING') !== false) {
            echo "<p>✅ MySQL80 service is running</p>";
        } else {
            echo "<p>❌ MySQL80 service is stopped</p>";
            echo "<p>Attempting to start MySQL service...</p>";
            exec('net start MySQL80 2>&1', $startOutput, $startResult);
            if ($startResult === 0) {
                echo "<p>✅ MySQL service started successfully</p>";
            } else {
                echo "<p>❌ Failed to start MySQL service. You may need to start it manually.</p>";
            }
        }
    }
} catch (Exception $e) {
    echo "<p>⚠️ Could not check MySQL service status</p>";
}

// Try to create database if it doesn't exist
echo "<h3>Database Creation Check:</h3>";
try {
    $pdo_server = new PDO("mysql:host=" . DB_HOST, DB_USER, DB_PASS);
    $pdo_server->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if database exists
    $stmt = $pdo_server->query("SHOW DATABASES LIKE '" . DB_NAME . "'");
    $dbExists = $stmt->fetch();
    
    if (!$dbExists) {
        echo "<p>⚠️ Database '" . DB_NAME . "' doesn't exist. Creating...</p>";
        $pdo_server->exec("CREATE DATABASE " . DB_NAME);
        echo "<p>✅ Database created successfully!</p>";
    } else {
        echo "<p>✅ Database '" . DB_NAME . "' exists</p>";
    }
    
} catch (PDOException $e) {
    echo "<p>❌ Could not create database: " . $e->getMessage() . "</p>";
}

echo "<h2>Step 4: Quick Actions</h2>";
echo "<div style='margin: 20px 0;'>";
echo "<a href='setup_database.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-right: 10px;'>🗄️ Setup Database</a>";
echo "<a href='test_connection.php' style='background: #17a2b8; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-right: 10px;'>🔍 Test Connection</a>";
echo "<a href='index.html' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🔐 Go to Login</a>";
echo "</div>";

echo "<h2>Step 5: Configuration Summary</h2>";
echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 8px;'>";
echo "<pre>";
echo "Database Configuration:\n";
echo "Host: " . DB_HOST . "\n";
echo "User: " . DB_USER . "\n";
echo "Database: " . DB_NAME . "\n";
echo "Debug Mode: " . (DEBUG_MODE ? 'Enabled' : 'Disabled') . "\n";
echo "</pre>";
echo "</div>";
?>

<style>
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    max-width: 1000px;
    margin: 0 auto;
    padding: 20px;
    background-color: #f5f5f5;
}

h1, h2, h3, h4 {
    color: #2c3e50;
}

pre {
    background: #f8f9fa;
    padding: 10px;
    border-radius: 5px;
    overflow-x: auto;
}

code {
    background: #f8f9fa;
    padding: 2px 6px;
    border-radius: 3px;
    font-family: monospace;
}

a {
    display: inline-block;
    margin: 5px;
}

ol, ul {
    padding-left: 20px;
}

li {
    margin: 5px 0;
}
</style>