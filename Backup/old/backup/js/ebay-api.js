// Load eBay configuration from localStorage
function loadEbayConfig() {
    return {
        SELLER_ID: localStorage.getItem('ebay_seller_id') || '',
        APP_ID: localStorage.getItem('ebay_app_id') || '',
        DEV_ID: localStorage.getItem('ebay_dev_id') || '',
        CERT_ID: localStorage.getItem('ebay_cert_id') || '',
        USE_SANDBOX: localStorage.getItem('ebay_use_sandbox') === 'false'
    };
}

// Function to check if eBay configuration is set up
function isEbayConfigured() {
    const config = loadEbayConfig();
    return config.SELLER_ID && config.APP_ID && config.CERT_ID;
}

// Function to get eBay authorization URL
function getEbayAuthUrl() {
    const config = loadEbayConfig();
    
    // Use the current domain as the redirect URI base
    const baseUrl = window.location.origin;
    
    // Use either Sandbox or Production URL based on configuration
    const authUrl = config.USE_SANDBOX ? 
        'https://auth.sandbox.ebay.com/oauth2/authorize' : 
        'https://auth.ebay.com/oauth2/authorize';
    
    const params = new URLSearchParams({
        client_id: config.APP_ID,
        response_type: 'code',
        redirect_uri: baseUrl + '/auth-success.html',
        // Use minimal scope to start with
        scope: 'https://api.ebay.com/oauth/api_scope',
    });
    
    // Add additional required parameters
    params.append('prompt', 'login');
    
    return `${authUrl}?${params.toString()}`;
}

// Function to start the eBay OAuth process
function startEbayAuth() {
    if (!isEbayConfigured()) {
        alert('Please set up your eBay configuration first on the setup page.');
        window.location.href = 'setup.html';
        return;
    }
    
    // Save the current page to return to after auth
    localStorage.setItem('ebay_auth_redirect', window.location.href);
    
    // Redirect to eBay auth page
    window.location.href = getEbayAuthUrl();
}

// Function to handle the eBay OAuth callback
async function handleEbayCallback() {
    const urlParams = new URLSearchParams(window.location.search);
    const authCode = urlParams.get('code');
    const error = urlParams.get('error');
    
    // If we're on the declined page, show the error
    if (window.location.pathname.includes('auth-declined.html')) {
        if (document.getElementById('auth-message')) {
            document.getElementById('auth-message').textContent = 'Authentication was declined or an error occurred.';
        }
        return;
    }
    
    // If we're on the success page with no code, something went wrong
    if (window.location.pathname.includes('auth-success.html') && !authCode) {
        window.location.href = 'auth-declined.html';
        return;
    }
    
    if (error) {
        console.error('eBay authentication error:', error);
        window.location.href = 'auth-declined.html';
        return;
    }
    
    if (!authCode) {
        console.error('No authorization code received from eBay');
        window.location.href = 'auth-declined.html';
        return;
    }
    
    // If we have a code on the success page, exchange it for tokens
    if (window.location.pathname.includes('auth-success.html')) {
        const success = await getEbayTokens(authCode);
        if (!success) {
            window.location.href = 'auth-declined.html';
        }
    }
}

// Function to exchange auth code for tokens
async function getEbayTokens(authCode) {
    const config = loadEbayConfig();
    const redirectUri = window.location.origin + '/auth-success.html';
    
    // Use either Sandbox or Production token URL
    const tokenUrl = config.USE_SANDBOX ? 
        'https://api.sandbox.ebay.com/identity/v1/oauth2/token' : 
        'https://api.ebay.com/identity/v1/oauth2/token';
    
    try {
        const response = await fetch(tokenUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'Authorization': 'Basic ' + btoa(`${config.APP_ID}:${config.CERT_ID}`)
            },
            body: new URLSearchParams({
                grant_type: 'authorization_code',
                code: authCode,
                redirect_uri: redirectUri
            })
        });
        
        const tokenData = await response.json();
        
        if (tokenData.access_token) {
            // Store tokens in localStorage
            localStorage.setItem('ebay_access_token', tokenData.access_token);
            localStorage.setItem('ebay_refresh_token', tokenData.refresh_token);
            localStorage.setItem('ebay_token_expiry', Date.now() + (tokenData.expires_in * 1000));
            
            // Success! Redirect will happen automatically from the success page
            return true;
        } else {
            console.error('Failed to get eBay tokens:', tokenData);
            return false;
        }
    } catch (error) {
        console.error('Error exchanging auth code for tokens:', error);
        return false;
    }
}

