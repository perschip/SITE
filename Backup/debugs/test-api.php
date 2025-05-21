<?php
// test-api.php - Use this to test the API functionality

header('Content-Type: application/json');

require_once 'config.php';
require_once 'classes/EbayAPI.php';

try {
    // Test database connection
    echo "Testing database connection...\n";
    $stmt = $pdo->query("SELECT 1");
    echo "Database connection successful!\n\n";
    
    // Check if ebay_listings table exists
    echo "Checking ebay_listings table...\n";
    $stmt = $pdo->query("SHOW TABLES LIKE 'ebay_listings'");
    $result = $stmt->fetch();
    
    if ($result) {
        echo "ebay_listings table exists!\n";
        
        // Count records
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM ebay_listings");
        $count = $stmt->fetch()['count'];
        echo "Current listings count: {$count}\n\n";
        
        // If no listings, try to create some test data
        if ($count == 0) {
            echo "No listings found. Adding test data...\n";
            $ebayAPI = new EbayAPI();
            $result = $ebayAPI->updateLocalListings();
            
            if ($result) {
                echo "Test data added successfully!\n";
                
                // Check again
                $stmt = $pdo->query("SELECT COUNT(*) as count FROM ebay_listings");
                $count = $stmt->fetch()['count'];
                echo "New listings count: {$count}\n";
            } else {
                echo "Failed to add test data.\n";
            }
        }
        
        // Test API endpoint
        echo "\nTesting API endpoint...\n";
        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/api/listings.php?featured=true";
        
        $response = file_get_contents($url);
        $data = json_decode($response, true);
        
        if ($data && $data['success']) {
            echo "API endpoint working! Found " . count($data['listings']) . " featured listings.\n";
        } else {
            echo "API endpoint failed with response: " . $response . "\n";
        }
        
    } else {
        echo "ERROR: ebay_listings table does not exist!\n";
        echo "Please run the database setup script first.\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
?>