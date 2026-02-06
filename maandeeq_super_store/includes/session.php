<?php
session_start();

// Check if user is logged in
function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

// Redirect if not logged in
function requireLogin()
{
    if (!isLoggedIn() || !isset($_SESSION['role'])) {
        // Force logout if session is partial/corrupt
        session_unset();
        session_destroy();
        header("Location: /maandeeq_super_store/auth/login.php");
        exit();
    }
}

// Redirect if not admin
function requireAdmin()
{
    requireLogin();
    if ($_SESSION['role'] !== 'admin') {
        die("Access Denied: You do not have permission to view this page.");
    }
}

// Get current user ID
function currentUserId()
{
    return $_SESSION['user_id'] ?? null;
}

// Get current user name
function currentUserName()
{
    return $_SESSION['full_name'] ?? 'Guest';
}
?>