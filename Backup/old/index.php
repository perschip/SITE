<?php
// index.php
$page_title = "Tristate Cards";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="images/logo.png" alt="Tristate Cards" height="30">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#ebay-listings">eBay Listings</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="https://www.ebay.com/str/tristatecardsnj" target="_blank">eBay Store</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="https://www.whatnot.com/user/tscardbreaks" target="_blank">Whatnot</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="bg-primary text-white text-center py-5">
        <div class="container">
            <h1>Welcome to Tristate Cards</h1>
            <p class="lead">Your premier source for sports card breaks, collectibles, and more</p>
        </div>
    </header>

    <!-- Whatnot Status Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-4">Whatnot Live Status</h2>
            <div id="whatnot-status" class="card shadow mx-auto" style="max-width: 600px;">
                <div class="card-body text-center p-4">
                    <div class="spinner-border text-primary mb-3" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="card-text">Checking if we're live on Whatnot...</p>
                </div>
            </div>
        </div>
    </section>

    <!-- eBay Listings Section -->
    <section class="py-5" id="ebay-listings-section">
        <div class="container">
            <h2 class="text-center mb-4">Featured eBay Listings</h2>
            <div id="ebay-listings" class="row g-4">
                <div class="col-12 text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p>Loading eBay listings...</p>
                </div>
            </div>
            <div class="text-center mt-4">
                <a href="https://www.ebay.com/str/tristatecardsnj" class="btn btn-primary" target="_blank">View All Listings</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>About Tristate Cards</h5>
                    <p>Your premier source for sports card breaks, collectibles, and more. Based in New Jersey, serving collectors nationwide.</p>
                </div>
                <div class="col-md-6">
                    <h5>Connect With Us</h5>
                    <div class="d-flex gap-3">
                        <a href="https://www.whatnot.com/user/tscardbreaks" class="text-white" target="_blank"><i class="fas fa-shopping-bag fa-lg"></i></a>
                        <a href="https://www.ebay.com/str/tristatecardsnj" class="text-white" target="_blank"><i class="fas fa-store fa-lg"></i></a>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12 text-center">
                    <p class="mb-0">&copy; <?php echo date('Y'); ?> Tristate Cards. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery (for AJAX) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Custom JS -->
    <script src="js/main.js"></script>
</body>
</html>