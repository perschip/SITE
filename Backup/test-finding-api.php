<?php
// test-finding-api.php - Test eBay Finding API

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config.php';

echo "=== Testing eBay Finding API ===\n\n";

// Finding API endpoint
$endpoint = 'https://svcs.ebay.com/services/search/FindingService/v1';

// Build request
$params = [
    'OPERATION-NAME' => 'findItemsAdvanced',
    'SERVICE-VERSION' => '1.13.0',
    'SECURITY-APPNAME' => EBAY_CLIENT_ID,
    'RESPONSE-DATA-FORMAT' => 'JSON',
    'REST-PAYLOAD' => '',
    
    // Search parameters
    'keywords' => 'cards',
    'itemFilter(0).name' => 'Seller',
    'itemFilter(0).value' => 'tristatecardsnj',
    'itemFilter(1).name' => 'ListingType',
    'itemFilter(1).value' => 'FixedPrice',
    'paginationInput.entriesPerPage' => '100',
    'sortOrder' => 'BestMatch'
];

$url = $endpoint . '?' . http_build_query($params);

echo "API URL: " . $url . "\n\n";

// Make the request
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Status: {$httpCode}\n";

if ($httpCode == 200 && $response) {
    $data = json_decode($response, true);
    
    if ($data && isset($data['findItemsAdvancedResponse'])) {
        $searchResult = $data['findItemsAdvancedResponse'][0];
        
        if (isset($searchResult['ack']) && $searchResult['ack'][0] == 'Success') {
            echo "✓ Finding API Success\n";
            
            $count = $searchResult['paginationOutput'][0]['totalEntries'][0] ?? 0;
            echo "Total items found: {$count}\n\n";
            
            if ($count > 0 && isset($searchResult['searchResult'][0]['item'])) {
                echo "Sample items:\n";
                $items = $searchResult['searchResult'][0]['item'];
                
                foreach (array_slice($items, 0, 3) as $item) {
                    echo "\n   Item:\n";
                    echo "      ID: " . ($item['itemId'][0] ?? 'N/A') . "\n";
                    echo "      Title: " . ($item['title'][0] ?? 'N/A') . "\n";
                    echo "      Price: $" . ($item['sellingStatus'][0]['currentPrice'][0]['__value__'] ?? 'N/A') . "\n";
                    echo "      URL: " . ($item['viewItemURL'][0] ?? 'N/A') . "\n";
                    echo "      Image: " . ($item['galleryURL'][0] ?? 'N/A') . "\n";
                }
            }
        } else {
            echo "❌ Finding API Error\n";
            if (isset($searchResult['errorMessage'])) {
                foreach ($searchResult['errorMessage'] as $error) {
                    echo "   Error: " . ($error['error'][0]['message'][0] ?? 'Unknown') . "\n";
                }
            }
        }
    } else {
        echo "❌ Invalid response format\n";
        echo "Response: " . substr($response, 0, 500) . "...\n";
    }
} else {
    echo "❌ Request failed\n";
    echo "Response: " . $response . "\n";
}

// Also try without keywords
echo "\n=== Trying without keywords ===\n";

$params2 = [
    'OPERATION-NAME' => 'findItemsByProduct',
    'SERVICE-VERSION' => '1.13.0',
    'SECURITY-APPNAME' => EBAY_CLIENT_ID,
    'RESPONSE-DATA-FORMAT' => 'JSON',
    'REST-PAYLOAD' => '',
    
    // Search by seller only
    'itemFilter(0).name' => 'Seller',
    'itemFilter(0).value' => 'tristatecardsnj',
    'paginationInput.entriesPerPage' => '10'
];

$url2 = $endpoint . '?' . http_build_query($params2);
echo "URL: " . $url2 . "\n";

$ch = curl_init($url2);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
$response2 = curl_exec($ch);
$httpCode2 = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Status: {$httpCode2}\n";

if ($httpCode2 == 200 && $response2) {
    $data2 = json_decode($response2, true);
    
    if ($data2 && isset($data2['findItemsByProductResponse'])) {
        $count = $data2['findItemsByProductResponse'][0]['paginationOutput'][0]['totalEntries'][0] ?? 0;
        echo "Total items: {$count}\n";
    }
}

echo "\n=== Test Complete ===\n";
?>