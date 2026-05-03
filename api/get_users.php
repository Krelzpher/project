<?php
session_start();
require 'db.php';
header('Content-Type: application/json');
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') { echo json_encode(['status' => 'error']); exit; }

$stmt = $pdo->query("SELECT id, full_name, email, role, is_verified FROM users ORDER BY created_at DESC");
echo json_encode(['status' => 'success', 'data' => $stmt->fetchAll()]);
?>