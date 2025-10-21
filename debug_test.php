<?php
/**
 * PHP Debugging Test File
 * This file demonstrates various debugging scenarios
 */

// Test 1: Simple variable debugging
function testBasicDebugging() {
    $name = "John Doe";
    $age = 30;
    $email = "john@example.com";
    
    // Set a breakpoint on the next line
    $userData = [
        'name' => $name,
        'age' => $age,
        'email' => $email,
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    return $userData;
}

// Test 2: Loop debugging
function testLoopDebugging() {
    $numbers = [1, 2, 3, 4, 5];
    $results = [];
    
    foreach ($numbers as $number) {
        // Set a breakpoint here to inspect each iteration
        $squared = $number * $number;
        $results[] = [
            'original' => $number,
            'squared' => $squared
        ];
    }
    
    return $results;
}

// Test 3: Function call debugging
function calculateTotal($items) {
    $total = 0;
    
    foreach ($items as $item) {
        // Set a breakpoint here
        $total += $item['price'] * $item['quantity'];
    }
    
    return $total;
}

function testFunctionDebugging() {
    $items = [
        ['name' => 'Laptop', 'price' => 999.99, 'quantity' => 1],
        ['name' => 'Mouse', 'price' => 29.99, 'quantity' => 2],
        ['name' => 'Keyboard', 'price' => 79.99, 'quantity' => 1]
    ];
    
    // Set a breakpoint before this function call
    $total = calculateTotal($items);
    
    return [
        'items' => $items,
        'total' => $total
    ];
}

// Test 4: Exception debugging
function testExceptionDebugging() {
    try {
        // Set a breakpoint here
        $result = riskyOperation();
        return $result;
    } catch (Exception $e) {
        // Set a breakpoint here to inspect the exception
        return [
            'error' => true,
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ];
    }
}

function riskyOperation() {
    $random = rand(1, 10);
    
    if ($random > 5) {
        // This will throw an exception sometimes
        throw new Exception("Random number $random is too high!");
    }
    
    return "Success! Random number was $random";
}

// Test 5: Database-like debugging (simulated)
function testDatabaseDebugging() {
    // Simulate database connection
    $connection = [
        'host' => 'localhost',
        'user' => 'root',
        'password' => 'root',
        'database' => 'formbuilder'
    ];
    
    // Simulate query building
    $query = "SELECT * FROM form_templates WHERE id = ?";
    $params = [1];
    
    // Set a breakpoint here to inspect query and params
    $result = simulateDbQuery($query, $params);
    
    return $result;
}

function simulateDbQuery($query, $params) {
    // Simulate database result
    $mockResult = [
        'id' => $params[0],
        'title' => 'Test Form',
        'structure' => '{"fields": [{"type": "text", "name": "username"}]}',
        'created_at' => date('Y-m-d H:i:s')
    ];
    
    // Set a breakpoint here to inspect the mock result
    return $mockResult;
}

// Main execution
if ($_SERVER['REQUEST_URI'] === '/debug_test.php' || basename(__FILE__) === 'debug_test.php') {
    echo "<h1>PHP Debugging Test</h1>";
    echo "<p>This file is designed for debugging. Set breakpoints and step through the code!</p>";
    
    // Test basic debugging
    echo "<h2>1. Basic Debugging Test</h2>";
    $basicResult = testBasicDebugging();
    echo "<pre>" . print_r($basicResult, true) . "</pre>";
    
    // Test loop debugging  
    echo "<h2>2. Loop Debugging Test</h2>";
    $loopResult = testLoopDebugging();
    echo "<pre>" . print_r($loopResult, true) . "</pre>";
    
    // Test function debugging
    echo "<h2>3. Function Debugging Test</h2>";
    $functionResult = testFunctionDebugging();
    echo "<pre>" . print_r($functionResult, true) . "</pre>";
    
    // Test exception debugging
    echo "<h2>4. Exception Debugging Test</h2>";
    $exceptionResult = testExceptionDebugging();
    echo "<pre>" . print_r($exceptionResult, true) . "</pre>";
    
    // Test database debugging
    echo "<h2>5. Database Debugging Test</h2>";
    $dbResult = testDatabaseDebugging();
    echo "<pre>" . print_r($dbResult, true) . "</pre>";
    
    echo "<h2>Debugging Instructions:</h2>";
    echo "<ol>";
    echo "<li>Set breakpoints by clicking in the left margin of VS Code</li>";
    echo "<li>Press F5 or use Debug > Start Debugging</li>";
    echo "<li>Choose 'Listen for Xdebug' configuration</li>";
    echo "<li>Refresh this page in your browser</li>";
    echo "<li>VS Code should break at your breakpoints</li>";
    echo "</ol>";
}
?>