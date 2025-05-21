<?php
// admin/test-sync-simple.php - Simple test for admin sync

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== Admin Sync Test ===\n\n";

// Check current directory
echo "Current directory: " . __DIR__ . "\n";
echo "Parent directory: " . dirname(__DIR__) . "\n\n";

// Try to include files
echo "Loading files...\n";

try {
    require_once '../config.php';
    echo "✓ config.php loaded\n";
} catch (Exception $e) {
    echo "✗ config.php failed: " . $e->getMessage() . "\n";
}

try {
    require_once '../classes/EbayAPI.php';
    echo "✓ EbayAPI.php loaded\n";
} catch (Exception $e) {
    echo "✗ EbayAPI.php failed: " . $e->getMessage() . "\n";
    
    // Try alternative path
    try {
        require_once dirname(__DIR__) . '/classes/EbayAPI.php';
        echo "✓ EbayAPI.php loaded (alternative path)\n";
    } catch (Exception $e2) {
        echo "✗ EbayAPI.php failed (alternative path): " . $e2->getMessage() . "\n";
    }
}

echo "\n";

// Check if class exists
if (class_exists('EbayAPI')) {
    echo "✓ EbayAPI class found\n";
    
    try {
        $api = new EbayAPI();
        echo "✓ EbayAPI instance created\n";
        
        // Test a simple method
        $result = $api->getAccessToken();
        if ($result) {
            echo "✓ getAccessToken() succeeded\n";
        } else {
            echo "✗ getAccessToken() failed\n";
        }
        
    } catch (Exception $e) {
        echo "✗ Error with EbayAPI: " . $e->getMessage() . "\n";
    }
} else {
    echo "✗ EbayAPI class NOT found\n";
}

echo "\n=== End Test ===\n";
?>