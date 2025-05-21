<?php
// admin/pages.php
$pageTitle = 'Manage Pages';
require_once 'includes/header.php';

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Connect to database
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Delete page
    $stmt = $conn->prepare("DELETE FROM pages WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $message = '<div class="success-message">Page deleted successfully!</div>';
    } else {
        $message = '<div class="error-message">Error deleting page: ' . $conn->error . '</div>';
    }
    
    $stmt->close();
    $conn->close();
}

// Get all pages
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT id, title, slug, created_at FROM pages ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<div class="admin-container">
    <h1>Manage Pages</h1>
    
    <?php if (isset($message)) echo $message; ?>
    
    <div class="admin-actions">
        <a href="add-page.php" class="btn">Add New Page</a>
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
                            <a href="edit-page.php?id=<?php echo $row['id']; ?>" class="btn btn-small">Edit</a>
                            <a href="pages.php?action=delete&id=<?php echo $row['id']; ?>" class="btn btn-small btn-danger" onclick="return confirm('Are you sure you want to delete this page?')">Delete</a>
                            <a href="<?php echo SITE_URL; ?>/page/<?php echo $row['slug']; ?>" target="_blank" class="btn btn-small btn-secondary">View</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No pages found.</p>
    <?php endif; ?>
</div>

<?php
$conn->close();
require_once 'includes/footer.php';
?>