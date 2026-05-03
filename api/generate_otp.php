<?php
session_start();
require 'db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$course_id = $_POST['course_id'] ?? '';
if (empty($course_id)) {
    echo json_encode(['status' => 'error', 'message' => 'Please select a course.']);
    exit;
}

// Generate a random 6-character alphanumeric code
$otp = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));
$date = date('Y-m-d');

try {
    $stmt = $pdo->prepare("UPDATE courses SET current_otp = ?, otp_date = ? WHERE id = ? AND teacher_id = ?");
    $stmt->execute([$otp, $date, $course_id, $_SESSION['user_id']]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['status' => 'success', 'otp' => $otp]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Course not found or unauthorized.']);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error']);
}
?>