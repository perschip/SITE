<?php
// admin/sync-ebay.php - Manual eBay sync for admin

session_start();
require_once '../config.php';
require_once '../classes/EbayAPI.php';

requireLogin();

header('Content-Type: application/json');

try {
    $ebayAPI = new EbayAPI();
    $result = $ebayAPI->updateLocalListings();
    
    if ($result) {
        // Update last sync time in database
        $stmt = $pdo->prepare("INSERT INTO site_settings (setting_key, setting_value) VALUES ('last_ebay_sync', NOW()) ON DUPLICATE KEY UPDATE setting_value = NOW()");
        $stmt->execute();
        
        echo json_encode([
            'success' => true,
            'message' => 'eBay listings synchronized successfully',
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Failed to sync eBay listings',
            'message' => 'Check error logs for details'
        ]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error syncing listings',
        'message' => $e->getMessage(),
        'debug' => [
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]
    ]);
}
?>