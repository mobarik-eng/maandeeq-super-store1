<?php
require_once '../config/database.php';
require_once '../includes/session.php';
requireAdmin();

// Fetch Products
$stmt = $pdo->query("SELECT * FROM products ORDER BY name ASC");
$products = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Products - Maandeeq Store</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

    <div class="wrapper">
        <?php include '../includes/sidebar.php'; ?>
        <div class="main-content">
            <?php include '../includes/header.php'; ?>

            <div class="card">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <h3>Product List</h3>
                    <a href="add.php" class="btn">Add New Product</a>
                </div>

                <table class="table mt-2">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($products) > 0): ?>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td>
                                        <?php echo $product['id']; ?>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($product['name']); ?>
                                    </td>
                                    <td>$
                                        <?php echo number_format($product['price'], 2); ?>
                                    </td>
                                    <td
                                        style="<?php echo $product['quantity'] < 10 ? 'color: red; font-weight: bold;' : ''; ?>">
                                        <?php echo $product['quantity']; ?>
                                    </td>
                                    <td>
                                        <a href="edit.php?id=<?php echo $product['id']; ?>" class="btn">Edit</a>
                                        <a href="delete.php?id=<?php echo $product['id']; ?>" class="btn btn-danger"
                                            onclick="return confirm('Ensure no sales are linked to this product before deleting. Continue?')">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5">No products found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php include '../includes/footer.php'; ?>