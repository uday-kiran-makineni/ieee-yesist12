<?php
echo "<h1>🔍 Database Connection Test</h1>";

// Database configuration
$host = 'localhost';
$username = 'root';
$password = 'root';
$database = 'yesist12_auth';

echo "<h2>Testing MySQL Connection...</h2>";

// Test 1: Check if MySQL extension is loaded
echo "<h3>Step 1: PHP MySQL Extension</h3>";
if (extension_loaded('pdo_mysql')) {
    echo "<p>✅ PDO MySQL extension is loaded</p>";
} else {
    echo "<p>❌ PDO MySQL extension is NOT loaded</p>";
    echo "<p><strong>Solution:</strong> Install php-mysql extension</p>";
}

// Test 2: Try to connect to MySQL server (without database)
echo "<h3>Step 2: MySQL Server Connection</h3>";
try {
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p>✅ Successfully connected to MySQL server</p>";
    
    // Test 3: Check if database exists
    echo "<h3>Step 3: Database Existence Check</h3>";
    $stmt = $pdo->query("SHOW DATABASES LIKE '$database'");
    $dbExists = $stmt->fetch();
    
    if ($dbExists) {
        echo "<p>✅ Database '$database' exists</p>";
        
        // Test 4: Connect to specific database
        echo "<h3>Step 4: Database Connection</h3>";
        $pdo_db = new PDO("mysql:host=$host;dbname=$database", $username, $password);
        $pdo_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "<p>✅ Successfully connected to database '$database'</p>";
        
        // Test 5: Check tables
        echo "<h3>Step 5: Tables Check</h3>";
        $tables_stmt = $pdo_db->query("SHOW TABLES");
        $tables = $tables_stmt->fetchAll(PDO::FETCH_COLUMN);
        
        if (count($tables) > 0) {
            echo "<p>✅ Found " . count($tables) . " tables:</p>";
            echo "<ul>";
            foreach ($tables as $table) {
                echo "<li>$table</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>⚠️ No tables found in database. Run setup_database.php first.</p>";
        }
        
    } else {
        echo "<p>❌ Database '$database' does not exist</p>";
        echo "<p><strong>Solution:</strong> <a href='setup_database.php'>Run Database Setup</a></p>";
    }
    
} catch (PDOException $e) {
    echo "<p>❌ MySQL connection failed</p>";
    echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>Error Code:</strong> " . $e->getCode() . "</p>";
    
    echo "<h3>🔧 Possible Solutions:</h3>";
    echo "<ul>";
    echo "<li><strong>Check MySQL Service:</strong> Make sure MySQL is running</li>";
    echo "<li><strong>Check Credentials:</strong> Verify username/password are correct</li>";
    echo "<li><strong>Check Port:</strong> MySQL default port is 3306</li>";
    echo "<li><strong>Check Host:</strong> Try '127.0.0.1' instead of 'localhost'</li>";
    echo "</ul>";
}

// Test MySQL service status
echo "<h3>Step 6: MySQL Service Status</h3>";
try {
    $output = [];
    $return_var = 0;
    exec('sc query MySQL80 2>&1', $output, $return_var);
    
    if ($return_var === 0) {
        $status = implode("\n", $output);
        if (strpos($status, 'RUNNING') !== false) {
            echo "<p>✅ MySQL80 service is running</p>";
        } else {
            echo "<p>❌ MySQL80 service is not running</p>";
            echo "<p><strong>Solution:</strong> Start MySQL service</p>";
        }
    } else {
        echo "<p>⚠️ Could not check MySQL service status</p>";
        echo "<p>Try checking manually: <code>Get-Service -Name '*mysql*'</code></p>";
    }
} catch (Exception $e) {
    echo "<p>⚠️ Could not check service status: " . $e->getMessage() . "</p>";
}

echo "<h2>🎯 Quick Actions</h2>";
echo "<p><a href='setup_database.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🗄️ Setup Database</a></p>";
echo "<p><a href='index.html' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🔐 Go to Login</a></p>";
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

ul {
    margin: 10px 0;
    padding-left: 20px;
}

code {
    background: #f8f9fa;
    padding: 2px 6px;
    border-radius: 3px;
    font-family: monospace;
}

a {
    display: inline-block;
    margin: 5px 10px 5px 0;
}
</style>