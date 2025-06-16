<?php
require ('database/DBController.php');

use database\DBController;

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

$db = new DBController();

if ($db->con->connect_error) {
    die("Connection failed: " . $db->con->connect_error);
}

echo "Connected successfully\n\n";

// Check tables
$tables = ['users', 'product', 'cart', 'wishlist', 'orders', 'order_items'];

foreach ($tables as $table) {
    echo "Checking table: $table\n";
    
    // Check if table exists
    $result = $db->con->query("SHOW TABLES LIKE '$table'");
    if ($result->num_rows == 0) {
        echo "Table $table does not exist!\n";
        continue;
    }
    
    // Get table structure
    $result = $db->con->query("DESCRIBE $table");
    echo "Structure:\n";
    while ($row = $result->fetch_assoc()) {
        echo "  {$row['Field']} - {$row['Type']} - {$row['Null']} - {$row['Key']} - {$row['Default']}\n";
    }
    
    // Get row count
    $result = $db->con->query("SELECT COUNT(*) as count FROM $table");
    $row = $result->fetch_assoc();
    echo "Row count: {$row['count']}\n\n";
}

// Check foreign keys
echo "Checking foreign keys:\n";
$result = $db->con->query("
    SELECT 
        TABLE_NAME,
        COLUMN_NAME,
        CONSTRAINT_NAME,
        REFERENCED_TABLE_NAME,
        REFERENCED_COLUMN_NAME
    FROM
        INFORMATION_SCHEMA.KEY_COLUMN_USAGE
    WHERE
        REFERENCED_TABLE_SCHEMA = 'eshop'
        AND REFERENCED_TABLE_NAME IS NOT NULL
");

while ($row = $result->fetch_assoc()) {
    echo "  {$row['TABLE_NAME']}.{$row['COLUMN_NAME']} -> {$row['REFERENCED_TABLE_NAME']}.{$row['REFERENCED_COLUMN_NAME']}\n";
}

// Check session status
echo "\nSession status:\n";
echo "Session started: " . (session_status() === PHP_SESSION_ACTIVE ? "Yes" : "No") . "\n";
if (isset($_SESSION)) {
    echo "Session variables:\n";
    print_r($_SESSION);
}
?> 