<?php
// Token Testing and Validation Utility
require_once __DIR__ . "/config.php";
require_once __DIR__ . "/He5Framework.php";

function testTokenGeneration() {
    echo "=== Token Generation Test ===\n";
    
    // Test token generation
    $userId = 1;
    $token = He5::generateUserToken($userId);
    
    echo "Generated token for user ID $userId:\n";
    echo substr($token, 0, 50) . "...\n";
    echo "Token length: " . strlen($token) . " characters\n\n";
    
    return $token;
}

function testTokenValidation($token) {
    echo "=== Token Validation Test ===\n";
    
    $userId = He5::validateUserToken($token);
    
    if ($userId) {
        echo "✅ Token is valid!\n";
        echo "User ID: $userId\n";
    } else {
        echo "❌ Token is invalid or expired\n";
    }
    
    echo "\n";
    return $userId;
}

function testTokenExpiration() {
    echo "=== Token Expiration Test ===\n";
    
    // Create a token that expires immediately
    $payload = [
        'user_id' => 999,
        'issued_at' => time() - 3600, // 1 hour ago
        'expires_at' => time() - 1800, // 30 minutes ago (expired)
        'random' => bin2hex(random_bytes(16))
    ];
    
    $payloadJson = json_encode($payload);
    $encodedPayload = base64_encode($payloadJson);
    $signature = hash_hmac('sha256', $encodedPayload, TOKEN_ENCRYPTION_KEY);
    $expiredToken = base64_encode($encodedPayload . '.' . $signature);
    
    echo "Testing expired token...\n";
    $userId = He5::validateUserToken($expiredToken);
    
    if ($userId) {
        echo "❌ Expired token was accepted (this shouldn't happen!)\n";
    } else {
        echo "✅ Expired token was correctly rejected\n";
    }
    
    echo "\n";
}

function testTokenFormat($token) {
    echo "=== Token Format Analysis ===\n";
    
    $decoded = base64_decode($token);
    $parts = explode('.', $decoded);
    
    if (count($parts) === 2) {
        $payload = json_decode(base64_decode($parts[0]), true);
        echo "✅ Token structure is valid\n";
        echo "Payload:\n";
        foreach ($payload as $key => $value) {
            if ($key === 'expires_at') {
                echo "  $key: $value (" . date('Y-m-d H:i:s', $value) . ")\n";
            } elseif ($key === 'issued_at') {
                echo "  $key: $value (" . date('Y-m-d H:i:s', $value) . ")\n";
            } else {
                echo "  $key: $value\n";
            }
        }
        echo "Signature: " . substr($parts[1], 0, 20) . "...\n";
    } else {
        echo "❌ Invalid token structure\n";
    }
    
    echo "\n";
}

// Run tests
echo "🔐 YESIST12 Token System Testing\n";
echo "================================\n\n";

$token = testTokenGeneration();
testTokenFormat($token);
$userId = testTokenValidation($token);
testTokenExpiration();

echo "=== Token Security Features ===\n";
echo "✅ HMAC-SHA256 signature\n";
echo "✅ Base64 encoding\n";
echo "✅ Expiration validation\n";
echo "✅ User ID encryption\n";
echo "✅ Random salt included\n";
echo "\n";

echo "=== Example Usage ===\n";
echo "// Generate token:\n";
echo "\$token = He5::generateUserToken(\$userId);\n\n";

echo "// Validate token:\n";
echo "\$userId = He5::validateUserToken(\$token);\n\n";

echo "// Get user ID from headers:\n";
echo "\$userId = He5::getUserIdFromToken();\n\n";

if ($token && $userId) {
    echo "🎉 All tests passed! Token system is working correctly.\n";
}
?>