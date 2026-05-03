<?php
session_start();
require 'db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) { echo json_encode(['status' => 'error', 'message' => 'Unauthorized']); exit; }

$user_id = $_SESSION['user_id'];
$full_name = $_POST['full_name'] ?? '';
$id_number = $_POST['id_number'] ?? '';
$contact_number = $_POST['contact_number'] ?? '';
$new_password = $_POST['new_password'] ?? '';

// 1. Handle File Upload
$profile_pic_query = "";
$params = [$full_name, $id_number, $contact_number];

if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
    
    $fileName = time() . '_' . basename($_FILES['profile_pic']['name']);
    $targetFilePath = $uploadDir . $fileName;
    
    $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
    $allowTypes = array('jpg', 'png', 'jpeg', 'gif');
    
    if (in_array($fileType, $allowTypes)) {
        if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $targetFilePath)) {
            $profile_pic_query = ", profile_pic = ?";
            $params[] = $fileName;
        }
    }
}

// 2. Handle Password Change
$password_query = "";
if (!empty($new_password)) {
    $password_query = ", password_hash = ?";
    $params[] = password_hash($new_password, PASSWORD_BCRYPT);
}

$params[] = $user_id;

$sql = "UPDATE users SET full_name = ?, id_number = ?, contact_number = ? {$profile_pic_query} {$password_query} WHERE id = ?";
$stmt = $pdo->prepare($sql);

if ($stmt->execute($params)) {
    echo json_encode(['status' => 'success', 'message' => 'Profile updated successfully!']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to update profile.']);
}
?>