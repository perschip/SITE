<?php
// classes/EbayAPI.php - eBay API with correct username

class EbayAPI {
    private $clientId;
    private $clientSecret;
    private $oauthToken;
    private $tokenEndpoint = 'https://api.ebay.com/identity/v1/oauth2/token';
    private $baseApiUrl = 'https://api.ebay.com';
    private $findingEndpoint = 'https://svcs.ebay.com/services/search/FindingService/v1';
    private $accessToken;
    private $refreshToken;
    private $storeUrl;
    private $sellerName;
    
    public function __construct() {
        $this->clientId = EBAY_CLIENT_ID;
        $this->clientSecret = EBAY_CLIENT_SECRET;
        $this->oauthToken = EBAY_OAUTH_TOKEN;
        $this->storeUrl = EBAY_STORE_URL;
        
        // Use the actual eBay username: tristate_cards
        $this->sellerName = 'tristate_cards';
        
        $this->loadTokens();
    }
    
    // Load tokens from database
    private function loadTokens() {
        global $pdo;
        
        try {
            $stmt = $pdo->prepare("SELECT setting_value FROM site_settings WHERE setting_key = 'ebay_access_token'");
            $stmt->execute();
            $result = $stmt->fetch();
            $this->accessToken = $result ? $result['setting_value'] : null;
            
            $stmt = $pdo->prepare("SELECT setting_value FROM site_settings WHERE setting_key = 'ebay_refresh_token'");
            $stmt->execute();
            $result = $stmt->fetch();
            $this->refreshToken = $result ? $result['setting_value'] : null;
        } catch (PDOException $e) {
            error_log("Error loading eBay tokens: " . $e->getMessage());
        }
    }
    
    // Save tokens to database
    private function saveTokens() {
        global $pdo;
        
        try {
            $stmt = $pdo->prepare("INSERT INTO site_settings (setting_key, setting_value) VALUES ('ebay_access_token', :token) ON DUPLICATE KEY UPDATE setting_value = :token2");
            $stmt->execute([':token' => $this->accessToken, ':token2' => $this->accessToken]);
            
            if ($this->refreshToken) {
                $stmt = $pdo->prepare("INSERT INTO site_settings (setting_key, setting_value) VALUES ('ebay_refresh_token', :token) ON DUPLICATE KEY UPDATE setting_value = :token2");
                $stmt->execute([':token' => $this->refreshToken, ':token2' => $this->refreshToken]);
            }
        } catch (PDOException $e) {
            error_log("Error saving eBay tokens: " . $e->getMessage());
        }
    }
    
