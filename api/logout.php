<?php
session_start();

// Unset all session variables
$_SESSION = [];

// Destroy the session completely
session_destroy();

header('Content-Type: application/json');
echo json_encode(['status' => 'success', 'message' => 'Logged out successfully.']);
?>