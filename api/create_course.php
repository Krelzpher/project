<?php
session_start();
require 'db.php';
header('Content-Type: application/json');

// Check if user is logged in and is a teacher
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$teacher_id = $_SESSION['user_id'];
$course_code = $_POST['course_code'] ?? '';
$course_name = $_POST['course_name'] ?? '';
$course_section = $_POST['course_section'] ?? '';

// Basic validation
if (empty($course_code) || empty($course_name) || empty($course_section)) {
    echo json_encode(['status' => 'error', 'message' => 'All fields (Code, Name, Section) are required.']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO courses (teacher_id, course_code, course_name, course_section) VALUES (?, ?, ?, ?)");
    $stmt->execute([$teacher_id, $course_code, $course_name, $course_section]);
    
    echo json_encode(['status' => 'success', 'message' => 'Course created successfully!']);
} catch (PDOException $e) {
    if ($e->getCode() == 23000) { // Error code for duplicate entry
        echo json_encode(['status' => 'error', 'message' => 'This Course Code already exists. Please use a unique code.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
}
?>