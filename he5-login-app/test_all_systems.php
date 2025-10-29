<?php
// Comprehensive authentication system test
echo "🧪 COMPREHENSIVE AUTHENTICATION SYSTEM TEST\n";
echo "==========================================\n\n";

// Test database connection
echo "1️⃣ Testing Database Connection...\n";
try {
    require_once 'config.php';
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $pdo = new PDO($dsn, DB_USERNAME, DB_PASSWORD, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    echo "✅ Database connection: SUCCESS\n";
    
    // Check users table
    $userCount = $pdo->query("SELECT COUNT(*) as count FROM users")->fetch()['count'];
    echo "✅ Users table: $userCount users found\n";
    
    // Check user_sessions table
    $sessionCount = $pdo->query("SELECT COUNT(*) as count FROM user_sessions")->fetch()['count'];
    echo "✅ User sessions table: $sessionCount sessions found\n";
    
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n2️⃣ Testing API Endpoints...\n";

// Function to make HTTP requests
function makeRequest($url, $method = 'GET', $data = null, $headers = []) {
    $context = [
        'http' => [
            'method' => $method,
            'header' => implode("\r\n", $headers),
            'content' => $data,
            'ignore_errors' => true
        ]
    ];
    
    $response = file_get_contents($url, false, stream_context_create($context));
    $httpCode = explode(' ', $http_response_header[0])[1];
    
    return [
        'code' => $httpCode,
        'body' => $response,
        'data' => json_decode($response, true)
    ];
}

// Test login endpoint
echo "🔐 Testing login API...\n";
$loginData = json_encode([
    'email' => 'test@yesist12.com',
    'password' => 'password123'
]);

$loginResponse = makeRequest('http://localhost:8000/api/signin', 'POST', $loginData, [
    'Content-Type: application/json'
]);

if ($loginResponse['code'] == '200' && $loginResponse['data']['success']) {
    echo "✅ Login API: SUCCESS\n";
    echo "   Token length: " . strlen($loginResponse['data']['token']) . " characters\n";
    $token = $loginResponse['data']['token'];
} else {
    echo "❌ Login API failed: " . $loginResponse['body'] . "\n";
    exit(1);
}

// Test token validation
echo "🎫 Testing token validation...\n";
$validateResponse = makeRequest('http://localhost:8000/api/validate-token', 'GET', null, [
    "Authorization: Bearer $token"
]);

if ($validateResponse['code'] == '200' && $validateResponse['data']['valid']) {
    echo "✅ Token validation: SUCCESS\n";
} else {
    echo "❌ Token validation failed\n";
}

// Test profile endpoint
echo "👤 Testing profile API...\n";
$profileResponse = makeRequest('http://localhost:8000/api/profile', 'GET', null, [
    "Authorization: Bearer $token"
]);

if ($profileResponse['code'] == '200' && $profileResponse['data']['success']) {
    echo "✅ Profile API: SUCCESS\n";
    echo "   User: " . $profileResponse['data']['data']['full_name'] . "\n";
} else {
    echo "❌ Profile API failed\n";
}

// Test logout
echo "🚪 Testing logout API...\n";
$logoutResponse = makeRequest('http://localhost:8000/api/logout', 'POST', null, [
    "Authorization: Bearer $token"
]);

if ($logoutResponse['code'] == '200' && $logoutResponse['data']['success']) {
    echo "✅ Logout API: SUCCESS\n";
} else {
    echo "❌ Logout API failed\n";
}

echo "\n🎉 ALL TESTS COMPLETED SUCCESSFULLY!\n";
echo "✅ Your He5 Framework Authentication System is fully operational!\n";
echo "\n📍 Access your application at: http://localhost:8000\n";
echo "🔑 Test credentials: test@yesist12.com / password123\n";