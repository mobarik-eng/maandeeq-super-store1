<?php
require_once '../config/database.php';
require_once '../includes/session.php';
requireAdmin();

// Fetch Debts
$stmt = $pdo->prepare("
    SELECT s.*, c.name as customer_name, c.phone as customer_phone
    FROM sales s 
    LEFT JOIN customers c ON s.customer_id = c.id 
    WHERE s.payment_status != 'paid' 
    ORDER BY s.created_at DESC
");
$stmt->execute();
$debts = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Outstanding Debts - Maandeeq Store</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

    <div class="wrapper">
        <?php include '../includes/sidebar.php'; ?>
        <div class="main-content">
            <?php include '../includes/header.php'; ?>

            <div class="card">
                <h3>Outstanding Debts</h3>
                <?php if (count($debts) === 0): ?>
                    <div class="alert alert-success">No outstanding debts found!</div>
                <?php else: ?>
                    <table class="table mt-2">
                        <thead>
                            <tr>
                                <th>Invoice #</th>
                                <th>Date</th>
                                <th>Customer</th>
                                <th>Total</th>
                                <th>Paid</th>
                                <th>Remaining Due</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($debts as $debt): ?>
                                <?php $due = $debt['total_amount'] - $debt['paid_amount']; ?>
                                <tr>
                                    <td>#
                                        <?php echo $debt['id']; ?>
                                    </td>
                                    <td>
                                        <?php echo date('Y-m-d', strtotime($debt['created_at'])); ?>
                                    </td>
                                    <td>
                                        <?php echo $debt['customer_name']; ?><br>
                                        <small>
                                            <?php echo $debt['customer_phone']; ?>
                                        </small>
                                    </td>
                                    <td>$
                                        <?php echo number_format($debt['total_amount'], 2); ?>
                                    </td>
                                    <td>$
                                        <?php echo number_format($debt['paid_amount'], 2); ?>
                                    </td>
                                    <td style="color: red; font-weight: bold;">$
                                        <?php echo number_format($due, 2); ?>
                                    </td>
                                    <td>
                                        <a href="../sales/invoice.php?id=<?php echo $debt['id']; ?>" class="btn btn-sm">View</a>
                                        <a href="../sales/update_payment.php?id=<?php echo $debt['id']; ?>"
                                            class="btn btn-sm btn-success">Pay</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <?php include '../includes/footer.php'; ?>