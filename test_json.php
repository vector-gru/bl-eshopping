<?php
// Simple test to check JSON response
header('Content-Type: application/json');
echo json_encode(['test' => 'success', 'message' => 'JSON is working']);
exit();
?> 