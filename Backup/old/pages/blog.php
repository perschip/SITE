<?php
// pages/blog.php
$pageTitle = 'Blog';

// Pagination setup
$postsPerPage = 9;
$page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
$offset = ($page - 1) * $postsPerPage;

// Connect to database
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get total posts count
$countSql = "SELECT COUNT(*) AS total FROM posts";
$countResult = $conn->query($countSql);
$totalPosts = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalPosts / $postsPerPage);

// Get posts for current page
$sql = "SELECT id, title, slug, featured_image, LEFT(content, 200) AS excerpt, created_at 
        FROM posts 
        ORDER BY created_at DESC 
        LIMIT $offset, $postsPerPage";

$result = $conn->query($sql);
?>

<h1 class="page-title">Blog</h1>

<?php if ($result->num_rows > 0): ?>
    <div class="blog-grid">
        <?php while ($row = $result->fetch_assoc()): 
            $image = $row['featured_image'] ? SITE_URL . '/uploads/' . $row['featured_image'] : SITE_URL . '/images/default-post.jpg';
            $date = date('F j, Y', strtotime($row['created_at']));
            $excerpt = strip_tags($row['excerpt']) . '...';
        ?>
            <div class="blog-card">
                <div class="blog-image">
                    <img src="<?php echo $image; ?>" alt="<?php echo $row['title']; ?>">
                </div>
                <div class="blog-content">
                    <h2><a href="<?php echo SITE_URL; ?>/blog/<?php echo $row['slug']; ?>"><?php echo $row['title']; ?></a></h2>
                    <span class="blog-date"><?php echo $date; ?></span>
                    <p><?php echo $excerpt; ?></p>
                    <a href="<?php echo SITE_URL; ?>/blog/<?php echo $row['slug']; ?>" class="read-more">Read More</a>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
    
    <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="<?php echo SITE_URL; ?>/blog?p=<?php echo $page - 1; ?>" class="btn btn-secondary">Previous</a>
            <?php endif; ?>
            
            <span class="page-numbers">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <?php if ($i == $page): ?>
                        <span class="current-page"><?php echo $i; ?></span>
                    <?php else: ?>
                        <a href="<?php echo SITE_URL; ?>/blog?p=<?php echo $i; ?>"><?php echo $i; ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
            </span>
            
            <?php if ($page < $totalPages): ?>
                <a href="<?php echo SITE_URL; ?>/blog?p=<?php echo $page + 1; ?>" class="btn btn-secondary">Next</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
<?php else: ?>
    <p>No blog posts found.</p>
<?php endif; ?>

<?php $conn->close(); ?>