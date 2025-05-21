<?php
// pages/home.php
$pageTitle = 'Home';
$pageScripts = ['whatnot.js', 'ebay.js'];
?>

<section class="hero">
    <h1>Welcome to Tristate Cards</h1>
    <p>Your premier destination for sports card breaks, collectibles, and more</p>
</section>

<section class="whatnot-container">
    <h2 class="section-title">Whatnot Live Status</h2>
    <div id="whatnot-status">
        <p>Loading Whatnot status...</p>
    </div>
</section>

<section class="ebay-container">
    <h2 class="section-title">Featured eBay Listings</h2>
    <div id="ebay-listings">
        <p>Loading eBay listings...</p>
    </div>
<div class="view-all">
    <a href="<?php echo SITE_URL; ?>/index.php?page=ebay" class="btn">View All Listings</a>
</div>
</section>

<section class="latest-posts">
    <h2 class="section-title">Latest Blog Posts</h2>
    
    <?php
    // Connect to database
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Get latest 3 posts
    $sql = "SELECT id, title, slug, featured_image, LEFT(content, 200) AS excerpt, created_at 
            FROM posts 
            ORDER BY created_at DESC 
            LIMIT 3";
    
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        echo '<div class="posts-grid">';
        
        while ($row = $result->fetch_assoc()) {
            $image = $row['featured_image'] ? SITE_URL . '/uploads/' . $row['featured_image'] : SITE_URL . '/images/default-post.jpg';
            $date = date('F j, Y', strtotime($row['created_at']));
            $excerpt = strip_tags($row['excerpt']) . '...';
            
            echo '
            <div class="post-card">
                <div class="post-image">
                    <img src="' . $image . '" alt="' . $row['title'] . '">
                </div>
                <div class="post-content">
                    <h3><a href="' . SITE_URL . '/blog/' . $row['slug'] . '">' . $row['title'] . '</a></h3>
                    <span class="post-date">' . $date . '</span>
                    <p>' . $excerpt . '</p>
                    <a href="' . SITE_URL . '/blog/' . $row['slug'] . '" class="read-more">Read More</a>
                </div>
            </div>';
        }
        
        echo '</div>';
    } else {
        echo '<p>No blog posts found.</p>';
    }
    
    $conn->close();
    ?>
    
    <div class="view-all">
        <a href="<?php echo SITE_URL; ?>/index.php?page=blog" class="btn">View All Posts</a>
    </div>
</section>