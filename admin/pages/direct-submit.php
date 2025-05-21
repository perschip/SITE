<?php
// Direct page submission script - an emergency solution for adding pages
// Save this as admin/pages/direct-submit.php and access it via browser

// Include database connection and essential files
require_once '../../includes/db.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

// Initialize result variables
$success = false;
$message = '';

// Process page creation on form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get and sanitize form data
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $status = $_POST['status'] ?? 'published';
    
    // Basic validation
    if (empty($title)) {
        $message = 'Error: Title is required';
    } elseif (empty($content)) {
        $message = 'Error: Content is required';
    } else {
        // Generate slug from title
        $slug = createSlug($title);
        
        // Check for duplicate slug
        try {
            $check_query = "SELECT COUNT(*) as count FROM pages WHERE slug = :slug";
            $check_stmt = $pdo->prepare($check_query);
            $check_stmt->bindParam(':slug', $slug);
            $check_stmt->execute();
            $row = $check_stmt->fetch();
            
            if ($row['count'] > 0) {
                // Add unique identifier to make slug unique
                $slug = $slug . '-' . date('mdY');
            }
            
            // Generate excerpt for meta description
            $meta_description = generateExcerpt($content, 160);
            
            // Insert the page
            $query = "INSERT INTO pages (title, slug, content, meta_description, status, created_at, updated_at) 
                      VALUES (:title, :slug, :content, :meta_description, :status, NOW(), NOW())";
            
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':slug', $slug);
            $stmt->bindParam(':content', $content);
            $stmt->bindParam(':meta_description', $meta_description);
            $stmt->bindParam(':status', $status);
            $stmt->execute();
            
            $page_id = $pdo->lastInsertId();
            $success = true;
            $message = "Page created successfully! ID: $page_id";
            
            // Add to navigation if checkbox was checked
            if (isset($_POST['add_to_navigation']) && $_POST['add_to_navigation'] == '1') {
                try {
                    // Check if navigation table exists
                    $nav_check = $pdo->query("SHOW TABLES LIKE 'navigation'");
                    if ($nav_check->rowCount() > 0) {
                        // Get highest display_order
                        $order_query = "SELECT MAX(display_order) as max_order FROM navigation";
                        $order_stmt = $pdo->prepare($order_query);
                        $order_stmt->execute();
                        $order_result = $order_stmt->fetch();
                        $display_order = ($order_result && isset($order_result['max_order'])) ? $order_result['max_order'] + 1 : 5;
                        
                        // Get location preference
                        $location = isset($_POST['nav_location']) ? $_POST['nav_location'] : 'header';
                        
                        // Make sure location is valid
                        if (!in_array($location, ['header', 'footer', 'both'])) {
                            $location = 'header';
                        }
                        
                        // Add to navigation table
                        $nav_query = "INSERT INTO navigation (title, url, page_id, display_order, location, is_active) 
                                    VALUES (:title, :slug, :page_id, :display_order, :location, 1)";
                        $nav_stmt = $pdo->prepare($nav_query);
                        $nav_stmt->bindParam(':title', $title);
                        $nav_stmt->bindParam(':slug', $slug);
                        $nav_stmt->bindParam(':page_id', $page_id);
                        $nav_stmt->bindParam(':display_order', $display_order);
                        $nav_stmt->bindParam(':location', $location);
                        $nav_stmt->execute();
                    }
                } catch (PDOException $e) {
                    // Don't stop page creation if navigation fails
                    $message .= " (Note: Navigation menu item not added: " . $e->getMessage() . ")";
                }
            }
            
            // Clear form data after successful submission
            $title = '';
            $content = '';
            
        } catch (Exception $e) {
            $message = 'Database error: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Direct Page Submission</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f5f5f5;
            padding: 20px;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .card-header {
            background-color: #007bff;
            color: white;
            border-radius: 10px 10px 0 0 !important;
        }
        .btn-primary {
            background-color: #007bff;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <div class="card mt-4">
                    <div class="card-header">
                        <h3 class="mb-0">Direct Page Submission</h3>
                        <p class="mb-0 small">Emergency tool for adding pages</p>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($message)): ?>
                            <div class="alert alert-<?php echo $success ? 'success' : 'danger'; ?> mb-4">
                                <?php echo htmlspecialchars($message); ?>
                                <?php if ($success): ?>
                                    <div class="mt-2">
                                        <a href="list.php" class="btn btn-sm btn-primary">View All Pages</a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="post" action="">
                            <div class="mb-3">
                                <label for="title" class="form-label">Page Title *</label>
                                <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($title ?? ''); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="content" class="form-label">Page Content *</label>
                                <textarea class="form-control" id="content" name="content" rows="15" required><?php echo htmlspecialchars($content ?? ''); ?></textarea>
                                <small class="form-text text-muted">You can use HTML tags for formatting.</small>
                            </div>
                            
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="published" selected>Published</option>
                                    <option value="draft">Draft</option>
                                </select>
                            </div>
                            
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="add_to_navigation" name="add_to_navigation" value="1" checked>
                                <label class="form-check-label" for="add_to_navigation">Add to Navigation Menu</label>
                            </div>
                            
                            <div class="mb-3 ps-4 nav-location-options">
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" id="nav_location_header" name="nav_location" value="header" checked>
                                    <label class="form-check-label" for="nav_location_header">Header Only</label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" id="nav_location_footer" name="nav_location" value="footer">
                                    <label class="form-check-label" for="nav_location_footer">Footer Only</label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" id="nav_location_both" name="nav_location" value="both">
                                    <label class="form-check-label" for="nav_location_both">Both Header & Footer</label>
                                </div>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">Create Page</button>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer">
                        <a href="list.php" class="btn btn-sm btn-outline-secondary">Back to Page List</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
    // Toggle navigation options based on checkbox
    document.addEventListener('DOMContentLoaded', function() {
        const navCheckbox = document.getElementById('add_to_navigation');
        const navOptions = document.querySelector('.nav-location-options');
        
        function toggleNavOptions() {
            if (navCheckbox.checked) {
                navOptions.style.display = 'block';
            } else {
                navOptions.style.display = 'none';
            }
        }
        
        // Set initial state
        toggleNavOptions();
        
        // Add event listener
        navCheckbox.addEventListener('change', toggleNavOptions);
    });
    </script>
</body>
</html>