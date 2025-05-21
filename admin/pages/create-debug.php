<?php
// Include database connection
require_once '../../includes/db.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

// For debugging, let's log that the page was accessed
error_log('Pages create.php accessed at ' . date('Y-m-d H:i:s'));

// Initialize variables
$errors = [];
$title = '';
$slug = '';
$content = '';
$meta_description = '';
$status = 'published';

// Make sure upload directory exists
$upload_dir = '../../uploads/pages/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Check if form is posted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Log form submission 
    error_log('Form submitted. POST data: ' . print_r($_POST, true));

    // Get form data
    $title = trim($_POST['title'] ?? '');
    $slug = isset($_POST['slug']) && !empty($_POST['slug']) ? createSlug($_POST['slug']) : createSlug($title);
    $content = $_POST['content'] ?? '';
    $meta_description = trim($_POST['meta_description'] ?? '');
    $status = $_POST['status'] ?? 'published';
    
    // Basic validation
    if (empty($title)) {
        $errors[] = 'Page title is required';
    }
    
    if (empty($content)) {
        $errors[] = 'Page content is required';
    }

    // Log validation results
    error_log('Validation complete. Errors: ' . (!empty($errors) ? implode(', ', $errors) : 'none'));
    
    // If no errors, attempt to create the page
    if (empty($errors)) {
        try {
            error_log('Attempting to create page in database');
            
            // Check if pages table exists
            $tableCheck = $pdo->query("SHOW TABLES LIKE 'pages'");
            if ($tableCheck->rowCount() == 0) {
                // Table doesn't exist, create it
                error_log('Pages table does not exist. Creating it...');
                
                $createTable = "CREATE TABLE IF NOT EXISTS `pages` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `title` varchar(255) NOT NULL,
                    `slug` varchar(255) NOT NULL,
                    `content` text NOT NULL,
                    `meta_description` varchar(255) DEFAULT NULL,
                    `featured_image` varchar(255) DEFAULT NULL,
                    `status` enum('published','draft') NOT NULL DEFAULT 'published',
                    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `slug` (`slug`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
                
                $pdo->exec($createTable);
                error_log('Pages table created successfully');
            }
            
            // Simplified insert without featured image for now
            $query = "INSERT INTO pages (title, slug, content, meta_description, status, created_at, updated_at) 
                      VALUES (:title, :slug, :content, :meta_description, :status, NOW(), NOW())";
            
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':slug', $slug);
            $stmt->bindParam(':content', $content);
            $stmt->bindParam(':meta_description', $meta_description);
            $stmt->bindParam(':status', $status);
            
            error_log('Executing query: ' . $query);
            error_log('Parameters: title=' . $title . ', slug=' . $slug . ', status=' . $status);
            
            $result = $stmt->execute();
            
            if ($result) {
                error_log('Page created successfully. Last insert ID: ' . $pdo->lastInsertId());
                
                // Set success message
                $_SESSION['success_message'] = 'Page created successfully!';
                
                // Redirect to list page
                header('Location: list.php');
                exit;
            } else {
                error_log('Failed to create page. Error info: ' . print_r($stmt->errorInfo(), true));
                $errors[] = 'Failed to create the page. Database error.';
            }
        } catch (PDOException $e) {
            error_log('PDO Exception: ' . $e->getMessage());
            $errors[] = 'Database error: ' . $e->getMessage();
        }
    }
}

// Page variables
$page_title = 'Create Page';
$use_tinymce = true;

// Include admin header
include_once '../includes/header.php';
?>

<!-- Simple form for testing -->
<div class="container py-4">
    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Create Page - Debug Version</h5>
        </div>
        <div class="card-body">
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <form action="create.php" method="post">
                <div class="mb-3">
                    <label for="title" class="form-label">Title *</label>
                    <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($title); ?>" required>
                </div>
                
                <div class="mb-3">
                    <label for="content" class="form-label">Content *</label>
                    <textarea class="form-control" id="content" name="content" rows="5" required><?php echo htmlspecialchars($content); ?></textarea>
                </div>
                
                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-control" id="status" name="status">
                        <option value="published" selected>Published</option>
                        <option value="draft">Draft</option>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-primary">Create Page</button>
                <a href="list.php" class="btn btn-secondary">Back to List</a>
            </form>
        </div>
    </div>
    
    <!-- Debug Info -->
    <div class="card mt-4">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0">Debug Information</h5>
        </div>
        <div class="card-body">
            <h6>POST Data:</h6>
            <pre><?php print_r($_POST); ?></pre>
            
            <h6>Server Info:</h6>
            <pre>REQUEST_METHOD: <?php echo $_SERVER['REQUEST_METHOD']; ?>
PHP_SELF: <?php echo $_SERVER['PHP_SELF']; ?>
</pre>
            
            <h6>Database Check:</h6>
            <?php
            try {
                $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
                echo '<pre>Tables: ' . implode(', ', $tables) . '</pre>';
                
                if (in_array('pages', $tables)) {
                    $cols = $pdo->query("DESCRIBE pages")->fetchAll(PDO::FETCH_COLUMN);
                    echo '<pre>Pages columns: ' . implode(', ', $cols) . '</pre>';
                }
            } catch (Exception $e) {
                echo '<div class="alert alert-danger">Database error: ' . $e->getMessage() . '</div>';
            }
            ?>
        </div>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>