<?php
// test-comprehensive-fixed.php - Fixed version with proper paths

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== File Structure Check ===\n\n";

// Check current directory
echo "Current directory: " . __DIR__ . "\n";
echo "Files in current directory:\n";
foreach (scandir(__DIR__) as $file) {
    echo "   - $file\n";
}
echo "\n";

// Check if classes directory exists
$classesDir = __DIR__ . '/classes';
echo "Classes directory (" . $classesDir . "): ";
if (is_dir($classesDir)) {
    echo "EXISTS\n";
    echo "Files in classes directory:\n";
    foreach (scandir($classesDir) as $file) {
        echo "   - $file\n";
    }
} else {
    echo "NOT FOUND\n";
}
echo "\n";

// Check if EbayAPI.php exists
$ebayApiFile = __DIR__ . '/classes/EbayAPI.php';
echo "EbayAPI.php file (" . $ebayApiFile . "): ";
if (file_exists($ebayApiFile)) {
    echo "EXISTS\n";
    echo "File size: " . filesize($ebayApiFile) . " bytes\n";
    echo "File permissions: " . substr(sprintf('%o', fileperms($ebayApiFile)), -4) . "\n";
} else {
    echo "NOT FOUND\n";
    
    // Check alternative locations
    $altPaths = [
        __DIR__ . '/EbayAPI.php',
        __DIR__ . '/admin/classes/EbayAPI.php',
        __DIR__ . '/admin/EbayAPI.php'
    ];
    
    echo "\nChecking alternative locations:\n";
    foreach ($altPaths as $path) {
        echo "   - $path: " . (file_exists($path) ? "EXISTS" : "not found") . "\n";
    }
}
echo "\n";

// Try to start session
echo "=== Session Test ===\n";
try {
    session_start();
    echo "Session started successfully\n";
    echo "Session ID: " . session_id() . "\n";
    if (isset($_SESSION['admin_id'])) {
        echo "Admin ID: " . $_SESSION['admin_id'] . "\n";
    } else {
        echo "Admin not logged in\n";
    }
} catch (Exception $e) {
    echo "Session error: " . $e->getMessage() . "\n";
}
echo "\n";

// Try to load config
echo "=== Config Test ===\n";
$configFile = __DIR__ . '/config.php';
echo "Config file (" . $configFile . "): ";
if (file_exists($configFile)) {
    echo "EXISTS\n";
    try {
        require_once $configFile;
        echo "Config loaded successfully\n";
        echo "Database name: " . DB_NAME . "\n";
        echo "eBay Client ID: " . (defined('EBAY_CLIENT_ID') ? "Set" : "Not set") . "\n";
    } catch (Exception $e) {
        echo "Config error: " . $e->getMessage() . "\n";
    }
} else {
    echo "NOT FOUND\n";
}
echo "\n";

// Try to load EbayAPI class manually
echo "=== EbayAPI Class Test ===\n";
if (file_exists($ebayApiFile)) {
    try {
        require_once $ebayApiFile;
        echo "EbayAPI.php included successfully\n";
        
        if (class_exists('EbayAPI')) {
            echo "EbayAPI class found\n";
            
            // Try to create instance
            $ebayAPI = new EbayAPI();
            echo "EbayAPI instance created successfully\n";
        } else {
            echo "EbayAPI class NOT found after include\n";
        }
    } catch (Exception $e) {
        echo "Error loading EbayAPI: " . $e->getMessage() . "\n";
        echo "File: " . $e->getFile() . "\n";
        echo "Line: " . $e->getLine() . "\n";
    }
} else {
    echo "Cannot load EbayAPI - file not found\n";
}

echo "\n=== Next Steps ===\n";
echo "1. Ensure EbayAPI.php is in the correct location\n";
echo "2. Check file permissions\n";
echo "3. Verify the file contains the EbayAPI class\n";
echo "4. Run this test again\n";
?>