<?php
// debug-listings.php - Check what's causing the blank page

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== Debugging Listings Page ===\n\n";

try {
    echo "1. Testing PHP execution: ";
    echo "✓ PHP is working\n\n";
    
    echo "2. Testing session start: ";
    session_start();
    echo "✓ Session started\n\n";
    
    echo "3. Testing config include: ";
    require_once '../config.php';
    echo "✓ Config loaded\n\n";
    
    echo "4. Testing database connection: ";
    $stmt = $pdo->query("SELECT 1");
    echo "✓ Database connected\n\n";
    
    echo "5. Testing admin check: ";
    if (!isset($_SESSION['admin_id'])) {
        echo "✗ Not logged in as admin\n";
        echo "   Please log in first at /admin/login.php\n";
    } else {
        echo "✓ Logged in as admin (ID: " . $_SESSION['admin_id'] . ")\n";
    }
    echo "\n";
    
    echo "6. Testing listings table: ";
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM ebay_listings");
    $count = $stmt->fetch()['count'];
    echo "✓ Found {$count} listings\n\n";
    
    echo "7. Testing simple query: ";
    $stmt = $pdo->query("SELECT * FROM ebay_listings LIMIT 1");
    $listing = $stmt->fetch();
    if ($listing) {
        echo "✓ Sample listing: " . $listing['title'] . "\n";
    } else {
        echo "No listings found\n";
    }
    echo "\n";
    
} catch (Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "\nStack trace:\n";
    echo $e->getTraceAsString();
}

echo "\n=== End Debug ===\n";
?>