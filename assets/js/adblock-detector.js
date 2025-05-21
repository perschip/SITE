// Enhanced AdBlock Detector v2.0
(function() {
    // Function to run when DOM is fully loaded
    function initAdblockDetection() {
        console.log("AdBlock detection initialized");
        
        // Create a bait element that ad blockers typically target
        function createBait() {
            const bait = document.createElement('div');
            bait.setAttribute('class', 'ad-banner ad adsbox ad-placement ad-container');
            bait.setAttribute('id', 'ad-detector');
            bait.setAttribute('data-ad-status', 'not-blocked');
            bait.style.position = 'absolute';
            bait.style.height = '1px';
            bait.style.width = '1px';
            bait.style.left = '-10000px';
            bait.style.top = '-10000px';
            bait.innerHTML = '&nbsp;';
            document.body.appendChild(bait);
            return bait;
        }

        // Multiple detection methods to improve reliability
        function detectAdBlocker() {
            const bait = createBait();
            
            setTimeout(function() {
                let adBlockDetected = false;
                let detectionMethod = '';
                
                // Method 1: Check if the element has been hidden or removed
                if (bait.offsetParent === null || 
                    bait.offsetHeight === 0 || 
                    bait.offsetLeft === 0 || 
                    bait.offsetTop === 0 || 
                    bait.offsetWidth === 0 || 
                    bait.clientHeight === 0 || 
                    bait.clientWidth === 0) {
                    adBlockDetected = true;
                    detectionMethod = 'Offset method';
                }
                
                // Method 2: Check computed style
                const computed = window.getComputedStyle(bait);
                if (computed && (computed.display === 'none' || 
                                 computed.visibility === 'hidden' || 
                                 computed.opacity === '0')) {
                    adBlockDetected = true;
                    detectionMethod = 'Style method';
                }
                
                // Method 3: Check if element was removed
                if (!document.getElementById('ad-detector')) {
                    adBlockDetected = true;
                    detectionMethod = 'Element removed';
                }
                
                // Store adblock status in a global variable
                window.adBlockDetected = adBlockDetected;
                console.log("AdBlock detected: " + adBlockDetected + " (" + detectionMethod + ")");
                
                // Remove the bait if it still exists
                if (bait.parentNode) {
                    bait.parentNode.removeChild(bait);
                }
                
                // Check eBay listings if adblock is detected
                if (adBlockDetected) {
                    checkEbayListings();
                }
                
            }, 300); // Increased delay for more reliable detection
        }

        // Check for eBay listing issues
        function checkEbayListings() {
            console.log("Checking eBay listings");
            
            // Wait for Auction Nudge to attempt to load (increased wait time)
            setTimeout(function() {
                // Try multiple possible container IDs
                const possibleContainers = [
                    'ebay-listings',
                    'auction-nudge-items',
                    'auction-nudge-4c9be4bc1',
                    'auction-nudge-unique123',
                    'auction-nudge-classic123',
                    'auction-nudge-tristatecards123'
                ];
                
                let containerFound = false;
                let listingsLoaded = false;
                let containerElement = null;
                
                // Check each possible container
                for (const containerId of possibleContainers) {
                    const container = document.getElementById(containerId);
                    if (container) {
                        containerFound = true;
                        containerElement = container;
                        
                        // Check for actual listings
                        const listingElements = container.querySelectorAll('.an-item, .an-auction, .item-card');
                        if (listingElements && listingElements.length > 0) {
                            listingsLoaded = true;
                            console.log(`Found ${listingElements.length} eBay listings in container #${containerId}`);
                            break;
                        }
                        
                        // Also check if iframe is loaded
                        const iframes = container.querySelectorAll('iframe');
                        if (iframes && iframes.length > 0) {
                            // If we have iframes, assume it might be working
                            const iframe = iframes[0];
                            if (iframe.contentDocument && iframe.contentDocument.body) {
                                const iframeContent = iframe.contentDocument.body.innerHTML;
                                if (iframeContent && iframeContent.length > 100) {
                                    listingsLoaded = true;
                                    console.log(`Found iframe content in container #${containerId}`);
                                    break;
                                }
                            }
                        }
                    }
                }
                
                console.log("eBay container found: " + containerFound);
                console.log("eBay listings loaded: " + listingsLoaded);
                
                // If a container was found but listings weren't loaded, show error message
                if (containerFound && !listingsLoaded) {
                    showEbayErrorMessage(containerElement);
                } else if (!containerFound) {
                    // Try to find any elements with "ebay" in their ID or class
                    const ebayElements = document.querySelectorAll('[id*="ebay"], [class*="ebay"], [id*="auction"], [class*="auction"]');
                    if (ebayElements.length > 0) {
                        console.log(`Found ${ebayElements.length} possible eBay-related elements`);
                        // Show error in the first one
                        showEbayErrorMessage(ebayElements[0]);
                    } else {
                        console.log("No eBay containers found at all");
                    }
                }
            }, 5000); // Allow 5 seconds for Auction Nudge to load (increased from 3 seconds)
        }

        // Show error message for eBay listings
        function showEbayErrorMessage(container) {
            console.log("Showing eBay error message");
            
            if (!container) {
                console.log("No container provided to show error message");
                return;
            }
            
            // Create error message if it doesn't already exist
            if (!container.querySelector('.adblock-warning')) {
                // Create error message
                const errorDiv = document.createElement('div');
                errorDiv.className = 'alert alert-warning adblock-warning';
                errorDiv.innerHTML = `
                    <h5 class="mb-3"><i class="fas fa-exclamation-triangle me-2"></i> eBay Listings Blocked</h5>
                    <p>We've detected that you're using an ad blocker which is preventing our eBay listings from displaying properly.</p>
                    <p class="mb-0">To view our current listings, please consider temporarily disabling your ad blocker for this site, or visit our <a href="https://www.ebay.com/usr/tristate_cards" target="_blank" class="alert-link">eBay store directly <i class="fas fa-external-link-alt fa-xs"></i></a>.</p>
                `;
                
                // Find the auction nudge elements and hide them
                const auctionNudgeElements = container.querySelectorAll('[id^="auction-nudge-"], iframe');
                auctionNudgeElements.forEach(element => {
                    element.style.display = 'none';
                });
                
                // Add our error message to the container
                container.appendChild(errorDiv);
                
                console.log("AdBlock warning message added");
            } else {
                console.log("Warning message already exists");
            }
        }

        // Start the detection process
        detectAdBlocker();
    }

    // Make sure the DOM is fully loaded before running detection
    if (document.readyState === 'complete' || document.readyState === 'interactive') {
        setTimeout(initAdblockDetection, 1000);
    } else {
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(initAdblockDetection, 1000);
        });
    }
    
    // Add event listener for page load completion
    window.addEventListener('load', function() {
        setTimeout(initAdblockDetection, 2000);
    });
    
    // Export functions to global scope so they can be called from other scripts
    window.checkEbayListings = function() {
        setTimeout(function() {
            console.log("Manual check for eBay listings triggered");
            initAdblockDetection();
        }, 500);
    };
})();