// Function to refresh the access token
async function refreshEbayToken() {
    const config = loadEbayConfig();
    const refreshToken = localStorage.getItem('ebay_refresh_token');
    
    if (!refreshToken) {
        console.error('No refresh token available');
        return false;
    }
    
    // Use either Sandbox or Production token URL
    const tokenUrl = config.USE_SANDBOX ? 
        'https://api.sandbox.ebay.com/identity/v1/oauth2/token' : 
        'https://api.ebay.com/identity/v1/oauth2/token';
    
    try {
        const response = await fetch(tokenUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'Authorization': 'Basic ' + btoa(`${config.APP_ID}:${config.CERT_ID}`)
            },
            body: new URLSearchParams({
                grant_type: 'refresh_token',
                refresh_token: refreshToken
            })
        });
        
        const tokenData = await response.json();
        
        if (tokenData.access_token) {
            localStorage.setItem('ebay_access_token', tokenData.access_token);
            localStorage.setItem('ebay_token_expiry', Date.now() + (tokenData.expires_in * 1000));
            return true;
        } else {
            console.error('Failed to refresh eBay token:', tokenData);
            return false;
        }
    } catch (error) {
        console.error('Error refreshing eBay token:', error);
        return false;
    }
}

// Function to check if token is valid and refresh if needed
async function ensureValidToken() {
    const tokenExpiry = localStorage.getItem('ebay_token_expiry');
    
    // If token is expired or will expire in the next 5 minutes
    if (!tokenExpiry || Date.now() > (parseInt(tokenExpiry) - 300000)) {
        return await refreshEbayToken();
    }
    
    return true;
}

// Function to fetch eBay listings
async function fetchEbayListings() {
    const listingsContainer = document.getElementById('ebay-listings');
    
    // Show loading state
    listingsContainer.innerHTML = '<div class="loading">Loading listings...</div>';
    
    // Check if eBay is configured
    if (!isEbayConfigured()) {
        listingsContainer.innerHTML = `
            <div class="setup-notice">
                <p>eBay API is not configured yet.</p>
                <a href="setup.html" class="view-button">Set Up eBay Integration</a>
            </div>
        `;
        return;
    }
    
    // Check if we have a valid token
    const hasToken = localStorage.getItem('ebay_access_token');
    if (!hasToken) {
        listingsContainer.innerHTML = `
            <div class="setup-notice">
                <p>eBay authentication required.</p>
                <button id="auth-button" class="view-button">Authenticate with eBay</button>
            </div>
        `;
        document.getElementById('auth-button').addEventListener('click', startEbayAuth);
        return;
    }
    
    // Ensure token is valid
    const tokenValid = await ensureValidToken();
    if (!tokenValid) {
        listingsContainer.innerHTML = `
            <div class="setup-notice">
                <p>eBay authentication expired.</p>
                <button id="auth-button" class="view-button">Re-authenticate with eBay</button>
            </div>
        `;
        document.getElementById('auth-button').addEventListener('click', startEbayAuth);
        return;
    }
    
    try {
        // In production, this would use the real eBay API
        // For now, use mock data until you're ready to switch to the real API
        const listings = await getMockListings();
        
        // Once you're ready to use the real API, comment out the line above and uncomment this:
        // const listings = await getRealEbayListings();
        
        renderListings(listings);
    } catch (error) {
        showError(`Error fetching eBay listings: ${error.message}`, listingsContainer);
        console.error('Error fetching eBay listings:', error);
    }
}

// Function to render listings in the DOM
function renderListings(listings) {
    const listingsContainer = document.getElementById('ebay-listings');
    
    // Clear the container
    listingsContainer.innerHTML = '';
    
    // If no listings, show a message
    if (listings.length === 0) {
        listingsContainer.innerHTML = '<div class="no-listings">No listings found</div>';
        return;
    }
    
    // Create a card for each listing
    listings.forEach(listing => {
        const card = createElement('div', { className: 'listing-card' }, [
            createElement('img', {
                src: listing.image,
                alt: listing.title,
                className: 'listing-image'
            }),
            createElement('div', { className: 'listing-info' }, [
                createElement('h3', { 
                    className: 'listing-title',
                    textContent: listing.title
                }),
                createElement('p', { 
                    className: 'listing-price',
                    textContent: `$${listing.price.toFixed(2)}`
                }),
                createElement('a', {
                    href: listing.url,
                    target: '_blank',
                    className: 'view-button',
                    textContent: 'View on eBay'
                })
            ])
        ]);
        
        listingsContainer.appendChild(card);
    });
}

