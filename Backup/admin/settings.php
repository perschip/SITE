<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body class="theme-light">
    <?php
    session_start();
    require_once '../config.php';
    requireLogin();
    
    // Handle form submissions
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            if (isset($_POST['action'])) {
                switch ($_POST['action']) {
                    case 'update_site_settings':
                        $siteName = sanitize($_POST['site_name']);
                        $siteDescription = sanitize($_POST['site_description']);
                        $whatnotChannel = sanitize($_POST['whatnot_channel']);
                        $ebayStoreUrl = sanitize($_POST['ebay_store_url']);
                        
                        $stmt = $pdo->prepare("INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
                        $stmt->execute(['site_name', $siteName]);
                        $stmt->execute(['site_description', $siteDescription]);
                        $stmt->execute(['whatnot_channel', $whatnotChannel]);
                        $stmt->execute(['ebay_store_url', $ebayStoreUrl]);
                        
                        $success = "Site settings updated successfully!";
                        break;
                        
                    case 'change_password':
                        $currentPassword = $_POST['current_password'];
                        $newPassword = $_POST['new_password'];
                        $confirmPassword = $_POST['confirm_password'];
                        
                        // Verify current password
                        $stmt = $pdo->prepare("SELECT password FROM admin_users WHERE id = ?");
                        $stmt->execute([$_SESSION['admin_id']]);
                        $user = $stmt->fetch();
                        
                        if (!password_verify($currentPassword, $user['password'])) {
                            $error = "Current password is incorrect.";
                        } elseif ($newPassword !== $confirmPassword) {
                            $error = "New passwords do not match.";
                        } elseif (strlen($newPassword) < 8) {
                            $error = "Password must be at least 8 characters long.";
                        } else {
                            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                            $stmt = $pdo->prepare("UPDATE admin_users SET password = ? WHERE id = ?");
                            $stmt->execute([$hashedPassword, $_SESSION['admin_id']]);
                            $success = "Password changed successfully!";
                        }
                        break;
                        
                    case 'update_ebay_settings':
                        $clientId = sanitize($_POST['ebay_client_id']);
                        $clientSecret = sanitize($_POST['ebay_client_secret']);
                        $devId = sanitize($_POST['ebay_dev_id']);
                        
                        $stmt = $pdo->prepare("INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
                        $stmt->execute(['ebay_client_id', $clientId]);
                        $stmt->execute(['ebay_client_secret', $clientSecret]);
                        $stmt->execute(['ebay_dev_id', $devId]);
                        
                        $success = "eBay settings updated successfully!";
                        break;
                        
                    case 'upload_logo':
                        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === 0) {
                            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                            $fileType = $_FILES['logo']['type'];
                            
                            if (in_array($fileType, $allowedTypes)) {
                                $uploadDir = '../images/';
                                if (!file_exists($uploadDir)) {
                                    mkdir($uploadDir, 0777, true);
                                }
                                
                                $fileName = 'logo.' . pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
                                $uploadPath = $uploadDir . $fileName;
                                
                                if (move_uploaded_file($_FILES['logo']['tmp_name'], $uploadPath)) {
                                    $stmt = $pdo->prepare("INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
                                    $stmt->execute(['site_logo', 'images/' . $fileName]);
                                    $success = "Logo uploaded successfully!";
                                } else {
                                    $error = "Failed to upload logo.";
                                }
                            } else {
                                $error = "Invalid file type. Please upload a JPG, PNG, or GIF image.";
                            }
                        } else {
                            $error = "Please select a logo file to upload.";
                        }
                        break;
                }
            }
        } catch (Exception $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
    
    // Get current settings
    $stmt = $pdo->query("SELECT setting_key, setting_value FROM site_settings");
    $settings = [];
    while ($row = $stmt->fetch()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
    ?>
    
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">
                <i class="fas fa-tachometer-alt me-2"></i>Admin Dashboard
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="listings.php">eBay Listings</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="blog.php">Blog Posts</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="pages.php">Pages</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="stream.php">Stream Schedule</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php" target="_blank">
                            <i class="fas fa-external-link-alt me-1"></i>View Site
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">
                            <i class="fas fa-sign-out-alt me-1"></i>Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block bg-light sidebar admin-sidebar">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="dashboard.php">
                                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="listings.php">
                                <i class="fas fa-list me-2"></i>eBay Listings
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="blog.php">
                                <i class="fas fa-blog me-2"></i>Blog Posts
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="pages.php">
                                <i class="fas fa-file me-2"></i>Pages
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="stream.php">
                                <i class="fas fa-video me-2"></i>Stream Schedule
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="settings.php">
                                <i class="fas fa-cog me-2"></i>Settings
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Settings</h1>
                </div>

                <!-- Alerts -->
                <?php if (isset($success)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $success; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Settings Tabs -->
                <ul class="nav nav-tabs" id="settingsTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab">
                            <i class="fas fa-cog me-2"></i>General
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="logo-tab" data-bs-toggle="tab" data-bs-target="#logo" type="button" role="tab">
                            <i class="fas fa-image me-2"></i>Logo & Branding
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="ebay-tab" data-bs-toggle="tab" data-bs-target="#ebay" type="button" role="tab">
                            <i class="fab fa-ebay me-2"></i>eBay Settings
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="account-tab" data-bs-toggle="tab" data-bs-target="#account" type="button" role="tab">
                            <i class="fas fa-user me-2"></i>Account
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="settingsTabContent">
                    <!-- General Settings -->
                    <div class="tab-pane fade show active" id="general" role="tabpanel">
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5>General Site Settings</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <input type="hidden" name="action" value="update_site_settings">
                                    
                                    <div class="mb-3">
                                        <label for="site_name" class="form-label">Site Name</label>
                                        <input type="text" class="form-control" id="site_name" name="site_name" 
                                               value="<?php echo htmlspecialchars($settings['site_name'] ?? 'Tri-State Cards NJ'); ?>">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="site_description" class="form-label">Site Description</label>
                                        <textarea class="form-control" id="site_description" name="site_description" rows="3"><?php echo htmlspecialchars($settings['site_description'] ?? 'Your premier card shop'); ?></textarea>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="whatnot_channel" class="form-label">Whatnot Channel</label>
                                        <input type="text" class="form-control" id="whatnot_channel" name="whatnot_channel" 
                                               value="<?php echo htmlspecialchars($settings['whatnot_channel'] ?? 'tscardbreaks'); ?>">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="ebay_store_url" class="form-label">eBay Store URL</label>
                                        <input type="url" class="form-control" id="ebay_store_url" name="ebay_store_url" 
                                               value="<?php echo htmlspecialchars($settings['ebay_store_url'] ?? 'https://www.ebay.com/str/tristatecardsnj'); ?>">
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Logo & Branding -->
                    <div class="tab-pane fade" id="logo" role="tabpanel">
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5>Logo & Branding</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-4">
                                    <h6>Current Logo</h6>
                                    <?php if (isset($settings['site_logo']) && file_exists('../' . $settings['site_logo'])): ?>
                                        <img src="../<?php echo $settings['site_logo']; ?>" alt="Current Logo" class="img-thumbnail" style="max-height: 200px;">
                                    <?php else: ?>
                                        <p class="text-muted">No logo uploaded yet.</p>
                                    <?php endif; ?>
                                </div>
                                
                                <form method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="action" value="upload_logo">
                                    
                                    <div class="mb-3">
                                        <label for="logo" class="form-label">Upload New Logo</label>
                                        <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                                        <small class="form-text text-muted">Upload a JPG, PNG, or GIF image. Recommended size: 200x50 pixels.</small>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary">Upload Logo</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- eBay Settings -->
                    <div class="tab-pane fade" id="ebay" role="tabpanel">
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5>eBay API Settings</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <input type="hidden" name="action" value="update_ebay_settings">
                                    
                                    <div class="mb-3">
                                        <label for="ebay_client_id" class="form-label">Client ID (App ID)</label>
                                        <input type="text" class="form-control" id="ebay_client_id" name="ebay_client_id" 
                                               value="<?php echo htmlspecialchars($settings['ebay_client_id'] ?? EBAY_CLIENT_ID); ?>">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="ebay_client_secret" class="form-label">Client Secret (Cert ID)</label>
                                        <input type="password" class="form-control" id="ebay_client_secret" name="ebay_client_secret" 
                                               value="<?php echo htmlspecialchars($settings['ebay_client_secret'] ?? EBAY_CLIENT_SECRET); ?>">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="ebay_dev_id" class="form-label">Developer ID</label>
                                        <input type="text" class="form-control" id="ebay_dev_id" name="ebay_dev_id" 
                                               value="<?php echo htmlspecialchars($settings['ebay_dev_id'] ?? EBAY_DEV_ID); ?>">
                                    </div>
                                    
                                    <div class="alert alert-info">
                                        <strong>Note:</strong> After updating eBay settings, you may need to re-authenticate your connection.
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary">Save eBay Settings</button>
                                    <a href="dashboard.php" class="btn btn-outline-secondary">Re-authenticate eBay</a>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Account Settings -->
                    <div class="tab-pane fade" id="account" role="tabpanel">
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5>Change Password</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <input type="hidden" name="action" value="change_password">
                                    
                                    <div class="mb-3">
                                        <label for="current_password" class="form-label">Current Password</label>
                                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="new_password" class="form-label">New Password</label>
                                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                                        <small class="form-text text-muted">Password must be at least 8 characters long.</small>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary">Change Password</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/app.js"></script>
</body>
</html>