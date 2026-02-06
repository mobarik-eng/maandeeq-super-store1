<?php
require_once '../config/database.php';
require_once '../includes/session.php';
requireLogin();

if (!isset($_GET['id'])) {
    die("Invalid Invoice ID");
}

$sale_id = $_GET['id'];

// Get Sale Details
$stmt = $pdo->prepare("
    SELECT s.*, c.name as customer_name, c.phone as customer_phone, u.full_name as sold_by 
    FROM sales s 
    LEFT JOIN customers c ON s.customer_id = c.id 
    JOIN users u ON s.user_id = u.id 
    WHERE s.id = ?
");
$stmt->execute([$sale_id]);
$sale = $stmt->fetch();

if (!$sale) {
    die("Invoice not found.");
}

// Get Sale Items
$stmt = $pdo->prepare("
    SELECT si.*, p.name as product_name 
    FROM sale_items si 
    JOIN products p ON si.product_id = p.id 
    WHERE si.sale_id = ?
");
$stmt->execute([$sale_id]);
$items = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Invoice #
        <?php echo $sale['id']; ?> - Maandeeq Store
    </title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #555;
            max-width: 800px;
            margin: auto;
            padding: 20px;
        }

        .invoice-box {
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
        }

        .invoice-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .invoice-header h2 {
            margin: 0;
            color: #333;
        }

        .invoice-details {
            margin-bottom: 20px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .table th,
        .table td {
            padding: 10px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }

        .table th {
            background: #f9f9f9;
        }

        .text-right {
            text-align: right;
        }

        .total-section {
            text-align: right;
            font-size: 1.1em;
        }

        @media print {
            .no-print {
                display: none;
            }

            .invoice-box {
                border: none;
                box-shadow: none;
            }
        }
    </style>
</head>

<body>

    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer;">Print Invoice</button>
        <a href="create.php" style="margin-left: 10px; text-decoration: none;">Back to Sales</a>
    </div>

    <div class="invoice-box">
        <div class="invoice-header">
            <div>
                <h2>Maandeeq Super Store</h2>
                <p>123 Wholesale Market St.<br>City, Country<br>Phone: +123 456 7890</p>
            </div>
            <div style="text-align: right;">
                <h3>INVOICE</h3>
                <p><strong>Invoice #:</strong>
                    <?php echo $sale['id']; ?><br>
                    <strong>Date:</strong>
                    <?php echo date('Y-m-d H:i', strtotime($sale['created_at'])); ?><br>
                    <strong>Sold By:</strong>
                    <?php echo $sale['sold_by']; ?>
                </p>
            </div>
        </div>

        <div class="invoice-details">
            <strong>Bill To:</strong><br>
            <?php echo $sale['customer_name']; ?><br>
            <?php echo $sale['customer_phone']; ?>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th class="text-right">Price</th>
                    <th class="text-right">Qty</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td>
                            <?php echo $item['product_name']; ?>
                        </td>
                        <td class="text-right">$
                            <?php echo number_format($item['price'], 2); ?>
                        </td>
                        <td class="text-right">
                            <?php echo $item['quantity']; ?>
                        </td>
                        <td class="text-right">$
                            <?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="total-section">
            <p><strong>Total Amount:</strong> $
                <?php echo number_format($sale['total_amount'], 2); ?>
            </p>
            <p><strong>Paid Amount:</strong> $
                <?php echo number_format($sale['paid_amount'], 2); ?>
            </p>
            <p style="color: <?php echo $sale['total_amount'] - $sale['paid_amount'] > 0 ? 'red' : 'green'; ?>;">
                <strong>Balance Due:</strong> $
                <?php echo number_format($sale['total_amount'] - $sale['paid_amount'], 2); ?>
            </p>
        </div>

        <div style="margin-top: 40px; text-align: center; font-size: 0.9em; color: #777;">
            <p>Thank you for your business!</p>
        </div>
    </div>

</body>

</html>