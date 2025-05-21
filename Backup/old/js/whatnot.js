// js/whatnot.js
document.addEventListener('DOMContentLoaded', function() {
    // Function to check Whatnot live status
    function checkWhatnotStatus() {
        const apiUrl = 'api/whatnot-status.php';
        const statusContainer = document.getElementById('whatnot-status');
        
        if (!statusContainer) return;
        
        // Show loading indicator
        statusContainer.innerHTML = '<div class="loading">Checking Whatnot status... <div class="spinner"></div></div>';
        
        fetch(apiUrl)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Network response was not ok (${response.status})`);
                }
                return response.json();
            })
            .then(data => {
                if (data.isLive) {
                    statusContainer.innerHTML = `
                        <div class="live-badge">LIVE NOW</div>
                        <h3>${data.streamTitle}</h3>
                        <p>${data.viewerCount} viewers</p>
                        <a href="https://www.whatnot.com/user/${data.username}" target="_blank" class="btn btn-primary">Watch Now</a>
                    `;
                    statusContainer.classList.add('live');
                } else {
                    statusContainer.innerHTML = `
                        <div class="offline-badge">OFFLINE</div>
                        <p>Follow on Whatnot for notifications</p>
                        <a href="https://www.whatnot.com/user/${data.username}" target="_blank" class="btn">Follow</a>
                    `;
                    statusContainer.classList.remove('live');
                }
            })
            .catch(error => {
                console.error('Error checking Whatnot status:', error);
                statusContainer.innerHTML = `
                    <p class="error-message">Unable to check live status: ${error.message}</p>
                    <a href="https://www.whatnot.com/user/tscardbreaks" target="_blank" class="btn">Visit Whatnot</a>
                `;
            });
    }
    
    // Initial check
    checkWhatnotStatus();
    
    // Check status every 2 minutes
    setInterval(checkWhatnotStatus, 120000);
});