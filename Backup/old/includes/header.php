<?php
// includes/header.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' . SITE_NAME : SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="icon" href="<?php echo SITE_URL; ?>/images/favicon.ico">
    <!-- Add some mobile menu functionality -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
    <header>
        <div class="container header-content">
<div class="logo">
    <a href="<?php echo SITE_URL; ?>">
        <img src="<?php echo SITE_URL; ?>/images/logo.png" alt="<?php echo SITE_NAME; ?>" style="height: 30px; width: auto;">
    </a>
</div>
            
            <nav id="main-nav">
                <ul>
                    <li><a href="<?php echo SITE_URL; ?>">Home</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/index.php?page=ebay">eBay Listings</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/index.php?page=blog">Blog</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/index.php?page=contact">Contact</a></li>
                </ul>
            </nav>
            
            <div class="mobile-menu-icon" id="mobile-menu-toggle">
                <i class="fas fa-bars"></i>
            </div>
            
            <div class="dark-mode-switch">
                <label class="toggle">
                    <input type="checkbox" id="dark-mode-toggle">
                    <span class="slider"></span>
                </label>
                <label for="dark-mode-toggle">Dark Mode</label>
            </div>
        </div>
    </header>
    
    <main>
        <div class="container">