<?php
// test-correct-username.php - Test with correct eBay username

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config.php';
require_once 'classes/EbayAPI.php';

echo "=== Testing with Correct eBay Username: tristate_cards ===\n\n";

try {
    // Test direct URLs first
    $testUrls = [
        'Member Profile' => 'https://www.ebay.com/usr/tristate_cards',
        'Member Items' => 'https://www.ebay.com/sch/tristate_cards/m.html?_nkw=',
        'Store URL' => 'https://www.ebay.com/str/tristatecardsnj',
        'Direct Search' => 'https://www.ebay.com/sch/i.html?_nkw=&_ssn=tristate_cards'
    ];
    
    foreach ($testUrls as $name => $url) {
        echo "{$name}: {$url}\n";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible)');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        echo "   Status: {$httpCode}\n";
        
        if ($httpCode == 200) {
            // Look for item count
            if (preg_match('/(\d+)\s+results?/i', $response, $matches)) {
                echo "   Found: {$matches[1]} results\n";
            }
            // Look for items in HTML
            if (preg_match_all('/class="[^"]*item[^"]*"/i', $response, $matches)) {
                echo "   HTML items: " . count($matches[0]) . "\n";
            }
        }
        echo "\n";
    }
    
    // Test Finding API with correct username
    echo "=== Testing Finding API with tristate_cards ===\n";
    
    $findingEndpoint = 'https://svcs.ebay.com/services/search/FindingService/v1';
    
    $params = [
        'OPERATION-NAME' => 'findItemsByKeywords',
        'SERVICE-VERSION' => '1.13.0',
        'SECURITY-APPNAME' => EBAY_CLIENT_ID,
        'RESPONSE-DATA-FORMAT' => 'JSON',
        'REST-PAYLOAD' => '',
        
        'keywords' => '',
        'itemFilter(0).name' => 'Seller',
        'itemFilter(0).value' => 'tristate_cards',
        'paginationInput.entriesPerPage' => '50'
    ];
    
    $url = $findingEndpoint . '?' . http_build_query($params);
    echo "API URL: " . $url . "\n\n";
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "HTTP Status: {$httpCode}\n";
    
    if ($httpCode == 200 && $response) {
        $data = json_decode($response, true);
        
        if ($data && isset($data['findItemsByKeywordsResponse'])) {
            $searchResult = $data['findItemsByKeywordsResponse'][0];
            
            if (isset($searchResult['ack']) && $searchResult['ack'][0] == 'Success') {
                echo "✓ Finding API Success\n";
                
                $count = $searchResult['paginationOutput'][0]['totalEntries'][0] ?? 0;
                echo "Total items found: {$count}\n\n";
                
                if ($count > 0 && isset($searchResult['searchResult'][0]['item'])) {
                    echo "Sample items from tristate_cards:\n";
                    $items = $searchResult['searchResult'][0]['item'];
                    
                    foreach (array_slice($items, 0, 5) as $item) {
                        echo "\n   Item:\n";
                        echo "      ID: " . ($item['itemId'][0] ?? 'N/A') . "\n";
                        echo "      Title: " . ($item['title'][0] ?? 'N/A') . "\n";
                        echo "      Price: $" . ($item['sellingStatus'][0]['currentPrice'][0]['__value__'] ?? 'N/A') . "\n";
                        echo "      URL: " . ($item['viewItemURL'][0] ?? 'N/A') . "\n";
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
        }
    }
    
    // Test our EbayAPI class
    echo "\n=== Testing EbayAPI Class ===\n";
    $ebayAPI = new EbayAPI();
    $result = $ebayAPI->updateLocalListings();
    
    if ($result) {
        echo "✓ Sync successful!\n";
        
        // Check database
        $stmt = $pdo->query("SELECT COUNT(*) FROM ebay_listings WHERE ebay_item_id NOT LIKE 'TEST_%'");
        $realCount = $stmt->fetchColumn();
        echo "Real eBay items in database: {$realCount}\n";
    } else {
        echo "❌ Sync failed\n";
    }
    
} catch (Exception $e) {
    echo "\n❌ ERROR:\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";  
    echo "Line: " . $e->getLine() . "\n";
}

echo "\n=== Test Complete ===\n";
?>