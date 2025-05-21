// Initialize the page when it loads
document.addEventListener('DOMContentLoaded', () => {
    // Set the current year for the footer
    document.getElementById('current-year').textContent = new Date().getFullYear();
    
    // Initialize the eBay listings
    if (typeof fetchEbayListings === 'function') {
        fetchEbayListings();
    }
    
    // Initialize the Whatnot status check
    if (typeof checkWhatnotStatus === 'function') {
        checkWhatnotStatus();
        
        // Periodically check Whatnot status (every 60 seconds)
        setInterval(checkWhatnotStatus, 60000);
    }
});

// Helper function to create HTML elements with attributes
function createElement(tag, attributes = {}, children = []) {
    const element = document.createElement(tag);
    
    // Set attributes
    for (const [key, value] of Object.entries(attributes)) {
        if (key === 'className') {
            element.className = value;
        } else if (key === 'textContent') {
            element.textContent = value;
        } else {
            element.setAttribute(key, value);
        }
    }
    
    // Append children
    children.forEach(child => {
        if (typeof child === 'string') {
            element.appendChild(document.createTextNode(child));
        } else {
            element.appendChild(child);
        }
    });
    
    return element;
}

// Function to display an error message
function showError(message, container) {
    const errorElement = createElement('div', {
        className: 'error',
        textContent: message
    });
    
    container.innerHTML = '';
    container.appendChild(errorElement);
}