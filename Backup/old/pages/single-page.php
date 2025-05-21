<?php
// pages/single-page.php
$slug = $_GET['slug'] ?? '';

// Connect to database
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get page by slug
$stmt = $conn->prepare("SELECT id, title, content FROM pages WHERE slug = ?");
$stmt->bind_param("s", $slug);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $page = $result->fetch_assoc();
    $pageTitle = $page['title'];
?>

<div class="single-page">
    <h1 class="page-title"><?php echo $page['title']; ?></h1>
    
    <div class="page-content">
        <?php echo $page['content']; ?>
    </div>
</div>

<?php
} else {
    // Page not found
    $pageTitle = 'Page Not Found';
?>

<div class="error-container">
    <h1>Page Not Found</h1>
    <p>The page you are looking for does not exist.</p>
    <a href="<?php echo SITE_URL; ?>" class="btn">Back to Home</a>
</div>

<?php
}
$stmt->close();
$conn->close();
?>