// Mock function to get placeholder listings for testing
async function getMockListings() {
    // Simulate API delay
    await new Promise(resolve => setTimeout(resolve, 500));
    
    return [
        {
            id: '1',
            title: '2023 Topps Chrome Jasson Dominguez Rookie Card PSA 10',
            price: 89.99,
            image: 'https://via.placeholder.com/250x200',
            url: 'https://www.ebay.com/itm/123456789'
        },
        {
            id: '2',
            title: 'Patrick Mahomes Prizm Silver 2022 Kansas City Chiefs',
            price: 45.00,
            image: 'https://via.placeholder.com/250x200',
            url: 'https://www.ebay.com/itm/123456790'
        },
        {
            id: '3',
            title: 'LeBron James Panini Prizm Basketball Card Lakers',
            price: 125.00,
            image: 'https://via.placeholder.com/250x200',
            url: 'https://www.ebay.com/itm/123456791'
        },
        {
            id: '4',
            title: 'Mike Trout Topps Chrome Refractor Angels MLB',
            price: 67.50,
            image: 'https://via.placeholder.com/250x200',
            url: 'https://www.ebay.com/itm/123456792'
        },
        {
            id: '5',
            title: 'Zion Williamson Rookie Card Panini Prizm PSA 9',
            price: 110.00,
            image: 'https://via.placeholder.com/250x200',
            url: 'https://www.ebay.com/itm/123456793'
        },
        {
            id: '6',
            title: 'Aaron Judge Auto Topps Chrome Yankees',
            price: 250.00,
            image: 'https://via.placeholder.com/250x200',
            url: 'https://www.ebay.com/itm/123456794'
        }
    ];
}

// Real eBay API integration
// This function can replace getMockListings() when you're ready to go live
async function getRealEbayListings() {
    const config = loadEbayConfig();
    const accessToken = localStorage.getItem('ebay_access_token');
    
    // Use the appropriate API URL based on sandbox setting
    const apiBase = config.USE_SANDBOX 
        ? 'https://api.sandbox.ebay.com' 
        : 'https://api.ebay.com';
    
    const response = await fetch(`${apiBase}/buy/browse/v1/item_summary/search?q=seller:${config.SELLER_ID}&limit=50`, {
        headers: {
            'Authorization': `Bearer ${accessToken}`,
            'X-EBAY-C-MARKETPLACE-ID': 'EBAY_US',
            'Content-Type': 'application/json'
        }
    });
    
    if (!response.ok) {
        throw new Error(`eBay API error: ${response.status} ${response.statusText}`);
    }
    
    const data = await response.json();
    
    // Format the data to match our expected structure
    return data.itemSummaries.map(item => ({
        id: item.itemId,
        title: item.title,
        price: parseFloat(item.price.value),
        image: item.thumbnailImages?.[0]?.imageUrl || 'https://via.placeholder.com/250x200',
        url: item.itemWebUrl
    }));
}

// Optional: Get specific listing details
async function getEbayListingDetails(itemId) {
    const config = loadEbayConfig();
    const accessToken = localStorage.getItem('ebay_access_token');
    
    // Use the appropriate API URL based on sandbox setting
    const apiBase = config.USE_SANDBOX 
        ? 'https://api.sandbox.ebay.com' 
        : 'https://api.ebay.com';
    
    const response = await fetch(`${apiBase}/buy/browse/v1/item/${itemId}`, {
        headers: {
            'Authorization': `Bearer ${accessToken}`,
            'X-EBAY-C-MARKETPLACE-ID': 'EBAY_US',
            'Content-Type': 'application/json'
        }
    });
    
    if (!response.ok) {
        throw new Error(`eBay API error: ${response.status} ${response.statusText}`);
    }
    
    return await response.json();
}

// Optional: Get eBay seller information
async function getEbaySellerInfo() {
    const config = loadEbayConfig();
    const accessToken = localStorage.getItem('ebay_access_token');
    
    // Use the appropriate API URL based on sandbox setting
    const apiBase = config.USE_SANDBOX 
        ? 'https://api.sandbox.ebay.com' 
        : 'https://api.ebay.com';
    
    const response = await fetch(`${apiBase}/commerce/seller/v1/seller/${config.SELLER_ID}`, {
        headers: {
            'Authorization': `Bearer ${accessToken}`,
            'Content-Type': 'application/json'
        }
    });
    
    if (!response.ok) {
        throw new Error(`eBay API error: ${response.status} ${response.statusText}`);
    }
    
    return await response.json();
}

// Helper function to show errors on the page
function showError(message, container) {
    container.innerHTML = `
        <div class="error-message">
            <p>${message}</p>
            <button id="retry-button" class="view-button">Retry</button>
        </div>
    `;
    
    document.getElementById('retry-button').addEventListener('click', fetchEbayListings);
}