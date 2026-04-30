<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

if (isset($_SESSION['admin_id'])) {
    logActivity($conn, $_SESSION['admin_id'], "Logout", "Admin logged out");
}

logout();
?>
