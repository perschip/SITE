<?php
// test-real-ebay.php - Test real eBay API integration

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config.php';
require_once 'classes/EbayAPI.php';

echo "=== Testing Real eBay API Integration ===\n\n";

try {
    $ebayAPI = new EbayAPI();
    
    // Step 1: Test access token
    echo "1. Testing Access Token:\n";
    $tokenResult = $ebayAPI->getAccessToken();
    if ($tokenResult) {
        echo "   ✓ Successfully obtained access token\n";
    } else {
        echo "   ✗ Failed to obtain access token\n";
    }
    echo "\n";
    
    // Step 2: Test store search
    echo "2. Testing Store Search:\n";
    $storeUrl = EBAY_STORE_URL;
    echo "   Store URL: {$storeUrl}\n";
    
    // Extract seller name
    $sellerName = '';
    if (preg_match('/\/str\/([^\/]+)/', $storeUrl, $matches)) {
        $sellerName = $matches[1];
        echo "   Extracted seller name: {$sellerName}\n";
    } else {
        echo "   ✗ Could not extract seller name from URL\n";
    }
    echo "\n";
    
    // Step 3: Test search API
    echo "3. Testing Search API:\n";
    $searchResults = $ebayAPI->searchStoreItems();
    
    if ($searchResults) {
        echo "   ✓ Search API responded\n";
        
        if (isset($searchResults['errors'])) {
            echo "   API Errors:\n";
            foreach ($searchResults['errors'] as $error) {
                echo "      - " . ($error['message'] ?? 'Unknown error') . "\n";
            }
        } elseif (isset($searchResults['itemSummaries'])) {
            echo "   Found " . count($searchResults['itemSummaries']) . " items\n";
            
            if (!empty($searchResults['itemSummaries'])) {
                $firstItem = $searchResults['itemSummaries'][0];
                echo "   Sample item:\n";
                echo "      ID: " . ($firstItem['itemId'] ?? 'N/A') . "\n";
                echo "      Title: " . ($firstItem['title'] ?? 'N/A') . "\n";
                echo "      Price: $" . ($firstItem['price']['value'] ?? 'N/A') . "\n";
            }
        } else {
            echo "   Unexpected response format\n";
            echo "   Response keys: " . implode(', ', array_keys($searchResults)) . "\n";
        }
    } else {
        echo "   ✗ Search API failed\n";
    }
    echo "\n";
    
    // Step 4: Test full sync
    echo "4. Testing Full Sync:\n";
    $before = $pdo->query("SELECT COUNT(*) FROM ebay_listings")->fetchColumn();
    echo "   Listings before sync: {$before}\n";
    
    $syncResult = $ebayAPI->updateLocalListings();
    
    if ($syncResult) {
        echo "   ✓ Sync completed successfully\n";
        
        $after = $pdo->query("SELECT COUNT(*) FROM ebay_listings")->fetchColumn();
        echo "   Listings after sync: {$after}\n";
        echo "   Net change: " . ($after - $before) . "\n";
        
        // Show recent listings
        $stmt = $pdo->query("SELECT * FROM ebay_listings ORDER BY last_updated DESC LIMIT 3");
        echo "\n   Recent listings:\n";
        while ($row = $stmt->fetch()) {
            echo "      - " . $row['title'] . " ($" . $row['price'] . ")\n";
        }
    } else {
        echo "   ✗ Sync failed\n";
    }
    
    // Step 5: Check error log
    echo "\n5. Recent Error Log:\n";
    $errorLog = __DIR__ . '/logs/error.log';
    if (file_exists($errorLog)) {
        $lines = file($errorLog);
        $recentLines = array_slice($lines, -10);
        foreach ($recentLines as $line) {
            if (strpos($line, 'eBay') !== false || strpos($line, 'API') !== false) {
                echo "   " . trim($line) . "\n";
            }
        }
    } else {
        echo "   No error log found\n";
    }
    
} catch (Exception $e) {
    echo "\n❌ ERROR:\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

echo "\n=== Test Complete ===\n";
?>