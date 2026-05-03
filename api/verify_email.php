<?php
require 'db.php';
if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $stmt = $pdo->prepare("SELECT id FROM users WHERE verification_token = ? AND is_verified = 0");
    $stmt->execute([$token]);
    $user = $stmt->fetch();

    if ($user) {
        $update = $pdo->prepare("UPDATE users SET is_verified = 1, verification_token = NULL WHERE id = ?");
        $update->execute([$user['id']]);
        echo "<h3 style='font-family:sans-serif; text-align:center; margin-top:50px;'>Email successfully verified! You can now <a href='../login.html'>Login</a>.</h3>";
    } else {
        echo "<h3 style='font-family:sans-serif; text-align:center; margin-top:50px;'>Invalid or expired token.</h3>";
    }
} else {
    echo "<h3>No token provided.</h3>";
}
?>