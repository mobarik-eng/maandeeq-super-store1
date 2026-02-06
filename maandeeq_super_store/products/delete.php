<?php
require_once '../config/database.php';
require_once '../includes/session.php';
requireAdmin();

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$id]);
    } catch (PDOException $e) {
        // In a real app we might want to show a friendly error if foreign key constraints fail
        $_SESSION['error'] = "Could not delete product. It might be linked to existing sales.";
    }
}
header("Location: index.php");
exit();
