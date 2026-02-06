<?php
require_once '../config/database.php';
require_once '../includes/session.php';
requireLogin();

// Simple pagination or limit could be added here
$stmt = $pdo->prepare("
    SELECT s.*, c.name as customer_name 
    FROM sales s 
    LEFT JOIN customers c ON s.customer_id = c.id 
    ORDER BY s.created_at DESC
");
$stmt->execute();
$sales = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Sales History - Maandeeq Store</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

    <div class="wrapper">
        <?php include '../includes/sidebar.php'; ?>
        <div class="main-content">
            <?php include '../includes/header.php'; ?>

            <div class="card">
                <h3>Sales History</h3>
                <table class="table mt-2">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Total</th>
                            <th>Paid</th>
                            <th>Due</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sales as $sale): ?>
                            <?php $due = $sale['total_amount'] - $sale['paid_amount']; ?>
                            <tr>
                                <td>#
                                    <?php echo $sale['id']; ?>
                                </td>
                                <td>
                                    <?php echo date('Y-m-d H:i', strtotime($sale['created_at'])); ?>
                                </td>
                                <td>
                                    <?php echo $sale['customer_name'] ?? 'Walk-in'; ?>
                                </td>
                                <td>$
                                    <?php echo number_format($sale['total_amount'], 2); ?>
                                </td>
                                <td>$
                                    <?php echo number_format($sale['paid_amount'], 2); ?>
                                </td>
                                <td style="<?php echo $due > 0 ? 'color: red;' : ''; ?>">$
                                    <?php echo number_format($due, 2); ?>
                                </td>
                                <td>
                                    <?php echo ucfirst($sale['payment_status']); ?>
                                </td>
                                <td>
                                    <a href="invoice.php?id=<?php echo $sale['id']; ?>" class="btn btn-sm"
                                        target="_blank">View Invoice</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php include '../includes/footer.php'; ?>