<?php
// DEBUG VERSION OF THE FORM API
// This will show us exactly what's happening step by step

echo "<h1>🔍 Debugging Form API Step by Step</h1>";

// BREAKPOINT 1: Set breakpoint here
$step = 1;
echo "<p><strong>Step $step:</strong> Starting API debug process</p>";

// BREAKPOINT 2: Set breakpoint here
$step++;
echo "<p><strong>Step $step:</strong> Setting up headers</p>";
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
echo "<p>✅ Headers set successfully</p>";

// BREAKPOINT 3: Set breakpoint here
$step++;
echo "<p><strong>Step $step:</strong> Checking request method: " . $_SERVER['REQUEST_METHOD'] . "</p>";

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    echo "<p>ℹ️ This is a preflight request, exiting early</p>";
    exit(0);
}

// BREAKPOINT 4: Set breakpoint here - Database connection attempt
$step++;
echo "<p><strong>Step $step:</strong> Attempting database connection</p>";

$host = 'localhost';
$username = 'root';
$password = 'root';
$database = 'formbuilder';

echo "<p>Database settings:</p>";
echo "<ul>";
echo "<li>Host: $host</li>";
echo "<li>Username: $username</li>";
echo "<li>Password: " . str_repeat('*', strlen($password)) . "</li>";
echo "<li>Database: $database</li>";
echo "</ul>";

// BREAKPOINT 5: Set breakpoint here - This is where the error likely occurs
try {
    echo "<p>🔄 Attempting to connect to MySQL...</p>";
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p>✅ Database connection successful!</p>";
    
    // BREAKPOINT 6: Set breakpoint here - Test a simple query
    $step++;
    echo "<p><strong>Step $step:</strong> Testing database query</p>";
    
    try {
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "<p>✅ Database query successful!</p>";
        echo "<p>Tables found: " . count($tables) . "</p>";
        
        if (count($tables) > 0) {
            echo "<ul>";
            foreach ($tables as $table) {
                echo "<li>$table</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>⚠️ No tables found in database</p>";
        }
        
    } catch (PDOException $e) {
        echo "<p>❌ Database query failed: " . $e->getMessage() . "</p>";
    }
    
} catch(PDOException $e) {
    // BREAKPOINT 7: Set breakpoint here - This catches database errors
    echo "<p>❌ Database connection failed!</p>";
    echo "<p><strong>Error details:</strong></p>";
    echo "<ul>";
    echo "<li><strong>Error Code:</strong> " . $e->getCode() . "</li>";
    echo "<li><strong>Error Message:</strong> " . $e->getMessage() . "</li>";
    echo "<li><strong>File:</strong> " . $e->getFile() . "</li>";
    echo "<li><strong>Line:</strong> " . $e->getLine() . "</li>";
    echo "</ul>";
    
    echo "<h3>🔧 Possible Solutions:</h3>";
    echo "<ol>";
    echo "<li><strong>Start MySQL:</strong> Make sure MySQL server is running</li>";
    echo "<li><strong>Create Database:</strong> Create 'formbuilder' database</li>";
    echo "<li><strong>Check Credentials:</strong> Verify username/password are correct</li>";
    echo "<li><strong>Check MySQL Port:</strong> Default is 3306</li>";
    echo "</ol>";
}

// BREAKPOINT 8: Set breakpoint here
$step++;
echo "<p><strong>Step $step:</strong> Debug process completed</p>";

echo "<h3>🎯 How to Debug This:</h3>";
echo "<ol>";
echo "<li>Set breakpoints at each step mentioned above</li>";
echo "<li>Press F5 in VS Code and choose 'Listen for Xdebug'</li>";
echo "<li>Refresh this page</li>";
echo "<li>Step through each line with F10</li>";
echo "<li>Watch variables in the Variables panel</li>";
echo "<li>See exactly where the error occurs</li>";
echo "</ol>";
?>