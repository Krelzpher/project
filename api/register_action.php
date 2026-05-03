<?php
require 'db.php';
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = strtolower($_POST['role'] ?? 'student');

    if (empty($name) || empty($email) || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required.']); exit;
    }

    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    $verification_token = bin2hex(random_bytes(32));

    try {
        $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password_hash, role, verification_token) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $email, $hashed_password, $role, $verification_token]);

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com'; 
            $mail->SMTPAuth   = true;
            $mail->Username   = 'rencabo@kld.edu.ph'; // EDIT THIS
            $mail->Password   = 'ghcebkoodejvgkhu'; // EDIT THIS (No spaces)
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('no-reply@kldattend.com', 'KLD Attend');
            $mail->addAddress($email, $name);
            $mail->isHTML(true);
            $mail->Subject = 'Verify Your KLD Attend Account';
            
            // Edit this path to match your actual local path!
            $verification_link = "http://localhost/kld-attend/api/verify_email.php?token=" . $verification_token;
            $mail->Body = "Hello $name,<br><br>Please click the link to verify your email:<br><a href='$verification_link'>$verification_link</a>";

            $mail->send();
            echo json_encode(['status' => 'success', 'message' => 'Registration successful! Check email to verify.']);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Registered, but email failed to send.']);
        }
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) echo json_encode(['status' => 'error', 'message' => 'Email already exists.']);
        else echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
}
?>