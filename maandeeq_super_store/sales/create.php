<?php
require_once '../config/database.php';
require_once '../includes/session.php';
requireLogin();

// Fetch Products for Select
$stmt = $pdo->query("SELECT * FROM products WHERE quantity > 0 ORDER BY name ASC");
$products = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>New Sale - Maandeeq Store</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

    <div class="wrapper">
        <?php include '../includes/sidebar.php'; ?>
        <div class="main-content">
            <?php include '../includes/header.php'; ?>

            <div class="card">
                <h3>New Transaction</h3>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger">
                        <?php echo $_SESSION['error'];
                        unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>

                <div style="display: flex; gap: 20px;">
                    <!-- Product Selection Area -->
                    <div style="flex: 1;">
                        <h4>Add Item</h4>
                        <div class="form-group">
                            <label>Product</label>
                            <select id="product-select" class="form-control">
                                <option value="">-- Select Product --</option>
                                <?php foreach ($products as $p): ?>
                                    <option value="<?php echo $p['id']; ?>" data-price="<?php echo $p['price']; ?>"
                                        data-stock="<?php echo $p['quantity']; ?>">
                                        <?php echo htmlspecialchars($p['name']); ?> ($
                                        <?php echo $p['price']; ?>) - Stock:
                                        <?php echo $p['quantity']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Quantity</label>
                            <input type="number" id="quantity-input" class="form-control" value="1" min="1">
                        </div>
                        <button type="button" id="add-item-btn" class="btn btn-block">Add to Cart</button>
                    </div>

                    <!-- Cart Area -->
                    <div style="flex: 2; border-left: 1px solid #eee; padding-left: 20px;">
                        <form action="process_sale.php" method="POST">
                            <h4>Cart</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Price</th>
                                        <th>Qty</th>
                                        <th>Total</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="cart-table-body">
                                    <!-- Cart Items go here -->
                                </tbody>
                            </table>

                            <hr>
                            <div style="text-align: right; font-size: 1.2rem; font-weight: bold; margin-bottom: 20px;">
                                Total: <span id="total-amount-display">$0.00</span>
                                <input type="hidden" name="total_amount" id="total-amount-input" value="0">
                            </div>

                            <h4>Customer Details</h4>
                            <div class="form-group">
                                <label>Customer Name</label>
                                <input type="text" name="customer_name" class="form-control" required
                                    placeholder="Enter customer name">
                            </div>
                            <div class="form-group">
                                <label>Phone Number (Optional)</label>
                                <input type="text" name="customer_phone" class="form-control"
                                    placeholder="123-456-7890">
                            </div>

                            <div class="form-group">
                                <label>Paid Amount</label>
                                <input type="number" step="0.01" name="paid_amount" class="form-control" required
                                    placeholder="Amount received">
                            </div>

                            <button type="submit" class="btn btn-success btn-block"
                                onclick="return confirm('Process this sale?')">Complete Sale</button>
                        </form>
                    </div>
                </div>
            </div>

            <?php include '../includes/footer.php'; ?>