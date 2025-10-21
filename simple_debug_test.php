<?php
// Simple test file for debugging
echo "PHP Debugging Test Started\n";

$name = "Uday Kiran";
$message = "Welcome to PHP debugging!";

// Set a breakpoint on the next line by clicking in the left margin
$greeting = "Hello, " . $name . "! " . $message;

echo $greeting . "\n";

// Another breakpoint opportunity
$numbers = [1, 2, 3, 4, 5];
$sum = 0;

foreach ($numbers as $number) {
    // Set a breakpoint here to watch the loop
    $sum += $number;
    echo "Added $number, sum is now: $sum\n";
}

echo "Final sum: $sum\n";
echo "Debugging test completed!\n";
?>