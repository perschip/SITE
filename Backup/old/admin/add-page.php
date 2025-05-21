<?php
// admin/add-page.php
session_start();
require_once '../config/config.php';

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';
    $slug = $_POST['slug'] ?? createSlug($title);
    
    // Connect to database
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Insert page into database
    $stmt = $conn->prepare("INSERT INTO pages (title, content, slug, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("sss", $title, $content, $slug);
    
    if ($stmt->execute()) {
        $message = 'Page created successfully!';
    } else {
        $message = 'Error creating page: ' . $conn->error;
    }
    
    $stmt->close();
    $conn->close();
}

// Function to create a URL slug from a title
function createSlug($string) {
    $string = strtolower($string);
    $string = preg_replace('/[^a-z0-9\s-]/', '', $string);
    $string = preg_replace('/[\s-]+/', '-', $string);
    $string = trim($string, '-');
    return $string;
}

require_once 'includes/header.php';
?>

<div class="admin-container">
    <h1>Add New Page</h1>
    
    <?php if ($message): ?>
        <div class="message"><?php echo $message; ?></div>
    <?php endif; ?>
    
    <form action="add-page.php" method="post">
        <div class="form-group">
            <label for="title">Page Title</label>
            <input type="text" id="title" name="title" required>
        </div>
        
        <div class="form-group">
            <label for="slug">URL Slug</label>
            <input type="text" id="slug" name="slug" placeholder="Leave blank to generate from title">
            <small>This will be the URL: <?php echo SITE_URL; ?>/page/<span id="slug-preview">page-slug</span></small>
        </div>
        
        <div class="form-group">
            <label for="content">Content</label>
            <textarea id="content" name="content" rows="15" required></textarea>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn">Publish Page</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<script src="js/tinymce/tinymce.min.js"></script>
<script>
    tinymce.init({
        selector: '#content',
        height: 400,
        plugins: 'lists link image code table',
        toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright | bullist numlist outdent indent | link image | code'
    });
    
    // Update slug preview when title is typed
    document.getElementById('title').addEventListener('input', function() {
        const titleValue = this.value;
        const slugValue = titleValue.toLowerCase()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/[\s-]+/g, '-')
            .trim('-');
        
        document.getElementById('slug-preview').textContent = slugValue;
    });
</script>

<?php require_once 'includes/footer.php'; ?>