<?php
// api/ebay-test.php
header('Content-Type: application/json');
require_once '../config/config.php';

// Test eBay API connection
function testEbayConnection() {
    $appID = EBAY_APP_ID;
    $devID = EBAY_DEV_ID;
    $certID = EBAY_CERT_ID;
    $oauthToken = EBAY_OAUTH_TOKEN;
    
    // API endpoint for testing
    $endpoint = 'https://api.ebay.com/buy/browse/v1/item_summary/search';
    
    // Simple parameters for testing
    $params = [
        'q' => 'test',
        'limit' => 1
    ];
    
    // Create curl resource
    $ch = curl_init();
    
    // Set curl options
    curl_setopt_array($ch, [
        CURLOPT_URL => $endpoint . '?' . http_build_query($params),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $oauthToken,
            'X-EBAY-C-MARKETPLACE-ID: EBAY_US',
            'Content-Type: application/json'
        ]
    ]);
    
    // Execute curl and get response
    $response = curl_exec($ch);
    
    // Check for errors
    if (curl_errno($ch)) {
        return [
            'success' => false,
            'error' => 'Curl error: ' . curl_error($ch)
        ];
    }
    
    // Get HTTP status code
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    // Close curl resource
    curl_close($ch);
    
    // Decode JSON response
    $data = json_decode($response, true);
    
    if ($httpCode >= 200 && $httpCode < 300) {
        return [
            'success' => true,
            'message' => 'API connection successful'
        ];
    } else {
        return [
            'success' => false,
            'error' => isset($data['errors'][0]['message']) ? $data['errors'][0]['message'] : 'HTTP Error: ' . $httpCode
        ];
    }
}

// Test connection and return JSON response
echo json_encode(testEbayConnection());
?>