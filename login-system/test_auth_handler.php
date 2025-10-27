<?php
// Simple test to check auth_handler.php directly
header('Content-Type: text/html');

echo "<h1>Auth Handler Direct Test</h1>";

// Test 1: Check if the file can be included without errors
echo "<h2>Test 1: File Inclusion</h2>";
ob_start();
try {
    // Temporarily capture any output from including the auth handler
    $tempFile = tempnam(sys_get_temp_dir(), 'auth_test');
    file_put_contents($tempFile, '{"action":"test"}');
    
    $_SERVER['REQUEST_METHOD'] = 'POST';
    $_POST = [];
    
    // Can't directly test include due to headers, so let's test the endpoint via HTTP
    echo "<p>Testing auth_handler.php via HTTP request...</p>";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/auth_handler.php');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, '{"action":"test"}');
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen('{"action":"test"}')
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        echo "<p style='color: red;'>❌ cURL Error: $error</p>";
    } else {
        echo "<p>HTTP Code: $httpCode</p>";
        echo "<p>Response:</p>";
        echo "<pre>" . htmlspecialchars($response) . "</pre>";
        
        // Try to decode JSON
        $json = json_decode($response, true);
        if ($json !== null) {
            echo "<p style='color: green;'>✅ Valid JSON response received</p>";
        } else {
            echo "<p style='color: red;'>❌ Invalid JSON response - this is the problem!</p>";
            echo "<p>Raw response length: " . strlen($response) . " characters</p>";
            echo "<p>First 100 characters: " . htmlspecialchars(substr($response, 0, 100)) . "</p>";
        }
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<h2>Test 2: Check for PHP Errors</h2>";
echo "<p>Check the server console for any PHP errors or warnings.</p>";

echo "<h2>Test 3: Direct Signup Test</h2>";
echo "<p>Testing signup functionality...</p>";

$signupData = json_encode([
    'action' => 'signup',
    'email' => 'test_direct_' . time() . '@example.com',
    'password' => 'TestPassword123!',
    'fullName' => 'Direct Test User',
    'phone' => '+1234567890'
]);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/auth_handler.php');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $signupData);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Content-Length: ' . strlen($signupData)
));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$signupResponse = curl_exec($ch);
$signupHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$signupError = curl_error($ch);
curl_close($ch);

if ($signupError) {
    echo "<p style='color: red;'>❌ Signup cURL Error: $signupError</p>";
} else {
    echo "<p>Signup HTTP Code: $signupHttpCode</p>";
    echo "<p>Signup Response:</p>";
    echo "<pre>" . htmlspecialchars($signupResponse) . "</pre>";
    
    $signupJson = json_decode($signupResponse, true);
    if ($signupJson !== null) {
        echo "<p style='color: green;'>✅ Valid JSON response for signup</p>";
        if ($signupJson['success']) {
            echo "<p style='color: green;'>✅ Signup was successful!</p>";
        } else {
            echo "<p style='color: orange;'>⚠️ Signup failed: " . $signupJson['message'] . "</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Invalid JSON response for signup</p>";
    }
}
?>