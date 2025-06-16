<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Test database connection
require_once 'database/DBController.php';
use database\DBController;

try {
    $db = new DBController();
    echo "Database connection successful!";
} catch (Exception $e) {
    echo "Database connection failed: " . $e->getMessage();
}

// Test if PHP is working
echo "<br>PHP is working!";
?> 