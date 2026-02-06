<?php
require_once '../config/database.php';
require_once '../includes/session.php';
requireAdmin();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];

    if (!empty($name) && is_numeric($price) && is_numeric($quantity)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO products (name, description, price, quantity) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $description, $price, $quantity]);
            header("Location: index.php");
            exit();
        } catch (PDOException $e) {
            $error = "Error adding product: " . $e->getMessage();
        }
    } else {
        $error = "Please check your inputs.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Add Product - Maandeeq Store</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

    <div class="wrapper">
        <?php include '../includes/sidebar.php'; ?>
        <div class="main-content">
            <?php include '../includes/header.php'; ?>

            <div class="card">
                <h3>Add New Product</h3>
                <?php if (isset($error))
                    echo "<div class='alert alert-danger'>$error</div>"; ?>

                <form action="" method="POST">
                    <div class="form-group">
                        <label>Product Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Price</label>
                        <input type="number" step="0.01" name="price" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Quantity</label>
                        <input type="number" name="quantity" class="form-control" required>
                    </div>
                    <button type="submit" class="btn">Save Product</button>
                    <a href="index.php" class="btn btn-danger" style="text-decoration: none;">Cancel</a>
                </form>
            </div>

            <?php include '../includes/footer.php'; ?>