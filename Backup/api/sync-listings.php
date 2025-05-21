<?php
// api/sync-listings.php

header('Content-Type: application/json');

require_once '../config.php';
require_once '../classes/EbayAPI.php';

// Check if user is logged in (optional - remove if you want public access)
session_start();
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'error' => 'Unauthorized'
    ]);
    exit();
}

try {
    $ebayAPI = new EbayAPI();
    $result = $ebayAPI->updateLocalListings();
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'eBay listings synchronized successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Failed to sync eBay listings'
        ]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error syncing listings',
        'message' => $e->getMessage()
    ]);
}
?>