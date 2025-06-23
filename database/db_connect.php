<?php
// Database configuration as constants
define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');
define('DB_USER', 'u240131984_root');
define('DB_PASS', 'Dev@hintekk1');
define('DB_NAME', 'u240131984_eshop');

function getDBConnection() {
    try {
        // Connect using TCP/IP with explicit port
        $dsn = sprintf(
            "mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4",
            DB_HOST,
            DB_PORT,
            DB_NAME
        );
        error_log("Attempting database connection with DSN: " . $dsn);
        
        $conn = new PDO($dsn, DB_USER, DB_PASS);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Verify database connection
        $result = $conn->query("SELECT DATABASE()")->fetchColumn();
        error_log("Connected to database: " . $result);
        
        return $conn;
    } catch(PDOException $e) {
        error_log("Database connection error: " . $e->getMessage());
        throw new PDOException("Database connection failed: " . $e->getMessage());
    }
}

// For backward compatibility
try {
    $conn = getDBConnection();
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?> 