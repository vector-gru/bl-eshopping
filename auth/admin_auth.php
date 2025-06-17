<?php
// Remove session_start since it's already called in index.php
// session_start();

function isAdmin() {
    if (!isset($_SESSION['user_id'])) {
        error_log("isAdmin: No user session");
        return false;
    }

    try {
        require_once __DIR__ . '/../database/db_connect.php';
        $conn = getDBConnection();
        
        $stmt = $conn->prepare("SELECT is_admin FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        error_log("isAdmin check for user {$_SESSION['user_id']}: " . ($result ? json_encode($result) : 'no result'));
        return $result && $result['is_admin'] == 1;
    } catch (PDOException $e) {
        error_log("isAdmin database error: " . $e->getMessage());
        throw $e;
    }
}

function requireAdmin() {
    try {
        if (!isAdmin()) {
            header('Location: /projects/bl-eshopping/login.php?error=admin_required');
            exit();
        }
    } catch (Exception $e) {
        error_log("requireAdmin error: " . $e->getMessage());
        die("Error checking admin status. Please check the error logs.");
    }
}
?> 