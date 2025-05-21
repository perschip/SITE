// app.js

// Theme handling
document.addEventListener('DOMContentLoaded', function() {
    const themeToggle = document.getElementById('theme-toggle');
    const themeIcon = document.getElementById('theme-icon');
    const body = document.body;
    
    // Get current theme from cookie
    const currentTheme = getCookie('theme_mode') || 'light';
    setTheme(currentTheme);
    
    if (themeToggle) {
        themeToggle.addEventListener('click', function() {
            const newTheme = body.classList.contains('theme-dark') ? 'light' : 'dark';
            setTheme(newTheme);
            setCookie('theme_mode', newTheme, 365);
        });
    }
    
    function setTheme(theme) {
        body.className = 'theme-' + theme;
        if (themeIcon) {
            themeIcon.className = theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
        }
    }
    
    function setCookie(name, value, days) {
        let expires = "";
        if (days) {
            const date = new Date();
            date.setTime(date.setTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + (value || "") + expires + "; path=/";
    }
    
    function getCookie(name) {
        const nameEQ = name + "=";
        const ca = document.cookie.split(';');
        for(let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) === ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }
    
    // Auto-refresh stream status every 30 seconds
    if (typeof loadStreamStatus === 'function') {
        setInterval(loadStreamStatus, 30000);
    }
});

// Loading animation
function showLoading(element) {
    element.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x"></i></div>';
}

// Error handling
function showError(message, container) {
    if (container) {
        container.innerHTML = `
            <div class="alert alert-danger" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                ${message}
            </div>
        `;
    } else {
        console.error(message);
    }
}

// Debounce function for search
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Format currency
function formatCurrency(amount) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount);
}

// Format date
function formatDate(dateString) {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString(undefined, options);
}

// Scroll to top function
function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

// Add scroll to top button
const scrollButton = document.createElement('button');
scrollButton.innerHTML = '<i class="fas fa-arrow-up"></i>';
scrollButton.className = 'btn btn-primary position-fixed bottom-0 end-0 m-4 rounded-pill';
scrollButton.style.zIndex = '9999';
scrollButton.style.display = 'none';
scrollButton.onclick = scrollToTop;
document.body.appendChild(scrollButton);

window.onscroll = function() {
    if (document.body.scrollTop > 100 || document.documentElement.scrollTop > 100) {
        scrollButton.style.display = 'block';
    } else {
        scrollButton.style.display = 'none';
    }
};

// Toast notification system
function showToast(message, type = 'info', duration = 3000) {
    const toastContainer = document.getElementById('toast-container') || createToastContainer();
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type} border-0`;
    toast.role = 'alert';
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    
    toastContainer.appendChild(toast);
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
    
    setTimeout(() => {
        if (toast.parentNode) {
            toast.remove();
        }
    }, duration + 1000);
}

function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toast-container';
    container.className = 'position-fixed top-0 end-0 p-3';
    container.style.zIndex = '9999';
    document.body.appendChild(container);
    return container;
}

// API Helper functions
async function fetchAPI(url, options = {}) {
    try {
        const response = await fetch(url, {
            ...options,
            headers: {
                'Content-Type': 'application/json',
                ...options.headers
            }
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        return await response.json();
    } catch (error) {
        console.error('API Error:', error);
        throw error;
    }
}

// Search functionality
function initializeSearch() {
    const searchInput = document.getElementById('search-input');
    const searchResults = document.getElementById('search-results');
    
    if (searchInput && searchResults) {
        const debouncedSearch = debounce(async (query) => {
            if (query.length < 3) {
                searchResults.innerHTML = '';
                return;
            }
            
            try {
                showLoading(searchResults);
                const data = await fetchAPI(`/api/search.php?q=${encodeURIComponent(query)}`);
                displaySearchResults(data, searchResults);
            } catch (error) {
                showError('Error searching listings', searchResults);
            }
        }, 300);
        
        searchInput.addEventListener('input', (e) => {
            debouncedSearch(e.target.value);
        });
    }
}

function displaySearchResults(data, container) {
    if (!data.listings || data.listings.length === 0) {
        container.innerHTML = '<div class="text-center text-muted">No results found</div>';
        return;
    }
    
    container.innerHTML = data.listings.map(listing => `
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <img src="${listing.image_url || '/api/placeholder/300/200'}" class="card-img-top" alt="${listing.title}">
                <div class="card-body">
                    <h6 class="card-title">${listing.title}</h6>
                    <p class="card-text">${formatCurrency(listing.price)}</p>
                    <a href="${listing.listing_url}" class="btn btn-sm btn-primary" target="_blank">View on eBay</a>
                </div>
            </div>
        </div>
    `).join('');
}

// Initialize page-specific functions
document.addEventListener('DOMContentLoaded', function() {
    initializeSearch();
    
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
    
    // Initialize popovers
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl));
});

// Admin-specific functions
if (window.location.pathname.includes('/admin/')) {
    // Add admin-specific JavaScript here
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize admin features
        initializeAdminPanel();
    });
    
    function initializeAdminPanel() {
        // Add admin-specific functionality
        const forms = document.querySelectorAll('form[data-ajax="true"]');
        forms.forEach(form => {
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                
                try {
                    const response = await fetch(this.action, {
                        method: 'POST',
                        body: formData
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        showToast(data.message || 'Operation successful', 'success');
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        }
                    } else {
                        showToast(data.message || 'Operation failed', 'danger');
                    }
                } catch (error) {
                    showToast('An error occurred', 'danger');
                    console.error(error);
                }
            });
        });
    }
}