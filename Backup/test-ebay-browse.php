<?php
// test-ebay-browse.php - Test eBay Browse API more thoroughly

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config.php';
require_once 'classes/EbayAPI.php';

echo "=== Testing eBay Browse API ===\n\n";

try {
    $ebayAPI = new EbayAPI();
    
    // Test different search methods
    $testQueries = [
        ['q' => 'cards', 'description' => 'Search for cards'],
        ['q' => '*', 'description' => 'Wildcard search'],
        ['q' => '', 'description' => 'Empty search'],
        ['q' => 'sports', 'description' => 'Search for sports']
    ];
    
    foreach ($testQueries as $test) {
        echo "{$test['description']}:\n";
        echo "   Query: '{$test['q']}'\n";
        
        // Make API call
        $endpoint = '/buy/browse/v1/item_summary/search';
        $params = [
            'q' => $test['q'],
            'filter' => 'sellers:{tristatecardsnj}',
            'limit' => 10,
            'marketplace_id' => 'EBAY_US'
        ];
        
        // Build URL manually to show what we're calling
        $url = 'https://api.ebay.com' . $endpoint . '?' . http_build_query($params);
        echo "   URL: " . $url . "\n";
        
        // Make the call through our API class
        $result = $ebayAPI->searchStoreItems();
        
        if ($result) {
            if (isset($result['errors'])) {
                echo "   Errors:\n";
                foreach ($result['errors'] as $error) {
                    echo "      - " . ($error['message'] ?? 'Unknown error') . "\n";
                }
            } elseif (isset($result['itemSummaries'])) {
                echo "   Found: " . count($result['itemSummaries']) . " items\n";
                
                if (!empty($result['itemSummaries'])) {
                    $item = $result['itemSummaries'][0];
                    echo "   Sample:\n";
                    echo "      - ID: " . ($item['itemId'] ?? 'N/A') . "\n";
                    echo "      - Title: " . ($item['title'] ?? 'N/A') . "\n";
                    echo "      - Price: $" . ($item['price']['value'] ?? 'N/A') . "\n";
                    break; // Found some results, no need to continue
                }
            } else {
                echo "   Response keys: " . implode(', ', array_keys($result)) . "\n";
            }
        } else {
            echo "   No response\n";
        }
        echo "\n";
    }
    
    // Test direct search with different filters
    echo "Testing alternate search methods:\n\n";
    
    // Method 1: Search with specific eBay store filters
    echo "Method 1: Direct store search\n";
    $params = [
        'q' => 'cards',
        'filter' => 'sellers:{' . urlencode('tristatecardsnj') . '}',
        'limit' => 50,
        'marketplace_id' => 'EBAY_US'
    ];
    
    $result = $ebayAPI->makeRequest('/buy/browse/v1/item_summary/search', $params);
    
    if ($result && isset($result['itemSummaries']) && !empty($result['itemSummaries'])) {
        echo "   ✓ Found " . count($result['itemSummaries']) . " items\n\n";
    } else {
        echo "   No items found\n\n";
    }
    
    // Method 2: Search without filter first
    echo "Method 2: General search\n";
    $params = [
        'q' => 'cards',
        'limit' => 10,
        'marketplace_id' => 'EBAY_US'
    ];
    
    $result = $ebayAPI->makeRequest('/buy/browse/v1/item_summary/search', $params);
    
    if ($result && isset($result['itemSummaries'])) {
        echo "   ✓ General search works, found " . count($result['itemSummaries']) . " items\n";
        
        // Check if any are from your store
        $yourItems = 0;
        foreach ($result['itemSummaries'] as $item) {
            if (isset($item['seller']['username']) && strpos($item['seller']['username'], 'tristatecardsnj') !== false) {
                $yourItems++;
            }
        }
        echo "   Your items in results: " . $yourItems . "\n";
    } else {
        echo "   General search failed\n";
    }
    
} catch (Exception $e) {
    echo "\n❌ ERROR:\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

echo "\n=== Test Complete ===\n";
?>