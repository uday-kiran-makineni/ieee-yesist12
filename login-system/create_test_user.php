<?php
// create_test_user.php
// This script creates a test user in your existing yesist12_auth database

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "yesist12_auth";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>🧪 Creating Test User in Your Database</h2>";
    
    // Test credentials
    $test_email = "test@yesist12.com";
    $test_password = "password123";
    $test_name = "Test User";
    
    // Check if user already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$test_email]);
    
    if ($stmt->rowCount() > 0) {
        echo "<p style='color: orange;'>⚠️ Test user already exists!</p>";
        echo "<p><strong>Email:</strong> $test_email<br>";
        echo "<strong>Password:</strong> $test_password</p>";
    } else {
        // Create test user
        $hashed_password = password_hash($test_password, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, created_at) VALUES (?, ?, ?, NOW())");
        $result = $stmt->execute([$test_name, $test_email, $hashed_password]);
        
        if ($result) {
            echo "<p style='color: green;'>✅ Test user created successfully!</p>";
            echo "<p><strong>Email:</strong> $test_email<br>";
            echo "<strong>Password:</strong> $test_password</p>";
        } else {
            echo "<p style='color: red;'>❌ Failed to create test user</p>";
        }
    }
    
    // Show all users
    echo "<h3>👥 Current Users in Database:</h3>";
    $stmt = $pdo->query("SELECT id, name, email, created_at FROM users ORDER BY created_at DESC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($users) > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background: #f0f0f0;'><th>ID</th><th>Name</th><th>Email</th><th>Created At</th></tr>";
        
        foreach ($users as $user) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($user['id']) . "</td>";
            echo "<td>" . htmlspecialchars($user['name']) . "</td>";
            echo "<td>" . htmlspecialchars($user['email']) . "</td>";
            echo "<td>" . htmlspecialchars($user['created_at']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No users found in database.</p>";
    }
    
    echo "<br><hr><br>";
    echo "<h3>🔗 Next Steps:</h3>";
    echo "<p><a href='test_login.html' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🧪 Test Login</a> ";
    echo "<a href='index.html' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-left: 10px;'>🏠 Main Login Page</a></p>";
    
} catch(PDOException $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<p>Please make sure:</p>";
    echo "<ul>";
    echo "<li>MySQL server is running</li>";
    echo "<li>Database 'yesist12_auth' exists</li>";
    echo "<li>Database connection credentials are correct</li>";
    echo "</ul>";
}
?>