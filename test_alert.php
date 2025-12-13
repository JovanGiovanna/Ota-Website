<?php

require 'vendor/autoload.php';
require 'bootstrap/app.php';

// Test alert() function
if (function_exists('alert')) {
    $alert = alert();
    echo "✓ alert() function exists\n";
    echo "✓ Alert class: " . get_class($alert) . "\n";
    echo "✓ Has success method: " . (method_exists($alert, 'success') ? "Yes" : "No") . "\n";
    echo "✓ Has error method: " . (method_exists($alert, 'error') ? "Yes" : "No") . "\n";
} else {
    echo "✗ alert() function does not exist\n";