<?php
session_start();
require 'db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$student_id = $_SESSION['user_id'];

try {
    // Fetches enrolled courses AND today's attendance status in one query
    $stmt = $pdo->prepare("
        SELECT 
            c.id, c.course_code, c.course_name, c.course_section, 
            a.time_logged, a.status 
        FROM enrollments e
        JOIN courses c ON e.course_id = c.id
        LEFT JOIN attendance a ON c.id = a.course_id 
            AND a.student_id = e.student_id 
            AND a.attendance_date = CURDATE()
        WHERE e.student_id = ?
    ");
    $stmt->execute([$student_id]);
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['status' => 'success', 'courses' => $courses]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error.']);
}
?>