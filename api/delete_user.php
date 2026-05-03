<?php
session_start();
require 'db.php';
header('Content-Type: application/json');
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') { echo json_encode(['status' => 'error']); exit; }

$id = $_POST['user_id'] ?? '';
if ($id == $_SESSION['user_id']) { echo json_encode(['status' => 'error', 'message' => 'Cannot delete yourself.']); exit; }

$stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
if ($stmt->execute([$id])) echo json_encode(['status' => 'success', 'message' => 'User deleted.']);
else echo json_encode(['status' => 'error', 'message' => 'Failed to delete.']);
?>