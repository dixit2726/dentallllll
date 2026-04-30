<?php
session_start();

/**
 * Check if user is logged in
 */
function checkLogin() {
    if (!isset($_SESSION['admin_id'])) {
        header("Location: clinic-login-2026.php");
        exit();
    }
}

/**
 * Redirect if already logged in
 */
function redirectIfLoggedIn() {
    if (isset($_SESSION['admin_id'])) {
        header("Location: index.php");
        exit();
    }
}

/**
 * Logout user
 */
function logout() {
    session_unset();
    session_destroy();
    header("Location: clinic-login-2026.php");
    exit();
}
?>
