<?php
echo "<h1>🧪 Test Authentication Endpoint</h1>";

echo "<h2>Testing Sign In with Test Credentials</h2>";

// Simulate a sign-in request
$test_data = [
    'action' => 'signin',
    'email' => 'test@yesist12.com',
    'password' => 'password123'
];

// Make internal request to auth_handler
$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => 'Content-Type: application/json',
        'content' => json_encode($test_data)
    ]
]);

echo "<p><strong>Sending request to auth_handler.php...</strong></p>";
echo "<p>Data: <code>" . json_encode($test_data) . "</code></p>";

try {
    $response = file_get_contents('http://localhost:8001/auth_handler.php', false, $context);
    
    if ($response === false) {
        echo "<p>❌ Failed to get response from auth_handler.php</p>";
    } else {
        echo "<p>✅ Got response from auth_handler.php</p>";
        echo "<p><strong>Response:</strong></p>";
        echo "<pre style='background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto;'>";
        
        $decoded = json_decode($response, true);
        if ($decoded) {
            echo json_encode($decoded, JSON_PRETTY_PRINT);
        } else {
            echo htmlspecialchars($response);
        }
        echo "</pre>";
    }
} catch (Exception $e) {
    echo "<p>❌ Error testing auth endpoint: " . $e->getMessage() . "</p>";
}

echo "<h2>🔗 Quick Links</h2>";
echo "<p><a href='debug_setup.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🗄️ Run Database Setup</a></p>";
echo "<p><a href='test_connection.php' style='background: #17a2b8; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🔍 Test Connection</a></p>";
echo "<p><a href='index.html' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🔐 Login Page</a></p>";

echo "<h2>📝 Manual Test Steps</h2>";
echo "<ol>";
echo "<li>Go to <a href='index.html'>Login Page</a></li>";
echo "<li>Use these credentials:</li>";
echo "<ul>";
echo "<li><strong>Email:</strong> test@yesist12.com</li>";
echo "<li><strong>Password:</strong> password123</li>";
echo "</ul>";
echo "<li>Click 'Sign In'</li>";
echo "<li>If successful, you should be redirected to dashboard.php</li>";
echo "</ol>";
?>

<style>
body {
    font-family: Arial, sans-serif;
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
    background-color: #f5f5f5;
}

h1, h2 {
    color: #2c3e50;
}

p {
    margin: 10px 0;
}

ol, ul {
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