<?php
require_once '../config/database.php';
require_once '../includes/session.php';
requireLogin();

// Fetch Dashboard Stats (Only for Admin mostly, but Employees can see some)
$totalSales = 0;
$totalIncome = 0;
$totalDebts = 0;
$totalProducts = 0;

try {
    // Total Products
    $stmt = $pdo->query("SELECT COUNT(*) FROM products");
    $totalProducts = $stmt->fetchColumn();

    if ($_SESSION['role'] == 'admin') {
        // Sales Today
        $stmt = $pdo->query("SELECT COUNT(*) FROM sales WHERE DATE(created_at) = CURDATE()");
        $todaySalesCount = $stmt->fetchColumn();

        // Income Today
        $stmt = $pdo->query("SELECT SUM(paid_amount) FROM sales WHERE DATE(created_at) = CURDATE()");
        $todayIncome = $stmt->fetchColumn() ?: 0;

        // Total Debts (Pending or Partial)
        $stmt = $pdo->query("SELECT SUM(total_amount - paid_amount) FROM sales WHERE payment_status != 'paid'");
        $totalDebts = $stmt->fetchColumn() ?: 0;

        // Recent Sales
        $stmt = $pdo->query("
            SELECT s.*, u.username, c.name as customer_name 
            FROM sales s 
            JOIN users u ON s.user_id = u.id 
            LEFT JOIN customers c ON s.customer_id = c.id 
            ORDER BY s.created_at DESC LIMIT 5
        ");
        $recentSales = $stmt->fetchAll();
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Dashboard - Maandeeq Super Store</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            text-align: center;
        }

        .stat-card h3 {
            font-size: 2rem;
            margin: 10px 0;
            color: var(--secondary-color);
        }

        .stat-card p {
            color: #7f8c8d;
            margin: 0;
        }

        .debt-card h3 {
            color: var(--danger);
        }
    </style>
</head>

<body>

    <div class="wrapper">
        <?php include '../includes/sidebar.php'; ?>

        <div class="main-content">
            <?php include '../includes/header.php'; ?>

            <?php if ($_SESSION['role'] == 'admin'): ?>
                <div class="stats-grid">
                    <div class="stat-card">
                        <h3>
                            <?php echo $todaySalesCount; ?>
                        </h3>
                        <p>Sales Today</p>
                    </div>
                    <div class="stat-card">
                        <h3>$
                            <?php echo number_format($todayIncome, 2); ?>
                        </h3>
                        <p>Income Today</p>
                    </div>
                    <div class="stat-card debt-card">
                        <h3>$
                            <?php echo number_format($totalDebts, 2); ?>
                        </h3>
                        <p>Outstanding Debts</p>
                    </div>
                    <div class="stat-card">
                        <h3>
                            <?php echo $totalProducts; ?>
                        </h3>
                        <p>Total Products</p>
                    </div>
                </div>

                <div class="card">
                    <h3>Recent Transactions</h3>
                    <table class="table mt-2">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Customer</th>
                                <th>Total</th>
                                <th>Paid</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentSales as $sale): ?>
                                <tr>
                                    <td>#
                                        <?php echo $sale['id']; ?>
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
                                        <span
                                            class="<?php echo $sale['payment_status'] == 'paid' ? 'text-success' : 'text-danger'; ?>">
                                            <?php echo ucfirst($sale['payment_status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php echo date('M d, H:i', strtotime($sale['created_at'])); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="card">
                    <h3>Welcome,
                        <?php echo currentUserName(); ?>!
                    </h3>
                    <p>Use the sidebar to create new sales or view your history.</p>
                </div>
            <?php endif; ?>

            <?php include '../includes/footer.php'; ?>