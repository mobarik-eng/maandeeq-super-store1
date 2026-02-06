<?php
require_once '../config/database.php';

$password = 'admin123';
$hash = password_hash($password, PASSWORD_DEFAULT);
$username = 'admin';

try {
    // Check if admin exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);

    if ($stmt->rowCount() > 0) {
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE username = ?");
        $stmt->execute([$hash, $username]);
        echo "Password for user '$username' has been reset to '$password'.<br>";
        echo "Hash: $hash";
    } else {
        // Create if not exists
        $stmt = $pdo->prepare("INSERT INTO users (username, password, role, full_name) VALUES (?, ?, 'admin', 'System Admin')");
        $stmt->execute([$username, $hash]);
        echo "Admin user created with password '$password'.";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>