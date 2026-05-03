<?php
session_start();
require 'db.php';
header('Content-Type: application/json');
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') { echo json_encode(['status' => 'error']); exit; }

$id = $_POST['user_id'] ?? ''; $name = $_POST['full_name'] ?? ''; $role = strtolower($_POST['role'] ?? '');
$stmt = $pdo->prepare("UPDATE users SET full_name = ?, role = ? WHERE id = ?");
if ($stmt->execute([$name, $role, $id])) echo json_encode(['status' => 'success', 'message' => 'User updated.']);
else echo json_encode(['status' => 'error', 'message' => 'Failed to update.']);
?>