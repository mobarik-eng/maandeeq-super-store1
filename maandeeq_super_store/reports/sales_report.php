<?php
require_once '../config/database.php';
require_once '../includes/session.php';
requireAdmin();

$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-d');

// Fetch Sales in range
$stmt = $pdo->prepare("
    SELECT s.*, c.name as customer_name 
    FROM sales s 
    LEFT JOIN customers c ON s.customer_id = c.id 
    WHERE DATE(s.created_at) BETWEEN ? AND ? 
    ORDER BY s.created_at DESC
");
$stmt->execute([$start_date, $end_date]);
$sales = $stmt->fetchAll();

// Calculate Totals
$total_sales = 0;
$total_income = 0;
foreach ($sales as $s) {
    $total_sales += $s['total_amount'];
    $total_income += $s['paid_amount'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Sales Report - Maandeeq Store</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

    <div class="wrapper">
        <?php include '../includes/sidebar.php'; ?>
        <div class="main-content">
            <?php include '../includes/header.php'; ?>

            <div class="card">
                <h3>Sales Report</h3>
                <form method="GET" style="display: flex; gap: 10px; margin-bottom: 20px; align-items: flex-end;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label>Start Date</label>
                        <input type="date" name="start_date" class="form-control" value="<?php echo $start_date; ?>">
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label>End Date</label>
                        <input type="date" name="end_date" class="form-control" value="<?php echo $end_date; ?>">
                    </div>
                    <button type="submit" class="btn">Filter</button>
                </form>

                <div style="display: flex; gap: 20px; margin-bottom: 20px;">
                    <div style="background: #e8f8f5; padding: 15px; border-radius: 4px; flex: 1;">
                        <h4>Total Revenue (Billed)</h4>
                        <span style="font-size: 1.5em; font-weight: bold;">$
                            <?php echo number_format($total_sales, 2); ?>
                        </span>
                    </div>
                    <div style="background: #eafaf1; padding: 15px; border-radius: 4px; flex: 1;">
                        <h4>Total Income (Collected)</h4>
                        <span style="font-size: 1.5em; font-weight: bold; color: green;">$
                            <?php echo number_format($total_income, 2); ?>
                        </span>
                    </div>
                </div>

                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Billed Amount</th>
                            <th>Paid Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($sales) > 0): ?>
                            <?php foreach ($sales as $sale): ?>
                                <tr>
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
                                    <td>
                                        <?php echo ucfirst($sale['payment_status']); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5">No sales found in this period.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php include '../includes/footer.php'; ?>