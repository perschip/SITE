<?php
// admin/add-post.php
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
    $slug = createSlug($title);
    $featured_image = '';
    
    // Handle image upload
    if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/';
        $filename = time() . '_' . basename($_FILES['featured_image']['name']);
        $upload_file = $upload_dir . $filename;
        
        if (move_uploaded_file($_FILES['featured_image']['tmp_name'], $upload_file)) {
            $featured_image = $filename;
        }
    }
    
    // Connect to database
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Insert post into database
    $stmt = $conn->prepare("INSERT INTO posts (title, content, slug, featured_image, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssss", $title, $content, $slug, $featured_image);
    
    if ($stmt->execute()) {
        $message = 'Post created successfully!';
    } else {
        $message = 'Error creating post: ' . $conn->error;
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
    <h1>Add New Blog Post</h1>
    
    <?php if ($message): ?>
        <div class="message"><?php echo $message; ?></div>
    <?php endif; ?>
    
    <form action="add-post.php" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" id="title" name="title" required>
        </div>
        
        <div class="form-group">
            <label for="content">Content</label>
            <textarea id="content" name="content" rows="15" required></textarea>
        </div>
        
        <div class="form-group">
            <label for="featured_image">Featured Image</label>
            <input type="file" id="featured_image" name="featured_image">
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn">Publish Post</button>
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
</script>

<?php require_once 'includes/footer.php'; ?>