    // Get access token using client credentials
    public function getAccessToken() {
        error_log("Getting eBay access token...");
        
        $authString = base64_encode("{$this->clientId}:{$this->clientSecret}");
        
        $data = [
            'grant_type' => 'client_credentials',
            'scope' => 'https://api.ebay.com/oauth/api_scope'
        ];
        
        $ch = curl_init($this->tokenEndpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Basic {$authString}",
            "Content-Type: application/x-www-form-urlencoded"
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        error_log("Token response HTTP {$httpCode}");
        
        if ($httpCode == 200) {
            $tokenData = json_decode($response, true);
            $this->accessToken = $tokenData['access_token'];
            if (isset($tokenData['refresh_token'])) {
                $this->refreshToken = $tokenData['refresh_token'];
            }
            $this->saveTokens();
            return true;
        }
        
        error_log("Token error: HTTP {$httpCode} - {$response}");
        return false;
    }
    
    // Use Finding API to search for items
    public function findItemsAdvanced() {
        error_log("Using Finding API to search for items from seller: {$this->sellerName}");
        
        $params = [
            'OPERATION-NAME' => 'findItemsAdvanced',
            'SERVICE-VERSION' => '1.13.0',
            'SECURITY-APPNAME' => $this->clientId,
            'RESPONSE-DATA-FORMAT' => 'JSON',
            'REST-PAYLOAD' => '',
            
            // Search for all items from this seller
            'keywords' => '',
            'itemFilter(0).name' => 'Seller',
            'itemFilter(0).value' => $this->sellerName,
            'itemFilter(1).name' => 'AvailableTo',
            'itemFilter(1).value' => 'US',
            'paginationInput.entriesPerPage' => '100',
            'paginationInput.pageNumber' => '1',
            'sortOrder' => 'StartTimeNewest'
        ];
        
        $url = $this->findingEndpoint . '?' . http_build_query($params);
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        error_log("Finding API response HTTP {$httpCode}");
        
        if ($httpCode == 200 && $response) {
            $data = json_decode($response, true);
            
            if ($data && isset($data['findItemsAdvancedResponse'])) {
                $searchResult = $data['findItemsAdvancedResponse'][0];
                
                if (isset($searchResult['ack']) && $searchResult['ack'][0] == 'Success') {
                    $count = $searchResult['paginationOutput'][0]['totalEntries'][0] ?? 0;
                    error_log("Finding API found {$count} items");
                    
                    if ($count > 0 && isset($searchResult['searchResult'][0]['item'])) {
                        return $searchResult['searchResult'][0]['item'];
                    }
                } else {
                    error_log("Finding API error: " . json_encode($searchResult['errorMessage'] ?? []));
                }
            }
        }
        
        return [];
    }
    
    // Alternative search method using Browse API
    public function searchStoreBrowse() {
        error_log("Using Browse API to search for items from seller: {$this->sellerName}");
        
        $endpoint = '/buy/browse/v1/item_summary/search';
        
        $params = [
            'q' => '',  // Empty query to get all items
            'filter' => "sellers:{" . urlencode($this->sellerName) . "}",
            'limit' => 100,
            'marketplace_id' => 'EBAY_US',
            'sort' => 'newlyListed'
        ];
        
        if (!$this->accessToken) {
            $this->getAccessToken();
        }
        
        $url = $this->baseApiUrl . $endpoint . '?' . http_build_query($params);
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer {$this->accessToken}",
            "Content-Type: application/json",
            "Accept: application/json"
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        error_log("Browse API response HTTP {$httpCode}");
        
        if ($httpCode == 200 && $response) {
            $result = json_decode($response, true);
            
            if ($result && isset($result['itemSummaries'])) {
                error_log("Browse API found " . count($result['itemSummaries']) . " items");
                return $result;
            }
        }
        
        return null;
    }
    
    // Get store inventory using multiple methods
    public function getStoreInventory() {
        error_log("Getting store inventory...");
        
        // Method 1: Try Finding API
        $items = $this->findItemsAdvanced();
        
        if (!empty($items)) {
            error_log("Found " . count($items) . " items via Finding API");
            
            $listings = [];
            foreach ($items as $item) {
                $listing = $this->formatFindingItem($item);
                if ($listing) {
                    $listings[] = $listing;
                }
            }
            
            if (!empty($listings)) {
                return $listings;
            }
        }
        
        // Method 2: Try Browse API
        $browseResults = $this->searchStoreBrowse();
        
        if ($browseResults && isset($browseResults['itemSummaries'])) {
            error_log("Found " . count($browseResults['itemSummaries']) . " items via Browse API");
            
            $listings = [];
            foreach ($browseResults['itemSummaries'] as $item) {
                $listing = $this->formatBrowseItem($item);
                if ($listing) {
                    $listings[] = $listing;
                }
            }
            
            if (!empty($listings)) {
                return $listings;
            }
        }
        
        error_log("All API methods failed, checking for real data store");
        
        // Check if we already have real data in the database
        global $pdo;
        $stmt = $pdo->query("SELECT COUNT(*) FROM ebay_listings WHERE ebay_item_id NOT LIKE 'TEST_%' AND status = 'active'");
        $realCount = $stmt->fetchColumn();
        
        if ($realCount > 0) {
            error_log("Found {$realCount} real listings in database, not adding test data");
            return [];
        }
        
        error_log("All methods failed, using fallback test data");
        return $this->getFallbackTestData();
    }
    
    // Format item from Finding API
    private function formatFindingItem($item) {
        try {
            $listing = [
                'ebay_item_id' => $item['itemId'][0] ?? '',
                'title' => $item['title'][0] ?? '',
                'price' => $item['sellingStatus'][0]['currentPrice'][0]['__value__'] ?? 0,
                'image_url' => $item['galleryURL'][0] ?? '',
                'listing_url' => $item['viewItemURL'][0] ?? '',
                'description' => '', // Not available in Finding API
                'status' => 'active'
            ];
            
            // Skip items without essential data
            if (empty($listing['ebay_item_id']) || empty($listing['title'])) {
                return null;
            }
            
            return $listing;
        } catch (Exception $e) {
            error_log("Error formatting finding item: " . $e->getMessage());
            return null;
        }
    }
    
    // Format item from Browse API
    private function formatBrowseItem($item) {
        try {
            $listing = [
                'ebay_item_id' => $item['itemId'] ?? '',
                'title' => $item['title'] ?? '',
                'price' => $item['price']['value'] ?? 0,
                'image_url' => $item['image']['imageUrl'] ?? '',
                'listing_url' => $item['itemWebUrl'] ?? '',
                'description' => $item['shortDescription'] ?? '',
                'status' => 'active'
            ];
            
            // Skip items without essential data
            if (empty($listing['ebay_item_id']) || empty($listing['title'])) {
                return null;
            }
            
            return $listing;
        } catch (Exception $e) {
            error_log("Error formatting browse item: " . $e->getMessage());
            return null;
        }
    }
    
    // Get all active listings
    public function getAllStoreListings() {
        error_log("Starting getAllStoreListings()");
        
        // Get real listings from eBay
        $allListings = $this->getStoreInventory();
        
        error_log("Total listings collected: " . count($allListings));
        return $allListings;
    }
    
    // Get featured listings (for homepage and API)
    public function getFeaturedListings($limit = 6) {
        global $pdo;
        
        try {
            $stmt = $pdo->prepare("
                SELECT * FROM ebay_listings 
                WHERE status = 'active' 
                ORDER BY RAND() 
                LIMIT :limit
            ");
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error fetching featured listings: " . $e->getMessage());
            return [];
        }
    }
    
    // Fallback test data if API fails
    private function getFallbackTestData() {
        error_log("Using fallback test data");
        
        return [
            [
                'ebay_item_id' => 'TEST_' . time() . '_1',
                'title' => 'Test Sports Card - ' . date('Y-m-d H:i:s'),
                'price' => 19.99,
                'image_url' => 'https://via.placeholder.com/300x200/3498db/ffffff?text=Sports+Card',
                'listing_url' => 'https://www.ebay.com/itm/TEST_001',
                'description' => 'Test sports card listing. If you see this, the eBay API needs configuration.',
                'status' => 'active'
            ]
        ];
    }
    
    // Update local database with eBay listings
    public function updateLocalListings() {
        global $pdo;
        
        try {
            error_log("Starting eBay sync...");
            
            $listings = $this->getAllStoreListings();
            
            error_log("Sync result - listings count: " . (is_array($listings) ? count($listings) : 'not array'));
            
            if (empty($listings)) {
                error_log("No listings returned from eBay API");
                return false;
            }
            
            // Prepare the insert/update statement
            $stmt = $pdo->prepare("
                INSERT INTO ebay_listings (ebay_item_id, title, price, image_url, listing_url, description, status)
                VALUES (:ebay_item_id, :title, :price, :image_url, :listing_url, :description, :status)
                ON DUPLICATE KEY UPDATE
                title = :title2,
                price = :price2,
                image_url = :image_url2,
                description = :description2,
                status = :status2,
                last_updated = NOW()
            ");
            
            $successCount = 0;
            foreach ($listings as $listing) {
                try {
                    error_log("Processing listing: " . $listing['ebay_item_id']);
                    
                    $params = [
                        ':ebay_item_id' => $listing['ebay_item_id'],
                        ':title' => $listing['title'],
                        ':price' => $listing['price'],
                        ':image_url' => $listing['image_url'],
                        ':listing_url' => $listing['listing_url'],
                        ':description' => $listing['description'],
                        ':status' => $listing['status'],
                        ':title2' => $listing['title'],
                        ':price2' => $listing['price'],
                        ':image_url2' => $listing['image_url'],
                        ':description2' => $listing['description'],
                        ':status2' => $listing['status']
                    ];
                    
                    $stmt->execute($params);
                    $successCount++;
                } catch (PDOException $e) {
                    error_log("Error inserting listing {$listing['ebay_item_id']}: " . $e->getMessage());
                }
            }
            
            // Mark old listings as ended (but only if we have real eBay data)
            if ($successCount > 0 && !strpos($listings[0]['ebay_item_id'], 'TEST_')) {
                $stmt = $pdo->prepare("UPDATE ebay_listings SET status = 'ended' WHERE last_updated < DATE_SUB(NOW(), INTERVAL 2 HOUR) AND status = 'active' AND ebay_item_id NOT LIKE 'TEST_%'");
                $stmt->execute();
            }
            
            // Log success
            error_log("Successfully updated {$successCount} listings from eBay API");
            
            return $successCount > 0;
        } catch (Exception $e) {
            error_log("Error updating eBay listings: " . $e->getMessage());
            error_log("Error file: " . $e->getFile());
            error_log("Error line: " . $e->getLine());
            return false;
        }
    }
}
?>