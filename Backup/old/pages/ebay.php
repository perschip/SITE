<?php
// pages/ebay.php
$pageTitle = 'eBay Listings';
$pageScripts = ['ebay.js'];
?>

<h1 class="page-title">eBay Listings</h1>

<div class="ebay-filters">
    <div class="filter-group">
        <label for="sort-by">Sort By:</label>
        <select id="sort-by">
            <option value="ending-soonest">Ending Soonest</option>
            <option value="newest">Newest Listings</option>
            <option value="price-asc">Price (Low to High)</option>
            <option value="price-desc">Price (High to Low)</option>
        </select>
    </div>
    
    <div class="filter-group">
        <label for="category">Category:</label>
        <select id="category">
            <option value="all">All Categories</option>
            <option value="baseball">Baseball</option>
            <option value="football">Football</option>
            <option value="basketball">Basketball</option>
            <option value="hockey">Hockey</option>
            <option value="other">Other Sports</option>
        </select>
    </div>
</div>

<div id="ebay-listings" class="full-listings">
    <p>Loading eBay listings...</p>
</div>

<div class="pagination">
    <button id="prev-page" class="btn btn-secondary" disabled>Previous</button>
    <span id="page-info">Page 1</span>
    <button id="next-page" class="btn btn-secondary">Next</button>
</div>

<script>
    // Add pagination and filtering functionality to the ebay.js script
    document.addEventListener('DOMContentLoaded', function() {
        // Filter change event listeners
        document.getElementById('sort-by').addEventListener('change', updateListings);
        document.getElementById('category').addEventListener('change', updateListings);
        
        // Pagination event listeners
        document.getElementById('prev-page').addEventListener('click', function() {
            if (currentPage > 1) {
                currentPage--;
                updateListings();
            }
        });
        
        document.getElementById('next-page').addEventListener('click', function() {
            currentPage++;
            updateListings();
        });
        
        // Variables for pagination
        let currentPage = 1;
        
        // Function to update listings based on filters and pagination
        function updateListings() {
            const sortBy = document.getElementById('sort-by').value;
            const category = document.getElementById('category').value;
            
            // Update API call in ebay.js to include these parameters
            // This would require modifying the original ebay.js fetch URL to include
            // sort, category, and page parameters
            
            // Update page info display
            document.getElementById('page-info').textContent = `Page ${currentPage}`;
            
            // Disable/enable prev button based on current page
            document.getElementById('prev-page').disabled = (currentPage === 1);
        }
    });
</script>