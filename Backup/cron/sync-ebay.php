<?php
// cron/sync-ebay.php
// Run this script every hour: 0 * * * * php /path/to/your/website/cron/sync-ebay.php

// Ensure script is run from command line only
if (php_sapi_name() !== 'cli') {
    die('This script can only be run from the command line');
}

// Set up error logging
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/cron.log');

// Include necessary files
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../classes/EbayAPI.php';

try {
    echo "[" . date('Y-m-d H:i:s') . "] Starting eBay listings sync...\n";
    
    $ebayAPI = new EbayAPI();
    $result = $ebayAPI->updateLocalListings();
    
    if ($result) {
        echo "[" . date('Y-m-d H:i:s') . "] eBay listings synchronized successfully\n";
        
        // Update last sync time
        $stmt = $pdo->prepare("REPLACE INTO site_settings (setting_key, setting_value) VALUES ('last_ebay_sync', NOW())");
        $stmt->execute();
        
        // Log success
        error_log("[" . date('Y-m-d H:i:s') . "] eBay listings synchronized successfully");
    } else {
        echo "[" . date('Y-m-d H:i:s') . "] Failed to sync eBay listings\n";
        error_log("[" . date('Y-m-d H:i:s') . "] Failed to sync eBay listings");
    }
    
    // Also check and update stream status if needed
    echo "[" . date('Y-m-d H:i:s') . "] Checking stream status...\n";
    
    // Update stream status (you would implement actual Whatnot API check here)
    $stmt = $pdo->query("UPDATE stream_schedule SET is_live = FALSE WHERE scheduled_time < NOW()");
    $stmt->execute();
    
    echo "[" . date('Y-m-d H:i:s') . "] Cron job completed\n";
    
} catch (Exception $e) {
    $errorMsg = "[" . date('Y-m-d H:i:s') . "] Error in cron job: " . $e->getMessage();
    echo $errorMsg . "\n";
    error_log($errorMsg);
    
    // You might want to send an email notification here
    // mail('admin@example.com', 'eBay Sync Error', $errorMsg);
}
?>