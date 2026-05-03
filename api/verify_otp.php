<?php
session_start();
require 'db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp_entered = $_POST['otp'] ?? '';
    $student_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare("SELECT id, expires_at FROM attendance_sessions WHERE otp_code = ? AND is_active = 1");
    $stmt->execute([$otp_entered]);
    $session = $stmt->fetch();

    if ($session) {
        if (new DateTime() > new DateTime($session['expires_at'])) {
            echo json_encode(['status' => 'error', 'message' => 'OTP has expired.']); exit;
        }
        try {
            $insert = $pdo->prepare("INSERT INTO attendance_records (session_id, student_id, status) VALUES (?, ?, 'present')");
            $insert->execute([$session['id'], $student_id]);
            echo json_encode(['status' => 'success', 'message' => 'Attendance recorded successfully!']);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => 'Attendance already recorded for this session.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid OTP.']);
    }
}
?>