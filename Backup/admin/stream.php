<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stream Management - Admin Dashboard</title>
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
                    case 'add_stream':
                        $title = sanitize($_POST['title']);
                        $description = sanitize($_POST['description']);
                        $datetime = $_POST['scheduled_time'];
                        
                        $stmt = $pdo->prepare("INSERT INTO stream_schedule (title, description, scheduled_time) VALUES (?, ?, ?)");
                        $stmt->execute([$title, $description, $datetime]);
                        
                        $success = "Stream scheduled successfully!";
                        break;
                        
                    case 'edit_stream':
                        $id = intval($_POST['stream_id']);
                        $title = sanitize($_POST['title']);
                        $description = sanitize($_POST['description']);
                        $datetime = $_POST['scheduled_time'];
                        $isLive = isset($_POST['is_live']) ? 1 : 0;
                        
                        $stmt = $pdo->prepare("UPDATE stream_schedule SET title = ?, description = ?, scheduled_time = ?, is_live = ? WHERE id = ?");
                        $stmt->execute([$title, $description, $datetime, $isLive, $id]);
                        
                        $success = "Stream updated successfully!";
                        break;
                        
                    case 'delete_stream':
                        $id = intval($_POST['stream_id']);
                        $stmt = $pdo->prepare("DELETE FROM stream_schedule WHERE id = ?");
                        $stmt->execute([$id]);
                        
                        $success = "Stream deleted successfully!";
                        break;
                        
                    case 'toggle_live':
                        $id = intval($_POST['stream_id']);
                        $stmt = $pdo->prepare("SELECT is_live FROM stream_schedule WHERE id = ?");
                        $stmt->execute([$id]);
                        $current = $stmt->fetchColumn();
                        
                        $newStatus = $current ? 0 : 1;
                        $stmt = $pdo->prepare("UPDATE stream_schedule SET is_live = ? WHERE id = ?");
                        $stmt->execute([$newStatus, $id]);
                        
                        $success = $newStatus ? "Stream marked as LIVE!" : "Stream marked as offline.";
                        break;
                }
            }
        } catch (Exception $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
    
    // Get all streams
    $stmt = $pdo->query("SELECT * FROM stream_schedule ORDER BY scheduled_time ASC");
    $streams = $stmt->fetchAll();
    
    // Get editing stream if requested
    $editStream = null;
    if (isset($_GET['edit'])) {
        $stmt = $pdo->prepare("SELECT * FROM stream_schedule WHERE id = ?");
        $stmt->execute([intval($_GET['edit'])]);
        $editStream = $stmt->fetch();
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
                        <a class="nav-link active" href="stream.php">Stream Schedule</a>
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
                            <a class="nav-link active" href="stream.php">
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
                    <h1 class="h2">
                        <i class="fas fa-video me-2"></i>Stream Management
                    </h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStreamModal">
                            <i class="fas fa-plus me-1"></i>Add Stream
                        </button>
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

                <!-- Upcoming Streams -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Scheduled Streams</h5>
                                <a href="../stream.php" target="_blank" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-external-link-alt me-1"></i>View Public Page
                                </a>
                            </div>
                            <div class="card-body">
                                <?php if (empty($streams)): ?>
                                    <p class="text-muted text-center py-4">No streams scheduled yet.</p>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Title</th>
                                                    <th>Scheduled Time</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($streams as $stream): ?>
                                                    <tr>
                                                        <td>
                                                            <strong><?php echo htmlspecialchars($stream['title']); ?></strong>
                                                            <?php if ($stream['description']): ?>
                                                                <br><small class="text-muted"><?php echo htmlspecialchars(substr($stream['description'], 0, 100)); ?>...</small>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <?php echo date('M j, Y g:i A', strtotime($stream['scheduled_time'])); ?>
                                                        </td>
                                                        <td>
                                                            <?php if ($stream['is_live']): ?>
                                                                <span class="badge bg-success">
                                                                    <i class="fas fa-circle me-1"></i>LIVE
                                                                </span>
                                                            <?php elseif (strtotime($stream['scheduled_time']) < time()): ?>
                                                                <span class="badge bg-secondary">Ended</span>
                                                            <?php else: ?>
                                                                <span class="badge bg-info">Scheduled</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <div class="btn-group btn-group-sm" role="group">
                                                                <a href="?edit=<?php echo $stream['id']; ?>" class="btn btn-outline-primary">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                                
                                                                <?php if (!$stream['is_live']): ?>
                                                                    <form method="POST" class="d-inline">
                                                                        <input type="hidden" name="action" value="toggle_live">
                                                                        <input type="hidden" name="stream_id" value="<?php echo $stream['id']; ?>">
                                                                        <button type="submit" class="btn btn-outline-success" title="Mark as Live">
                                                                            <i class="fas fa-broadcast-tower"></i>
                                                                        </button>
                                                                    </form>
                                                                <?php else: ?>
                                                                    <form method="POST" class="d-inline">
                                                                        <input type="hidden" name="action" value="toggle_live">
                                                                        <input type="hidden" name="stream_id" value="<?php echo $stream['id']; ?>">
                                                                        <button type="submit" class="btn btn-outline-warning" title="Mark as Offline">
                                                                            <i class="fas fa-stop-circle"></i>
                                                                        </button>
                                                                    </form>
                                                                <?php endif; ?>
                                                                
                                                                <form method="POST" class="d-inline">
                                                                    <input type="hidden" name="action" value="delete_stream">
                                                                    <input type="hidden" name="stream_id" value="<?php echo $stream['id']; ?>">
                                                                    <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Are you sure you want to delete this stream?')">
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
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Add Stream Modal -->
    <div class="modal fade" id="addStreamModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <?php echo $editStream ? 'Edit Stream' : 'Schedule New Stream'; ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <input type="hidden" name="action" value="<?php echo $editStream ? 'edit_stream' : 'add_stream'; ?>">
                    <?php if ($editStream): ?>
                        <input type="hidden" name="stream_id" value="<?php echo $editStream['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="title" class="form-label">Stream Title</label>
                            <input type="text" class="form-control" id="title" name="title" 
                                   value="<?php echo $editStream ? htmlspecialchars($editStream['title']) : ''; ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"><?php echo $editStream ? htmlspecialchars($editStream['description']) : ''; ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="scheduled_time" class="form-label">Scheduled Time</label>
                            <input type="datetime-local" class="form-control" id="scheduled_time" name="scheduled_time" 
                                   value="<?php echo $editStream ? date('Y-m-d\TH:i', strtotime($editStream['scheduled_time'])) : ''; ?>" required>
                        </div>
                        
                        <?php if ($editStream): ?>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_live" name="is_live" 
                                           <?php echo $editStream['is_live'] ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="is_live">
                                        Currently Live
                                    </label>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <?php echo $editStream ? 'Update Stream' : 'Schedule Stream'; ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/app.js"></script>
    <script>
        <?php if ($editStream): ?>
            // Automatically open modal if editing
            document.addEventListener('DOMContentLoaded', function() {
                var editModal = new bootstrap.Modal(document.getElementById('addStreamModal'));
                editModal.show();
            });
        <?php endif; ?>
    </script>
</body>
</html>