<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    session_start();
    require_once '../config.php';
    requireLogin();
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}

// Default values to prevent undefined variable errors
$status = isset($_GET['status']) ? $_GET['status'] : 'all';
$search = isset($_GET['search']) ? $_GET['search'] : '';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 20;
$offset = ($page - 1) * $perPage;
$total = 0;
$listings = [];
$stats = [
    'total' => 0,
    'active' => 0,
    'sold' => 0,
    'ended' => 0
];

try {
    // Build query conditions
    $conditions = [];
    $params = [];
    
    if ($status !== 'all') {
        $conditions[] = "status = ?";
        $params[] = $status;
    }
    
    if (!empty($search)) {
        $conditions[] = "title LIKE ?";
        $params[] = "%{$search}%";
    }
    
    $whereClause = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';
    
    // Get total count
    $countSql = "SELECT COUNT(*) as total FROM ebay_listings {$whereClause}";
    $stmt = $pdo->prepare($countSql);
    $stmt->execute($params);
    $total = $stmt->fetch()['total'];
    
    // Get listings with proper parameter binding
    $sql = "SELECT * FROM ebay_listings {$whereClause} ORDER BY last_updated DESC LIMIT :limit OFFSET :offset";
    $stmt = $pdo->prepare($sql);
    
    // Bind the WHERE clause parameters
    foreach ($params as $key => $param) {
        $stmt->bindValue($key + 1, $param, PDO::PARAM_STR);
    }
    
    // Bind LIMIT and OFFSET as integers
    $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    
    $stmt->execute();
    $listings = $stmt->fetchAll();
    
    // Get stats
    $stmt = $pdo->query("SELECT status, COUNT(*) as count FROM ebay_listings GROUP BY status");
    while ($row = $stmt->fetch()) {
        $stats[$row['status']] = $row['count'];
        $stats['total'] += $row['count'];
    }
} catch (Exception $e) {
    $error = "Database error: " . $e->getMessage();
}

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    try {
        switch ($_POST['action']) {
            case 'sync_listings':
                require_once '../classes/EbayAPI.php';
                $ebayAPI = new EbayAPI();
                $result = $ebayAPI->updateLocalListings();
                
                if ($result) {
                    $success = "eBay listings synchronized successfully!";
                } else {
                    $error = "Failed to sync eBay listings. Check error logs.";
                }
                break;
                
            case 'delete_listing':
                if (isset($_POST['listing_id'])) {
                    $listingId = intval($_POST['listing_id']);
                    $stmt = $pdo->prepare("DELETE FROM ebay_listings WHERE id = ?");
                    $stmt->execute([$listingId]);
                    $success = "Listing deleted successfully!";
                }
                break;
                
            case 'update_status':
                if (isset($_POST['listing_id']) && isset($_POST['new_status'])) {
                    $listingId = intval($_POST['listing_id']);
                    $newStatus = $_POST['new_status'];
                    $stmt = $pdo->prepare("UPDATE ebay_listings SET status = ? WHERE id = ?");
                    $stmt->execute([$newStatus, $listingId]);
                    $success = "Listing status updated successfully!";
                }
                break;
        }
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>eBay Listings - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body class="theme-light">
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
                        <a class="nav-link active" href="listings.php">eBay Listings</a>
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

    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2">
                <i class="fab fa-ebay me-2"></i>eBay Listings
            </h1>
            <div>
                <form method="POST" class="d-inline">
                    <input type="hidden" name="action" value="sync_listings">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-sync me-1"></i>Sync Listings
                    </button>
                </form>
                <a href="../listings.php" target="_blank" class="btn btn-outline-secondary">
                    <i class="fas fa-external-link-alt me-1"></i>View Public Page
                </a>
            </div>
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

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h6 class="text-uppercase fw-semibold">Total Listings</h6>
                        <h3 class="mb-0"><?php echo $stats['total']; ?></h3>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h6 class="text-uppercase fw-semibold">Active</h6>
                        <h3 class="mb-0"><?php echo $stats['active']; ?></h3>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h6 class="text-uppercase fw-semibold">Sold</h6>
                        <h3 class="mb-0"><?php echo $stats['sold']; ?></h3>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card bg-secondary text-white">
                    <div class="card-body">
                        <h6 class="text-uppercase fw-semibold">Ended</h6>
                        <h3 class="mb-0"><?php echo $stats['ended']; ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="all" <?php echo $status === 'all' ? 'selected' : ''; ?>>All Listings</option>
                            <option value="active" <?php echo $status === 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="sold" <?php echo $status === 'sold' ? 'selected' : ''; ?>>Sold</option>
                            <option value="ended" <?php echo $status === 'ended' ? 'selected' : ''; ?>>Ended</option>
                        </select>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="search" class="form-label">Search</label>
                        <input type="text" class="form-control" id="search" name="search" placeholder="Search listings..." value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-1"></i>Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Listings Table -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Listings (<?php echo $total; ?> total)</h5>
            </div>
            <div class="card-body">
                <?php if (empty($listings)): ?>
                    <p class="text-muted text-center py-4">No listings found matching your criteria.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Title</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                    <th>Last Updated</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($listings as $listing): ?>
                                    <tr>
                                        <td>
                                            <img src="<?php echo $listing['image_url'] ?: '/api/placeholder/100/100'; ?>" 
                                                 alt="<?php echo htmlspecialchars($listing['title']); ?>" 
                                                 style="width: 60px; height: 60px; object-fit: cover;" 
                                                 class="rounded">
                                        </td>
                                        <td>
                                            <div class="fw-semibold"><?php echo htmlspecialchars($listing['title']); ?></div>
                                            <small class="text-muted">ID: <?php echo $listing['ebay_item_id']; ?></small>
                                        </td>
                                        <td class="fw-semibold">
                                            $<?php echo number_format($listing['price'], 2); ?>
                                        </td>
                                        <td>
                                            <?php
                                            $statusClass = [
                                                'active' => 'success',
                                                'sold' => 'info',
                                                'ended' => 'secondary'
                                            ];
                                            $class = $statusClass[$listing['status']] ?? 'secondary';
                                            ?>
                                            <span class="badge bg-<?php echo $class; ?>">
                                                <?php echo ucfirst($listing['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php echo date('M j, Y g:i A', strtotime($listing['last_updated'])); ?>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?php echo $listing['listing_url']; ?>" 
                                                   target="_blank" 
                                                   class="btn btn-outline-primary">
                                                    <i class="fab fa-ebay"></i>
                                                </a>
                                                
                                                <form method="POST" class="d-inline">
                                                    <input type="hidden" name="action" value="delete_listing">
                                                    <input type="hidden" name="listing_id" value="<?php echo $listing['id']; ?>">
                                                    <button type="submit" 
                                                            class="btn btn-outline-danger" 
                                                            onclick="return confirm('Are you sure you want to delete this listing?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php
                    $totalPages = ceil($total / $perPage);
                    if ($totalPages > 1):
                    ?>
                        <nav aria-label="Listings pagination" class="mt-4">
                            <ul class="pagination justify-content-center">
                                <?php if ($page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?php echo $page - 1; ?>&status=<?php echo $status; ?>&search=<?php echo urlencode($search); ?>">Previous</a>
                                    </li>
                                <?php endif; ?>
                                
                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $i; ?>&status=<?php echo $status; ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor; ?>
                                
                                <?php if ($page < $totalPages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?php echo $page + 1; ?>&status=<?php echo $status; ?>&search=<?php echo urlencode($search); ?>">Next</a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>