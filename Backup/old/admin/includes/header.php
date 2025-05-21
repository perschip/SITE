<?php
// admin/includes/header.php
session_start();
require_once '../config/config.php';
require_once '../includes/functions.php';

// Check if user is logged in
if (!isLoggedIn() && basename($_SERVER['PHP_SELF']) !== 'login.php') {
    redirect('login.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - Admin' : 'Admin Dashboard'; ?> | <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="css/admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body class="admin-body">
    <header class="admin-header">
        <div class="container admin-header-content">
            <div class="admin-logo">
                <a href="index.php">
                    <img src="../images/logo.png" alt="<?php echo SITE_NAME; ?> Admin">
                    <span>Admin Panel</span>
                </a>
            </div>
            
            <?php if (isLoggedIn()): ?>
            <div class="mobile-menu-icon" id="admin-mobile-toggle">
                <i class="fas fa-bars"></i>
            </div>
            
            <nav class="admin-nav" id="admin-nav">
                <ul>
                    <li><a href="index.php" class="<?php echo isActive(basename($_SERVER['PHP_SELF']), 'index.php'); ?>">Dashboard</a></li>
                    <li><a href="posts.php" class="<?php echo isActive(basename($_SERVER['PHP_SELF']), 'posts.php'); ?>">Blog Posts</a></li>
                    <li><a href="pages.php" class="<?php echo isActive(basename($_SERVER['PHP_SELF']), 'pages.php'); ?>">Pages</a></li>
                    <li><a href="ebay-settings.php" class="<?php echo isActive(basename($_SERVER['PHP_SELF']), 'ebay-settings.php'); ?>">eBay Settings</a></li>
                    <li><a href="../" target="_blank">View Site</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
            <?php endif; ?>
        </div>
    </header>
    
    <main class="admin-main">
        <div class="container">