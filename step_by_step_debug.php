<?php
// STEP-BY-STEP DEBUG EXAMPLE
// Follow these instructions:

echo "<h1>Line-by-Line PHP Debugging Tutorial</h1>";

// BREAKPOINT 1: Set a breakpoint here
$step = 1;
echo "<p>Step $step: Starting the debug process</p>";

// BREAKPOINT 2: Set a breakpoint here  
$user_name = "Uday Kiran";
$step++;
echo "<p>Step $step: Created user name variable: $user_name</p>";

// BREAKPOINT 3: Set a breakpoint here
$numbers = [10, 20, 30, 40, 50];
$step++;
echo "<p>Step $step: Created array with " . count($numbers) . " numbers</p>";

// BREAKPOINT 4: Set a breakpoint here (inside the loop)
$total = 0;
$step++;
echo "<p>Step $step: Starting loop to calculate total</p>";

foreach ($numbers as $index => $number) {
    // BREAKPOINT 5: Set a breakpoint HERE - this will pause on each loop iteration
    $total += $number;
    echo "<p>Loop iteration " . ($index + 1) . ": Added $number, running total = $total</p>";
}

// BREAKPOINT 6: Set a breakpoint here
$step++;
echo "<p>Step $step: Loop completed. Final total: $total</p>";

// BREAKPOINT 7: Set a breakpoint here
$result = [
    'user' => $user_name,
    'numbers' => $numbers,
    'total' => $total,
    'average' => $total / count($numbers)
];
$step++;
echo "<p>Step $step: Created result array</p>";

// BREAKPOINT 8: Set a breakpoint here
echo "<h2>Final Results:</h2>";
echo "<pre>" . print_r($result, true) . "</pre>";

echo "<h3>🎯 Debugging Instructions:</h3>";
echo "<ol>";
echo "<li><strong>Open this file in VS Code</strong></li>";
echo "<li><strong>Click in the left margin</strong> next to the lines marked 'BREAKPOINT' to set red dots</li>";
echo "<li><strong>Press F5</strong> in VS Code</li>";
echo "<li><strong>Choose 'Listen for Xdebug'</strong></li>";
echo "<li><strong>Refresh this page</strong> - VS Code should pause at your first breakpoint</li>";
echo "<li><strong>Use F10</strong> to step to the next line</li>";
echo "<li><strong>Use F11</strong> to step into functions</li>";
echo "<li><strong>Hover over variables</strong> to see their values</li>";
echo "<li><strong>Check the Variables panel</strong> in VS Code</li>";
echo "</ol>";
?>