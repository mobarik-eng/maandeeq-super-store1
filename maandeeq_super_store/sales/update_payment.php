<?php
require_once '../config/database.php';
require_once '../includes/session.php';
requireAdmin();

if (!isset($_GET['id'])) {
    header("Location: ../reports/debts.php");
    exit();
}

$sale_id = $_GET['id'];

// Fetch Sale
$stmt = $pdo->prepare("
    SELECT s.*, c.name as customer_name 
    FROM sales s 
    LEFT JOIN customers c ON s.customer_id = c.id 
    WHERE s.id = ?
");
$stmt->execute([$sale_id]);
$sale = $stmt->fetch();

if (!$sale) {
    die("Sale not found.");
}

$due_amount = $sale['total_amount'] - $sale['paid_amount'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $payment_amount = floatval($_POST['payment_amount']);

    if ($payment_amount > 0) {
        $new_paid_amount = $sale['paid_amount'] + $payment_amount;

        // Prevent overpayment (optional, but good practice to cap or warn)
        // For now, we allow it (change could be given), but let's just cap status at 'paid'

        $new_status = ($new_paid_amount >= $sale['total_amount']) ? 'paid' : 'partial';

        try {
            $update = $pdo->prepare("UPDATE sales SET paid_amount = ?, payment_status = ? WHERE id = ?");
            $update->execute([$new_paid_amount, $new_status, $sale_id]);

            $_SESSION['success'] = "Payment updated successfully.";
            header("Location: ../reports/debts.php");
            exit();
        } catch (PDOException $e) {
            $error = "Error updating payment: " . $e->getMessage();
        }
    } else {
        $error = "Please enter a valid amount.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Update Payment - Maandeeq Store</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

    <div class="wrapper">
        <?php include '../includes/sidebar.php'; ?>
        <div class="main-content">
            <?php include '../includes/header.php'; ?>

            <div class="card" style="max-width: 600px; margin: auto;">
                <h3>Update Payment for Invoice #
                    <?php echo $sale['id']; ?>
                </h3>
                <p><strong>Customer:</strong>
                    <?php echo $sale['customer_name']; ?>
                </p>
                <p><strong>Total Amount:</strong> $
                    <?php echo number_format($sale['total_amount'], 2); ?>
                </p>
                <p><strong>Already Paid:</strong> $
                    <?php echo number_format($sale['paid_amount'], 2); ?>
                </p>
                <h4 style="color: red;">Remaining Due: $
                    <?php echo number_format($due_amount, 2); ?>
                </h4>

                <?php if (isset($error))
                    echo "<div class='alert alert-danger'>$error</div>"; ?>

                <form action="" method="POST">
                    <div class="form-group">
                        <label>New Payment Amount</label>
                        <input type="number" step="0.01" name="payment_amount" class="form-control"
                            max="<?php echo $due_amount; ?>" required placeholder="Enter amount being paid now">
                    </div>

                    <button type="submit" class="btn btn-success">Update Payment</button>
                    <a href="../reports/debts.php" class="btn btn-danger">Cancel</a>
                </form>
            </div>

            <?php include '../includes/footer.php'; ?>