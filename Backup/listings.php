<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>eBay Listings - Tri-State Cards NJ</title>
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
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="listings.php">eBay Listings</a>
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

    <!-- Page Header -->
    <header class="py-5 bg-light">
        <div class="container text-center">
            <h1 class="display-4">Our eBay Listings</h1>
            <p class="lead">Browse our current inventory of sports cards and collectibles</p>
            <a href="https://www.ebay.com/str/tristatecardsnj" class="btn btn-primary" target="_blank">
                <i class="fab fa-ebay me-2"></i>Visit Our eBay Store
            </a>
        </div>
    </header>

    <!-- Search and Filters -->
    <section class="py-4 bg-secondary bg-opacity-10">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <div class="input-group">
                        <input type="text" class="form-control" id="search-input" placeholder="Search listings...">
                        <button class="btn btn-outline-secondary" type="button" id="search-button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-4">
                    <select class="form-select" id="sort-select">
                        <option value="newest">Newest First</option>
                        <option value="price-low">Price: Low to High</option>
                        <option value="price-high">Price: High to Low</option>
                        <option value="popular">Most Popular</option>
                    </select>
                </div>
            </div>
        </div>
    </section>

    <!-- Listings Grid -->
    <section class="py-5">
        <div class="container">
            <div id="listings-container">
                <div class="text-center">
                    <i class="fas fa-spinner fa-spin fa-3x text-primary"></i>
                    <p class="mt-3">Loading listings...</p>
                </div>
            </div>
            
            <!-- Pagination -->
            <nav aria-label="Listings pagination" id="pagination-container" style="display: none;">
                <ul class="pagination justify-content-center mt-4" id="pagination">
                    <!-- Pagination will be dynamically generated -->
                </ul>
            </nav>
        </div>
    </section>

    <!-- Filters Sidebar (mobile) -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="filters-sidebar">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">Filters</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <div class="mb-4">
                <h6>Price Range</h6>
                <div class="input-group input-group-sm">
                    <span class="input-group-text">$</span>
                    <input type="number" class="form-control" id="price-min" placeholder="Min">
                    <span class="input-group-text">-</span>
                    <input type="number" class="form-control" id="price-max" placeholder="Max">
                </div>
            </div>
            
            <div class="mb-4">
                <h6>Categories</h6>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="cat-baseball">
                    <label class="form-check-label" for="cat-baseball">Baseball</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="cat-football">
                    <label class="form-check-label" for="cat-football">Football</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="cat-basketball">
                    <label class="form-check-label" for="cat-basketball">Basketball</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="cat-hockey">
                    <label class="form-check-label" for="cat-hockey">Hockey</label>
                </div>
            </div>
            
            <button class="btn btn-primary w-100" onclick="applyFilters()">Apply Filters</button>
        </div>
    </div>

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
        let currentPage = 1;
        let currentSearch = '';
        let currentSort = 'newest';
        
        document.addEventListener('DOMContentLoaded', function() {
            loadListings();
            
            // Search functionality
            const searchInput = document.getElementById('search-input');
            const searchButton = document.getElementById('search-button');
            
            const debouncedSearch = debounce((query) => {
                currentSearch = query;
                currentPage = 1;
                loadListings();
            }, 300);
            
            searchInput.addEventListener('input', (e) => {
                debouncedSearch(e.target.value);
            });
            
            searchButton.addEventListener('click', () => {
                currentSearch = searchInput.value;
                currentPage = 1;
                loadListings();
            });
            
            // Sort functionality
            document.getElementById('sort-select').addEventListener('change', (e) => {
                currentSort = e.target.value;
                currentPage = 1;
                loadListings();
            });
        });
        
        function loadListings() {
            const container = document.getElementById('listings-container');
            showLoading(container);
            
            const params = new URLSearchParams({
                page: currentPage,
                search: currentSearch,
                sort: currentSort
            });
            
            fetch(`api/listings.php?${params.toString()}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayListings(data.listings);
                        updatePagination(data.pagination);
                    } else {
                        showError('Failed to load listings', container);
                    }
                })
                .catch(error => {
                    showError('Error loading listings', container);
                    console.error(error);
                });
        }
        
        function displayListings(listings) {
            const container = document.getElementById('listings-container');
            
            if (listings.length === 0) {
                container.innerHTML = `
                    <div class="text-center mt-5">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <h4>No listings found</h4>
                        <p class="text-muted">Try adjusting your search criteria</p>
                    </div>
                `;
                return;
            }
            
            container.innerHTML = `
                <div class="listings-grid">
                    ${listings.map(listing => `
                        <div class="listing-card">
                            <img src="${listing.image_url || '/api/placeholder/300/250'}" 
                                 class="listing-image" 
                                 alt="${listing.title}"
                                 onerror="this.src='/api/placeholder/300/250'">
                            <div class="listing-info">
                                <h6 class="listing-title">${listing.title}</h6>
                                <p class="listing-price">${formatCurrency(listing.price)}</p>
                                <a href="${listing.listing_url}" 
                                   class="btn btn-primary btn-sm w-100" 
                                   target="_blank">
                                    View on eBay
                                </a>
                            </div>
                        </div>
                    `).join('')}
                </div>
            `;
        }
        
        function updatePagination(pagination) {
            const container = document.getElementById('pagination-container');
            const paginationList = document.getElementById('pagination');
            
            if (pagination.total_pages <= 1) {
                container.style.display = 'none';
                return;
            }
            
            container.style.display = 'block';
            paginationList.innerHTML = '';
            
            // Previous page
            paginationList.innerHTML += `
                <li class="page-item ${pagination.current_page === 1 ? 'disabled' : ''}">
                    <a class="page-link" href="#" onclick="changePage(${pagination.current_page - 1}); return false;">Previous</a>
                </li>
            `;
            
            // Page numbers
            for (let i = 1; i <= pagination.total_pages; i++) {
                if (i === 1 || i === pagination.total_pages || Math.abs(i - pagination.current_page) <= 2) {
                    paginationList.innerHTML += `
                        <li class="page-item ${i === pagination.current_page ? 'active' : ''}">
                            <a class="page-link" href="#" onclick="changePage(${i}); return false;">${i}</a>
                        </li>
                    `;
                } else if (i === 2 || i === pagination.total_pages - 1) {
                    paginationList.innerHTML += `
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    `;
                }
            }
            
            // Next page
            paginationList.innerHTML += `
                <li class="page-item ${pagination.current_page === pagination.total_pages ? 'disabled' : ''}">
                    <a class="page-link" href="#" onclick="changePage(${pagination.current_page + 1}); return false;">Next</a>
                </li>
            `;
        }
        
        function changePage(page) {
            currentPage = page;
            loadListings();
            window.scrollTo(0, 0);
        }
        
        function applyFilters() {
            // Get filter values
            const minPrice = document.getElementById('price-min').value;
            const maxPrice = document.getElementById('price-max').value;
            
            // Apply filters and reload listings
            // This would be implemented based on your specific filtering requirements
            loadListings();
            
            // Close offcanvas on mobile
            const offcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('filters-sidebar'));
            if (offcanvas) {
                offcanvas.hide();
            }
        }
    </script>
</body>
</html>