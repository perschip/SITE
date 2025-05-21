<?php
// admin/posts.php
$pageTitle = 'Manage Blog Posts';
require_once 'includes/header.php';

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Connect to database
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Delete post
    $stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $message = '<div class="success-message">Post deleted successfully!</div>';
    } else {
        $message = '<div class="error-message">Error deleting post: ' . $conn->error . '</div>';
    }
    
    $stmt->close();
    $conn->close();
}

// Get all posts
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT id, title, slug, created_at FROM posts ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<div class="admin-container">
    <h1>Manage Blog Posts</h1>
    
    <?php if (isset($message)) echo $message; ?>
    
    <div class="admin-actions">
        <a href="add-post.php" class="btn">Add New Post</a>
    </div>
    
    <?php if ($result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['title']; ?></td>
                        <td><?php echo formatDate($row['created_at']); ?></td>
                        <td>
                            <a href="edit-post.php?id=<?php echo $row['id']; ?>" class="btn btn-small">Edit</a>
                            <a href="posts.php?action=delete&id=<?php echo $row['id']; ?>" class="btn btn-small btn-danger" onclick="return confirm('Are you sure you want to delete this post?')">Delete</a>
                            <a href="<?php echo SITE_URL; ?>/blog/<?php echo $row['slug']; ?>" target="_blank" class="btn btn-small btn-secondary">View</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No blog posts found.</p>
    <?php endif; ?>
</div>

<?php
$conn->close();
require_once 'includes/footer.php';
?>