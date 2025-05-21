// Load Whatnot configuration from localStorage
function loadWhatnotConfig() {
    return {
        USERNAME: localStorage.getItem('whatnot_username') || ''
    };
}

// Function to check if Whatnot configuration is set up
function isWhatnotConfigured() {
    const config = loadWhatnotConfig();
    return config.USERNAME ? true : false;
}

// Function to check if you're live on Whatnot
async function checkWhatnotStatus() {
    const statusDot = document.getElementById('status-dot');
    const liveStatus = document.getElementById('live-status');
    const statusContainer = document.querySelector('.whatnot-status');
    
    // Check if Whatnot is configured
    if (!isWhatnotConfigured()) {
        liveStatus.textContent = 'Whatnot not configured';
        statusContainer.style.cursor = 'pointer';
        statusContainer.onclick = () => {
            window.location.href = 'setup.html';
        };
        return;
    }
    
    const config = loadWhatnotConfig();
    
    try {
        // In a real implementation, you would check if the user is live on Whatnot
        // This could be done by scraping the Whatnot page or using their API if available
        
        // For now, use a random status for demonstration
        const isLive = Math.random() > 0.5;
        
        updateWhatnotStatus(isLive);
        
        // Schedule the next check in 2 minutes
        setTimeout(checkWhatnotStatus, 120000);
    } catch (error) {
        console.error('Error checking Whatnot status:', error);
        
        // Show error in status
        statusDot.classList.remove('live');
        liveStatus.textContent = 'Unable to check Whatnot status';
        
        // Try again in 5 minutes
        setTimeout(checkWhatnotStatus, 300000);
    }
}

// Function to update the Whatnot status display
function updateWhatnotStatus(isLive) {
    const statusDot = document.getElementById('status-dot');
    const liveStatus = document.getElementById('live-status');
    const statusContainer = document.querySelector('.whatnot-status');
    
    const config = loadWhatnotConfig();
    
    if (isLive) {
        // Update UI to show live status
        statusDot.classList.add('live');
        liveStatus.textContent = 'LIVE NOW on Whatnot! Click to Watch';
        
        // Make the status container clickable
        statusContainer.style.cursor = 'pointer';
        statusContainer.onclick = () => {
            window.open(`https://www.whatnot.com/user/${config.USERNAME}`, '_blank');
        };
    } else {
        // Update UI to show not live status
        statusDot.classList.remove('live');
        liveStatus.textContent = 'Not Currently Live on Whatnot';
        
        // Still make it clickable to go to the profile
        statusContainer.style.cursor = 'pointer';
        statusContainer.onclick = () => {
            window.open(`https://www.whatnot.com/user/${config.USERNAME}`, '_blank');
        };
    }
}

// Future enhancement: Real Whatnot API or web scraping integration
// This would check if the user is actually live on Whatnot
async function checkRealWhatnotStatus() {
    const config = loadWhatnotConfig();
    
    // This would be replaced with actual API call or web scraping
    // For example, fetch the user's Whatnot page and check if there's a "Live" indicator
    const response = await fetch(`https://www.whatnot.com/user/${config.USERNAME}`);
    const html = await response.text();
    
    // Check if the page contains any indicators that the user is live
    const isLive = html.includes('is live now') || html.includes('LIVE') || html.includes('streaming now');
    
    return isLive;
}

// Advanced implementation with server-side proxy
// This function would work if you had a server-side component to avoid CORS issues
async function checkWhatnotStatusViaProxy() {
    const config = loadWhatnotConfig();
    
    try {
        // This assumes you have a server endpoint that checks Whatnot status for you
        // Replace with your actual server URL
        const response = await fetch(`/api/whatnot-status?username=${config.USERNAME}`);
        
        if (!response.ok) {
            throw new Error(`Error checking Whatnot status: ${response.status} ${response.statusText}`);
        }
        
        const data = await response.json();
        return data.isLive;
    } catch (error) {
        console.error('Error checking Whatnot status via proxy:', error);
        return false;
    }
}

// Helper function to get upcoming Whatnot streams
// Note: This requires server-side scraping or Whatnot API access
async function getUpcomingWhatnotStreams() {
    const config = loadWhatnotConfig();
    
    try {
        // This is a placeholder for a real implementation
        // In reality, you would need server-side scraping or API access
        
        // Mock data for demonstration
        return [
            {
                title: 'Sports Card Break - Baseball & Football',
                scheduledTime: new Date(Date.now() + 86400000), // Tomorrow
                image: 'https://via.placeholder.com/300x200'
            },
            {
                title: 'Hobby Box Openings - NBA Basketball',
                scheduledTime: new Date(Date.now() + 172800000), // 2 days from now
                image: 'https://via.placeholder.com/300x200'
            }
        ];
    } catch (error) {
        console.error('Error getting upcoming Whatnot streams:', error);
        return [];
    }
}

// Function to display upcoming streams on the page
// You could call this if you want to show a schedule
function displayUpcomingStreams() {
    // Get the container element where streams will be displayed
    const container = document.getElementById('upcoming-streams');
    
    if (!container) {
        return;
    }
    
    getUpcomingWhatnotStreams().then(streams => {
        if (streams.length === 0) {
            container.innerHTML = '<p>No upcoming streams scheduled.</p>';
            return;
        }
        
        let html = '<h3>Upcoming Whatnot Streams</h3><div class="stream-grid">';
        
        streams.forEach(stream => {
            // Format the date 
            const formattedDate = stream.scheduledTime.toLocaleDateString('en-US', {
                weekday: 'short',
                month: 'short',
                day: 'numeric',
                hour: 'numeric',
                minute: '2-digit'
            });
            
            html += `
                <div class="stream-card">
                    <img src="${stream.image}" alt="${stream.title}" class="stream-image">
                    <div class="stream-info">
                        <h4>${stream.title}</h4>
                        <p class="stream-time">${formattedDate}</p>
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
        container.innerHTML = html;
    });
}