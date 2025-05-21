<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tri-State Cards NJ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body class="theme-light">
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
                        <a class="nav-link active" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="listings.php">eBay Listings</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="blog.php">Blog</a>
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

    <!-- Hero Section -->
    <header class="hero-section text-white text-center py-5">
        <div class="container">
            <h1 class="display-4">Welcome to Tri-State Cards NJ</h1>
            <p class="lead">Your premier destination for sports cards and collectibles</p>
            <a href="listings.php" class="btn btn-primary btn-lg me-3">Shop eBay Store</a>
            <a href="stream.php" class="btn btn-outline-light btn-lg">Watch Live Stream</a>
        </div>
    </header>

    <!-- Current Stream Status -->
    <section class="py-4 bg-secondary text-white">
        <div class="container text-center">
            <div id="stream-status" class="h5">
                <i class="fas fa-circle text-danger me-2"></i>
                Stream Status: <span id="status-text">Loading...</span>
            </div>
        </div>
    </section>

    <!-- Featured Listings -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-4">Featured eBay Listings</h2>
            <div class="row" id="featured-listings">
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <img src="/api/placeholder/300/200" class="card-img-top" alt="Card placeholder">
                        <div class="card-body">
                            <h5 class="card-title">Loading...</h5>
                            <p class="card-text">$0.00</p>
                            <a href="#" class="btn btn-primary">View on eBay</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center mt-4">
                <a href="listings.php" class="btn btn-outline-primary">View All Listings</a>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h2>About Tri-State Cards</h2>
                    <p>We specialize in sports cards, trading cards, and collectibles. With years of experience in the industry, we bring you the best selection and prices.</p>
                    <p>Follow us on Whatnot for live card breaks and special deals!</p>
                </div>
                <div class="col-md-6">
                    <img src="/api/placeholder/500/300" class="img-fluid rounded" alt="Store image">
                </div>
            </div>
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
    <script>
        // Load featured listings
        document.addEventListener('DOMContentLoaded', function() {
            loadFeaturedListings();
            loadStreamStatus();
        });

        function loadFeaturedListings() {
            fetch('api/listings.php?featured=true')
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('featured-listings');
                    container.innerHTML = '';
                    
                    data.listings.slice(0, 3).forEach(listing => {
                        container.innerHTML += `
                            <div class="col-md-4 mb-4">
                                <div class="card h-100">
                                    <img src="${listing.image_url || '/api/placeholder/300/200'}" class="card-img-top" alt="${listing.title}">
                                    <div class="card-body">
                                        <h5 class="card-title">${listing.title}</h5>
                                        <p class="card-text">$${listing.price}</p>
                                        <a href="${listing.listing_url}" class="btn btn-primary" target="_blank">View on eBay</a>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                })
                .catch(error => console.error('Error loading listings:', error));
        }

       function loadStreamStatus() {
            fetch('api/stream-status.php')
                .then(response => response.json())
                .then(data => {
                    const statusText = document.getElementById('status-text');
                    const streamStatus = document.getElementById('stream-status');
                    
                    if (data.is_live) {
                        statusText.textContent = 'Live Now!';
                        streamStatus.innerHTML = '<i class="fas fa-circle text-success me-2"></i>Stream Status: <span id="status-text">Live Now!</span>';
                    } else {
                        let nextStreamText = 'TBD';
                        
                        if (data.next_stream && data.next_stream.title) {
                            // Format the date if available
                            const date = new Date(data.next_stream.time);
                            const formattedDate = date.toLocaleDateString('en-US', {
                                weekday: 'short',
                                month: 'short',
                                day: 'numeric',
                                hour: 'numeric',
                                minute: '2-digit'
                            });
                            nextStreamText = `${data.next_stream.title} - ${formattedDate}`;
                        }
                        
                        statusText.textContent = `Offline - Next stream: ${nextStreamText}`;
                        streamStatus.innerHTML = '<i class="fas fa-circle text-danger me-2"></i>Stream Status: <span id="status-text">Offline - Next stream: ' + nextStreamText + '</span>';
                    }
                })
                .catch(error => {
                    console.error('Error loading stream status:', error);
                    // Set a default status on error
                    const statusText = document.getElementById('status-text');
                    const streamStatus = document.getElementById('stream-status');
                    statusText.textContent = 'Status Unavailable';
                    streamStatus.innerHTML = '<i class="fas fa-circle text-warning me-2"></i>Stream Status: <span id="status-text">Status Unavailable</span>';
                });
        }
    </script>
</body>
</html>