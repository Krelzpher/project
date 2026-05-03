<?php
session_start();
require 'db.php';
header('Content-Type: application/json');

// 1. Ensure the user is logged in and is a student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
    exit;
}

$student_id = $_SESSION['user_id'];
$code = trim($_POST['course_code'] ?? '');
$name = trim($_POST['course_name'] ?? '');
$section = trim($_POST['course_section'] ?? '');

// 2. Validate inputs
if (empty($code) || empty($name) || empty($section)) {
    echo json_encode(['status' => 'error', 'message' => 'Please fill out all course details.']);
    exit;
}

try {
    // 3. Find the course matching ALL THREE criteria exactly
    $stmt = $pdo->prepare("SELECT id FROM courses WHERE course_code = ? AND course_name = ? AND course_section = ?");
    $stmt->execute([$code, $name, $section]);
    $course = $stmt->fetch();

    if (!$course) {
        // Did not match a course in the database
        echo json_encode(['status' => 'error', 'message' => 'Course not found. Please check your details and try again.']);
        exit;
    }

    $course_id = $course['id'];

    // 4. Check if the student is already enrolled in this exact course
    $checkStmt = $pdo->prepare("SELECT id FROM enrollments WHERE student_id = ? AND course_id = ?");
    $checkStmt->execute([$student_id, $course_id]);
    
    if ($checkStmt->fetch()) {
        echo json_encode(['status' => 'info', 'message' => 'You are already enrolled in this course.']);
        exit;
    }

    // 5. Enroll the student
    $enrollStmt = $pdo->prepare("INSERT INTO enrollments (student_id, course_id) VALUES (?, ?)");
    $enrollStmt->execute([$student_id, $course_id]);

    echo json_encode(['status' => 'success', 'message' => 'Successfully enrolled in the course!']);

} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>