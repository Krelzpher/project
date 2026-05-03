<?php
session_start();
require 'db.php';
header('Content-Type: application/json');

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    echo json_encode(['status' => 'error', 'message' => 'Please enter both email and password.']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // Check if user exists AND if the hashed password matches
    if ($user && password_verify($password, $user['password_hash'])) {
        
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['full_name'] = $user['full_name'];

        $redirect = 'student-dash.php';
        if ($user['role'] === 'teacher') $redirect = 'teacher-dash.php';
        if ($user['role'] === 'admin') $redirect = 'admin-dash.php';

        echo json_encode([
            'status' => 'success', 
            'message' => 'Login successful! Redirecting...', 
            'redirect' => $redirect
        ]);
    } else {
        // EXACT WORDING YOU REQUESTED
        echo json_encode(['status' => 'error', 'message' => 'Incorrect Email or Password.']);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>