<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Tri-State Cards NJ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body class="theme-light">
    <?php
    session_start();
    require_once '../config.php';
    requireLogin();
    
    // Get statistics
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM ebay_listings WHERE status = 'active'");
    $activeListings = $stmt->fetch()['count'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM blog_posts WHERE is_published = TRUE");
    $publishedPosts = $stmt->fetch()['count'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM pages WHERE is_published = TRUE");
    $publishedPages = $stmt->fetch()['count'];
    
    // Check eBay authentication status
    $stmt = $pdo->query("SELECT setting_value FROM site_settings WHERE setting_key = 'ebay_access_token'");
    $ebayToken = $stmt->fetch();
    $isEbayAuthenticated = $ebayToken && !empty($ebayToken['setting_value']);
    
    // Get last sync time
    $stmt = $pdo->query("SELECT setting_value FROM site_settings WHERE setting_key = 'last_ebay_sync'");
    $lastSync = $stmt->fetch();
    $lastSyncTime = $lastSync ? $lastSync['setting_value'] : null;
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
                        <a class="nav-link active" href="dashboard.php">Dashboard</a>
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
                            <a class="nav-link active" href="dashboard.php">
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
                            <a class="nav-link" href="settings.php">
                                <i class="fas fa-cog me-2"></i>Settings
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Dashboard</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="refreshStats()">
                                <i class="fas fa-sync me-1"></i>Refresh
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Alerts -->
                <?php if (isset($_GET['message'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($_GET['message']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($_GET['error']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- eBay Integration Status -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">eBay Integration Status</h5>
                            </div>
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-4">
                                        <p class="mb-0">
                                            <strong>Status:</strong> 
                                            <?php if ($isEbayAuthenticated): ?>
                                                <span class="badge bg-success">Authenticated</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Not Authenticated</span>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="mb-0">
                                            <strong>Last Sync:</strong> 
                                            <span data-sync-time><?php echo $lastSyncTime ? date('Y-m-d H:i:s', strtotime($lastSyncTime)) : 'Never'; ?></span>
                                        </p>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <?php if (!$isEbayAuthenticated): ?>
                                            <a href="https://auth.ebay.com/oauth2/authorize?client_id=<?php echo urlencode(EBAY_CLIENT_ID); ?>&response_type=code&redirect_uri=<?php echo urlencode(SITE_URL . '/admin/ebay-callback.php'); ?>&scope=https://api.ebay.com/oauth/api_scope" 
                                               class="btn btn-primary">
                                                <i class="fas fa-link me-1"></i>Authenticate eBay
                                            </a>
                                        <?php else: ?>
                                            <button class="btn btn-success" onclick="syncEbayListings()" <?php echo !$isEbayAuthenticated ? 'disabled' : ''; ?>>
                                                <i class="fas fa-sync me-1"></i>Sync Now
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row">
                    <div class="col-md-3 mb-4">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-uppercase fw-semibold">Active Listings</h6>
                                        <h3 class="mb-0"><?php echo $activeListings; ?></h3>
                                    </div>
                                    <div class="opacity-50">
                                        <i class="fas fa-shopping-cart fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 mb-4">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-uppercase fw-semibold">Blog Posts</h6>
                                        <h3 class="mb-0"><?php echo $publishedPosts; ?></h3>
                                    </div>
                                    <div class="opacity-50">
                                        <i class="fas fa-blog fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 mb-4">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-uppercase fw-semibold">Custom Pages</h6>
                                        <h3 class="mb-0"><?php echo $publishedPages; ?></h3>
                                    </div>
                                    <div class="opacity-50">
                                        <i class="fas fa-file fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 mb-4">
                        <div class="card bg-warning text-dark">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-uppercase fw-semibold">Stream Status</h6>
                                        <h3 class="mb-0" id="stream-status-text">Offline</h3>
                                    </div>
                                    <div class="opacity-50">
                                        <i class="fas fa-video fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Quick Actions</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <a href="blog-new.php" class="btn btn-primary w-100">
                                            <i class="fas fa-plus me-2"></i>New Blog Post
                                        </a>
                                    </div>
                                    <div class="col-md-3">
                                        <a href="page-new.php" class="btn btn-success w-100">
                                            <i class="fas fa-plus me-2"></i>New Page
                                        </a>
                                    </div>
                                    <div class="col-md-3">
                                        <button class="btn btn-info w-100" onclick="syncEbayListings()" <?php echo !$isEbayAuthenticated ? 'disabled' : ''; ?>>
                                            <i class="fas fa-sync me-2"></i>Sync eBay Listings
                                        </button>
                                    </div>
                                    <div class="col-md-3">
                                        <a href="stream-new.php" class="btn btn-warning w-100">
                                            <i class="fas fa-calendar-plus me-2"></i>Schedule Stream
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Blog Posts -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Recent Blog Posts</h5>
                            </div>
                            <div class="card-body">
                                <?php
                                $stmt = $pdo->query("SELECT * FROM blog_posts ORDER BY created_at DESC LIMIT 5");
                                $recentPosts = $stmt->fetchAll();
                                
                                if (empty($recentPosts)): ?>
                                    <p class="text-muted">No blog posts yet.</p>
                                <?php else: ?>
                                    <div class="list-group list-group-flush">
                                        <?php foreach ($recentPosts as $post): ?>
                                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="mb-1"><?php echo htmlspecialchars($post['title']); ?></h6>
                                                    <small class="text-muted"><?php echo date('F j, Y', strtotime($post['created_at'])); ?></small>
                                                </div>
                                                <div>
                                                    <?php if ($post['is_published']): ?>
                                                        <span class="badge bg-success">Published</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-warning">Draft</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Upcoming Streams</h5>
                            </div>
                            <div class="card-body">
                                <?php
                                $stmt = $pdo->query("SELECT * FROM stream_schedule WHERE scheduled_time > NOW() ORDER BY scheduled_time ASC LIMIT 5");
                                $upcomingStreams = $stmt->fetchAll();
                                
                                if (empty($upcomingStreams)): ?>
                                    <p class="text-muted">No scheduled streams.</p>
                                <?php else: ?>
                                    <div class="list-group list-group-flush">
                                        <?php foreach ($upcomingStreams as $stream): ?>
                                            <div class="list-group-item">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <h6 class="mb-1"><?php echo htmlspecialchars($stream['title']); ?></h6>
                                                        <small class="text-muted"><?php echo date('F j, Y g:i A', strtotime($stream['scheduled_time'])); ?></small>
                                                    </div>
                                                    <?php if ($stream['is_live']): ?>
                                                        <span class="badge bg-success">Live</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/app.js"></script>
    <script>
        async function syncEbayListings() {
            const btn = event.target;
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Syncing...';
            btn.disabled = true;
            
            try {
                const response = await fetch(window.location.href.includes('/admin/') ? 'sync-ebay.php' : 'admin/sync-ebay.php');
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                
                if (data.success) {
                    showToast(data.message || 'eBay listings synced successfully!', 'success');
                    refreshStats();
                    
                    // Update last sync time if available
                    const syncTimeElement = document.querySelector('[data-sync-time]');
                    if (syncTimeElement && data.timestamp) {
                        syncTimeElement.textContent = data.timestamp;
                    }
                } else {
                    showToast(data.message || data.error || 'Error syncing listings', 'danger');
                }
            } catch (error) {
                console.error('Sync error:', error);
                showToast('Error syncing listings: ' + error.message, 'danger');
            } finally {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        }
        
        function refreshStats() {
            // Refresh all statistics
            window.location.reload();
        }
        
        // Load stream status
        document.addEventListener('DOMContentLoaded', function() {
            fetch('../api/stream-status.php')
                .then(response => response.json())
                .then(data => {
                    const statusText = document.getElementById('stream-status-text');
                    if (data.is_live) {
                        statusText.textContent = 'Live';
                        statusText.parentElement.parentElement.className = 'card bg-success text-white';
                    }
                })
                .catch(error => console.error('Error loading stream status:', error));
        });
    </script>
</body>
</html>