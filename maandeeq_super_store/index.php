<?php
require_once 'includes/session.php';

// Redirect to login if not logged in
if (!isLoggedIn()) {
    header("Location: auth/login.php");
    exit();
}

// Redirect to dashboard
header("Location: admin/dashboard.php");
exit();
