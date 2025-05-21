<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog - Tri-State Cards NJ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body class="theme-light">
    <?php
    require_once 'config.php';
    
    // Get blog posts
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $perPage = 9;
    $offset = ($page - 1) * $perPage;
    
    // Get total count
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM blog_posts WHERE is_published = TRUE");
    $total = $stmt->fetch()['count'];
    
    // Get posts
    $stmt = $pdo->prepare("
        SELECT * FROM blog_posts 
        WHERE is_published = TRUE 
        ORDER BY created_at DESC 
        LIMIT :limit OFFSET :offset
    ");
    $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $posts = $stmt->fetchAll();
    ?>
    
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="images/logo.png" alt="Tri-State Cards NJ" height="40">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="listings.php">eBay Listings</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="blog.php">Blog</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="stream.php">Whatnot Stream</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <button class="btn btn-link nav-link" id="theme-toggle">
                            <i class="fas fa-moon" id="theme-icon"></i>
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <header class="py-5 bg-light">
        <div class="container text-center">
            <h1 class="display-4">Our Blog</h1>
            <p class="lead">Stay updated with the latest news, trends, and insights from Tri-State Cards</p>
        </div>
    </header>

    <!-- Blog Posts -->
    <section class="py-5">
        <div class="container">
            <?php if (empty($posts)): ?>
                <div class="text-center">
                    <p class="lead">No blog posts available at the moment.</p>
                    <p>Check back soon for updates!</p>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($posts as $post): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 blog-card">
                                <?php if ($post['featured_image']): ?>
                                    <img src="<?php echo htmlspecialchars($post['featured_image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($post['title']); ?>">
                                <?php else: ?>
                                    <img src="/api/placeholder/400/250" class="card-img-top" alt="Blog post">
                                <?php endif; ?>
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($post['title']); ?></h5>
                                    <p class="card-text">
                                        <?php echo substr(strip_tags($post['content']), 0, 150) . '...'; ?>
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted"><?php echo date('F j, Y', strtotime($post['created_at'])); ?></small>
                                        <a href="blog-post.php?slug=<?php echo urlencode($post['slug']); ?>" class="btn btn-primary btn-sm">Read More</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Pagination -->
                <?php
                $totalPages = ceil($total / $perPage);
                if ($totalPages > 1):
                ?>
                    <nav aria-label="Blog pagination">
                        <ul class="pagination justify-content-center">
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $page - 1; ?>">Previous</a>
                                </li>
                            <?php endif; ?>
                            
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($page < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $page + 1; ?>">Next</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white pt-5 pb-4">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5>Tri-State Cards NJ</h5>
                    <p>Your premier destination for sports cards and collectibles.</p>
                </div>
                <div class="col-md-4 mb-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="listings.php" class="text-light">eBay Listings</a></li>
                        <li><a href="blog.php" class="text-light">Blog</a></li>
                        <li><a href="stream.php" class="text-light">Live Stream</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-4">
                    <h5>Follow Us</h5>
                    <div class="d-flex gap-3">
                        <a href="https://www.ebay.com/str/tristatecardsnj" class="text-light fs-4"><i class="fab fa-ebay"></i></a>
                        <a href="https://whatnot.com/user/tscardbreaks" class="text-light fs-4"><i class="fas fa-video"></i></a>
                    </div>
                </div>
            </div>
            <hr>
            <div class="text-center">
                <p class="mb-0">&copy; <?php echo date('Y'); ?> Tri-State Cards NJ. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/app.js"></script>
</body>
</html>