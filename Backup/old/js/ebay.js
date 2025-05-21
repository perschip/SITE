// js/ebay.js
document.addEventListener('DOMContentLoaded', function() {
    // Function to fetch eBay listings
    function fetchEbayListings() {
        const apiUrl = 'api/ebay-listings.php';
        const listingsContainer = document.getElementById('ebay-listings');
        
        if (!listingsContainer) return;
        
        // Show loading indicator
        listingsContainer.innerHTML = '<div class="loading">Loading eBay listings... <div class="spinner"></div></div>';
        
        fetch(apiUrl)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Network response was not ok (${response.status})`);
                }
                return response.json();
            })
            .then(data => {
                listingsContainer.innerHTML = '';
                
                if (data.error) {
                    listingsContainer.innerHTML = `<p class="error-message">Error: ${data.error}</p>`;
                    return;
                }
                
                if (data.length === 0) {
                    listingsContainer.innerHTML = '<p>No active listings found. Please check back later.</p>';
                    return;
                }
                
                // Create a card for each listing
                data.forEach(listing => {
                    const listingCard = document.createElement('div');
                    listingCard.className = 'listing-card';
                    
                    // Handle missing or broken images
                    const imageUrl = listing.image || '../images/placeholder.jpg';
                    
                    listingCard.innerHTML = `
                        <img src="${imageUrl}" alt="${listing.title}" onerror="this.src='../images/placeholder.jpg'">
                        <h3>${listing.title}</h3>
                        <p class="price">$${listing.price}</p>
                        <p>${listing.bids} bids | ${listing.timeLeft}</p>
                        <a href="${listing.viewItemURL}" target="_blank" class="btn">View Listing</a>
                    `;
                    
                    listingsContainer.appendChild(listingCard);
                });
            })
            .catch(error => {
                console.error('Error fetching eBay listings:', error);
                if (listingsContainer) {
                    listingsContainer.innerHTML = `
                        <p class="error-message">Error loading listings: ${error.message}</p>
                        <p>Please try again later.</p>
                    `;
                }
            });
    }
    
    // Initial fetch
    fetchEbayListings();
    
    // Refresh listings every 5 minutes
    setInterval(fetchEbayListings, 300000);
});