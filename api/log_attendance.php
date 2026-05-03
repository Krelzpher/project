<?php
session_start();
require 'db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$course_id = $_POST['course_id'] ?? '';
$otp = $_POST['otp'] ?? ''; // Receive the OTP
$date = date('Y-m-d');

if (empty($course_id)) {
    echo json_encode(['status' => 'error', 'message' => 'Please select a course.']);
    exit;
}

// --- NEW: VERIFY THE OTP ---
$checkCourseStmt = $pdo->prepare("SELECT current_otp, otp_date FROM courses WHERE id = ?");
$checkCourseStmt->execute([$course_id]);
$course = $checkCourseStmt->fetch();

// Check if the OTP matches AND if it was generated today
if (!$course || $course['current_otp'] !== $otp || $course['otp_date'] !== $date) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid or expired OTP.']);
    exit;
}
// ---------------------------

try {
    $stmt = $pdo->prepare("INSERT INTO attendance (student_id, course_id, attendance_date, status) VALUES (?, ?, ?, 'Present')");
    $stmt->execute([$student_id, $course_id, $date]);

    echo json_encode(['status' => 'success', 'message' => 'Attendance logged successfully!']);
} catch (PDOException $e) {
    // Error Code 23000 means the UNIQUE constraint was violated (Already logged today)
    if ($e->getCode() == '23000') {
        echo json_encode(['status' => 'info', 'message' => 'You have already logged your attendance for this course today.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database error.']);
    }
}
?>