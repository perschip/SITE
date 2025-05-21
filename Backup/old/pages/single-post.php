<?php
// pages/single-post.php
$slug = $_GET['slug'] ?? '';

// Connect to database
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get post by slug
$stmt = $conn->prepare("SELECT id, title, content, featured_image, created_at FROM posts WHERE slug = ?");
$stmt->bind_param("s", $slug);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $post = $result->fetch_assoc();
    $pageTitle = $post['title'];
    $image = $post['featured_image'] ? SITE_URL . '/uploads/' . $post['featured_image'] : '';
    $date = date('F j, Y', strtotime($post['created_at']));
?>

<div class="single-post">
    <h1 class="post-title"><?php echo $post['title']; ?></h1>
    <div class="post-meta">
        <span class="post-date">Posted on <?php echo $date; ?></span>
    </div>
    
    <?php if ($image): ?>
    <div class="post-featured-image">
        <img src="<?php echo $image; ?>" alt="<?php echo $post['title']; ?>">
    </div>
    <?php endif; ?>
    
    <div class="post-content">
        <?php echo $post['content']; ?>
    </div>
    
    <div class="post-navigation">
        <a href="<?php echo SITE_URL; ?>/blog" class="btn btn-secondary">Back to Blog</a>
    </div>
</div>

<?php
} else {
    // Post not found
    $pageTitle = 'Post Not Found';
?>

<div class="error-container">
    <h1>Post Not Found</h1>
    <p>The blog post you are looking for does not exist.</p>
    <a href="<?php echo SITE_URL; ?>/blog" class="btn">Back to Blog</a>
</div>

<?php
}
$stmt->close();
$conn->close();
?>