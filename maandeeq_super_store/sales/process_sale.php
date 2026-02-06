<?php
require_once '../config/database.php';
require_once '../includes/session.php';
requireLogin();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customer_name = trim($_POST['customer_name']);
    $customer_phone = trim($_POST['customer_phone']);
    $paid_amount = floatval($_POST['paid_amount']);
    $total_amount = floatval($_POST['total_amount']);

    $product_ids = $_POST['products'] ?? [];
    $quantities = $_POST['quantities'] ?? [];

    if (empty($product_ids) || $total_amount <= 0) {
        $_SESSION['error'] = "Cart is empty.";
        header("Location: create.php");
        exit();
    }

    try {
        $pdo->beginTransaction();

        // 1. Create/Find Customer
        $customer_id = null;
        if (!empty($customer_name)) {
            // Simple check: if phone exists, assume existing customer, else insert new (or just always insert new for simple logs)
            // For simplicity in this scope: Always Insert new or find by exact name/phone match if complex, but here we just Insert and ignore dupe mostly 
            // or just simple insert:
            $stmt = $pdo->prepare("INSERT INTO customers (name, phone) VALUES (?, ?)");
            $stmt->execute([$customer_name, $customer_phone]);
            $customer_id = $pdo->lastInsertId();
        }

        // 2. Insert Sale
        $payment_status = ($paid_amount >= $total_amount) ? 'paid' : (($paid_amount > 0) ? 'partial' : 'pending');

        $stmt = $pdo->prepare("INSERT INTO sales (user_id, customer_id, total_amount, paid_amount, payment_status) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $customer_id, $total_amount, $paid_amount, $payment_status]);
        $sale_id = $pdo->lastInsertId();

        // 3. Insert Sale Items & Update Stock
        $stmt_item = $pdo->prepare("INSERT INTO sale_items (sale_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt_stock = $pdo->prepare("UPDATE products SET quantity = quantity - ? WHERE id = ?");
        $stmt_price = $pdo->prepare("SELECT price FROM products WHERE id = ?");

        foreach ($product_ids as $index => $p_id) {
            $qty = $quantities[$index];

            // Get current price (secure side)
            $stmt_price->execute([$p_id]);
            $price = $stmt_price->fetchColumn();

            $stmt_item->execute([$sale_id, $p_id, $qty, $price]);
            $stmt_stock->execute([$qty, $p_id]);
        }

        $pdo->commit();
        header("Location: invoice.php?id=" . $sale_id);
        exit();

    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Transaction failed: " . $e->getMessage();
        header("Location: create.php");
        exit();
    }
} else {
    header("Location: create.php");
    exit();
}
