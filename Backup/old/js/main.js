// js/main.js
$(document).ready(function() {
    // Check Whatnot status - with timeout to prevent endless loading
    function checkWhatnotStatus() {
        const statusContainer = $('#whatnot-status');
        
        // Set a timeout to handle failed API calls
        const whatnotTimeout = setTimeout(function() {
            console.log("Whatnot API timeout - showing fallback content");
            renderWhatnotFallback();
        }, 5000); // 5 seconds timeout
        
        $.ajax({
            url: 'api/whatnot-status.php',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                clearTimeout(whatnotTimeout); // Clear the timeout
                renderWhatnotStatus(data);
            },
            error: function(xhr, status, error) {
                clearTimeout(whatnotTimeout); // Clear the timeout
                console.error('Error checking Whatnot status:', error);
                renderWhatnotFallback();
            }
        });
    }
    
    // Render Whatnot status
    function renderWhatnotStatus(data) {
        const statusContainer = $('#whatnot-status');
        let html = '';
        
        if (data.isLive) {
            html = `
                <div class="card-body text-center p-4">
                    <div class="live-badge">LIVE NOW</div>
                    <h3 class="card-title">${data.streamTitle}</h3>
                    <p class="card-text">${data.viewerCount} viewers</p>
                    <a href="https://www.whatnot.com/user/${data.username}" class="btn btn-danger" target="_blank">Watch Now</a>
                </div>
            `;
            statusContainer.addClass('border-danger');
        } else {
            html = `
                <div class="card-body text-center p-4">
                    <div class="offline-badge">OFFLINE</div>
                    <p class="card-text">We're not currently live. Follow us on Whatnot for notifications.</p>
                    <a href="https://www.whatnot.com/user/tscardbreaks" class="btn btn-primary" target="_blank">Follow on Whatnot</a>
                </div>
            `;
        }
        
        statusContainer.html(html);
    }
    
    // Fallback for Whatnot
    function renderWhatnotFallback() {
        const statusContainer = $('#whatnot-status');
        statusContainer.html(`
            <div class="card-body text-center p-4">
                <div class="offline-badge">OFFLINE</div>
                <p class="card-text">We're not currently live. Follow us on Whatnot for notifications.</p>
                <a href="https://www.whatnot.com/user/tscardbreaks" class="btn btn-primary" target="_blank">Follow on Whatnot</a>
            </div>
        `);
    }
    
    // Fetch eBay listings with timeout
    function fetchEbayListings() {
        const listingsContainer = $('#ebay-listings');
        
        // Set a timeout to handle failed API calls
        const ebayTimeout = setTimeout(function() {
            console.log("eBay API timeout - showing fallback content");
            renderEbayFallback();
        }, 5000); // 5 seconds timeout
        
        $.ajax({
            url: 'api/ebay-listings.php',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                clearTimeout(ebayTimeout); // Clear the timeout
                renderEbayListings(data);
            },
            error: function(xhr, status, error) {
                clearTimeout(ebayTimeout); // Clear the timeout
                console.error('Error fetching eBay listings:', error);
                renderEbayFallback();
            }
        });
    }
    
    // Render eBay listings
    function renderEbayListings(data) {
        const listingsContainer = $('#ebay-listings');
        listingsContainer.empty();
        
        if (data.error) {
            renderEbayFallback();
            return;
        }
        
        if (!Array.isArray(data) || data.length === 0) {
            renderEbayFallback();
            return;
        }
        
        // Display listings
        $.each(data, function(index, listing) {
            const listingCard = `
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="card listing-card shadow h-100">
                        <img src="${listing.image || 'images/placeholder.jpg'}" class="card-img-top" alt="${listing.title}" onerror="this.src='images/placeholder.jpg'">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">${listing.title}</h5>
                            <p class="card-text text-primary fw-bold">$${listing.price}</p>
                            <p class="card-text"><small class="text-muted">${listing.bids} bids | ${listing.timeLeft}</small></p>
                            <a href="${listing.viewItemURL}" class="btn btn-outline-primary mt-auto" target="_blank">View Listing</a>
                        </div>
                    </div>
                </div>
            `;
            
            listingsContainer.append(listingCard);
        });
    }
    
    // Fallback for eBay listings
    function renderEbayFallback() {
        const listingsContainer = $('#ebay-listings');
        listingsContainer.empty();
        
        // Create dummy listings
        const dummyListings = [
            {
                title: "2023 Panini Prizm Basketball Card - Rookie Edition",
                price: "49.99",
                bids: "12",
                timeLeft: "2 days 5 hours",
                viewItemURL: "https://www.ebay.com/str/tristatecardsnj"
            },
            {
                title: "2023 Topps Chrome Baseball Card Pack - Factory Sealed",
                price: "29.99",
                bids: "7",
                timeLeft: "1 day 12 hours",
                viewItemURL: "https://www.ebay.com/str/tristatecardsnj"
            },
            {
                title: "Patrick Mahomes Signed Rookie Card - PSA Graded",
                price: "199.99",
                bids: "22",
                timeLeft: "3 days 4 hours",
                viewItemURL: "https://www.ebay.com/str/tristatecardsnj"
            },
            {
                title: "2023 NFL Panini Mosaic Football Card Box Set",
                price: "149.99",
                bids: "15",
                timeLeft: "2 days 7 hours",
                viewItemURL: "https://www.ebay.com/str/tristatecardsnj"
            }
        ];
        
        // Display fallback listings
        $.each(dummyListings, function(index, listing) {
            const listingCard = `
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="card listing-card shadow h-100">
                        <img src="images/placeholder.jpg" class="card-img-top" alt="${listing.title}">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">${listing.title}</h5>
                            <p class="card-text text-primary fw-bold">$${listing.price}</p>
                            <p class="card-text"><small class="text-muted">${listing.bids} bids | ${listing.timeLeft}</small></p>
                            <a href="${listing.viewItemURL}" class="btn btn-outline-primary mt-auto" target="_blank">View All Listings</a>
                        </div>
                    </div>
                </div>
            `;
            
            listingsContainer.append(listingCard);
        });
    }

    // Initial calls
    checkWhatnotStatus();
    fetchEbayListings();
    
    // Refresh data periodically
    setInterval(checkWhatnotStatus, 60000); // Check Whatnot status every minute
    setInterval(fetchEbayListings, 300000); // Refresh eBay listings every 5 minutes
});