<?php
require_once '../config/database.php';
require_once '../includes/session.php';
requireAdmin();

// Handle Add Employee
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_employee'])) {
    $full_name = trim($_POST['full_name']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (!empty($full_name) && !empty($username) && !empty($password)) {
        try {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (full_name, username, password, role) VALUES (?, ?, ?, 'employee')");
            $stmt->execute([$full_name, $username, $hashed_password]);
            $success = "Employee added successfully.";
        } catch (PDOException $e) {
            $error = "Error adding employee: " . $e->getMessage();
        }
    } else {
        $error = "All fields are required.";
    }
}

// Handle Delete Employee
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    if ($id != currentUserId()) { // Prevent self-delete
        try {
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role = 'employee'");
            $stmt->execute([$id]);
            header("Location: employees.php");
            exit();
        } catch (PDOException $e) {
            $error = "Error deleting employee: " . $e->getMessage();
        }
    }
}

// Fetch Employees
$stmt = $pdo->query("SELECT * FROM users WHERE role = 'employee' ORDER BY created_at DESC");
$employees = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Employees - Maandeeq Store</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

    <div class="wrapper">
        <?php include '../includes/sidebar.php'; ?>
        <div class="main-content">
            <?php include '../includes/header.php'; ?>

            <div class="card">
                <h3>Add New Employee</h3>
                <?php if (isset($success))
                    echo "<div class='alert alert-success'>$success</div>"; ?>
                <?php if (isset($error))
                    echo "<div class='alert alert-danger'>$error</div>"; ?>

                <form action="" method="POST">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="full_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" name="add_employee" class="btn">Add Employee</button>
                </form>
            </div>

            <div class="card">
                <h3>Employee List</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Username</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($employees as $emp): ?>
                            <tr>
                                <td>
                                    <?php echo $emp['id']; ?>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($emp['full_name']); ?>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($emp['username']); ?>
                                </td>
                                <td>
                                    <?php echo date('Y-m-d', strtotime($emp['created_at'])); ?>
                                </td>
                                <td>
                                    <a href="?delete=<?php echo $emp['id']; ?>" class="btn btn-danger"
                                        onclick="return confirm('Are you sure?')">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php include '../includes/footer.php'; ?>