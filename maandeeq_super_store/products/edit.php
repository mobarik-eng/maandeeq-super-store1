<?php
require_once '../config/database.php';
require_once '../includes/session.php';
requireAdmin();

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    die("Product not found.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];

    if (!empty($name) && is_numeric($price) && is_numeric($quantity)) {
        try {
            $stmt = $pdo->prepare("UPDATE products SET name = ?, description = ?, price = ?, quantity = ? WHERE id = ?");
            $stmt->execute([$name, $description, $price, $quantity, $id]);
            header("Location: index.php");
            exit();
        } catch (PDOException $e) {
            $error = "Error updating product: " . $e->getMessage();
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
    <title>Edit Product - Maandeeq Store</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

    <div class="wrapper">
        <?php include '../includes/sidebar.php'; ?>
        <div class="main-content">
            <?php include '../includes/header.php'; ?>

            <div class="card">
                <h3>Edit Product</h3>
                <?php if (isset($error))
                    echo "<div class='alert alert-danger'>$error</div>"; ?>

                <form action="" method="POST">
                    <div class="form-group">
                        <label>Product Name</label>
                        <input type="text" name="name" class="form-control"
                            value="<?php echo htmlspecialchars($product['name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description"
                            class="form-control"><?php echo htmlspecialchars($product['description']); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Price</label>
                        <input type="number" step="0.01" name="price" class="form-control"
                            value="<?php echo $product['price']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Quantity</label>
                        <input type="number" name="quantity" class="form-control"
                            value="<?php echo $product['quantity']; ?>" required>
                    </div>
                    <button type="submit" class="btn">Update Product</button>
                    <a href="index.php" class="btn btn-danger" style="text-decoration: none;">Cancel</a>
                </form>
            </div>

            <?php include '../includes/footer.php'; ?>