<?php
// enhanced-store-check.php - More detailed eBay store analysis

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== Enhanced eBay Store Check ===\n\n";

$storeUrl = 'https://www.ebay.com/str/tristatecardsnj';
echo "Checking: {$storeUrl}\n\n";

// Use cURL to fetch the store page
$ch = curl_init($storeUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
$html = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$redirect_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
curl_close($ch);

echo "HTTP Status: {$httpCode}\n";
echo "Final URL: {$redirect_url}\n";
echo "Page length: " . strlen($html) . " bytes\n\n";

if ($httpCode == 200 && $html) {
    // Save a copy of the HTML for debugging
    file_put_contents('ebay_store_debug.html', $html);
    echo "HTML saved to ebay_store_debug.html for inspection\n\n";
    
    // Look for various item patterns
    $patterns = [
        'srp-results' => '/class="[^"]*srp-results[^"]*"/',
        's-item' => '/class="[^"]*s-item[^"]*"/',
        'item-link' => '/href="https:\/\/www\.ebay\.com\/itm\/(\d+)/',
        'item-title' => '/class="[^"]*s-item__title[^"]*">([^<]+)/',
        'item-price' => '/class="[^"]*s-item__price[^"]*">([^<]+)/',
        'gallery-image' => '/class="[^"]*gallery-image[^"]*"/',
        'data-item-id' => '/data-item-id="(\d+)"/',
        'listing-widget' => '/class="[^"]*listing-widget[^"]*"/'
    ];
    
    foreach ($patterns as $name => $pattern) {
        preg_match_all($pattern, $html, $matches);
        echo "{$name} matches: " . count($matches[0] ?? []) . "\n";
        if (!empty($matches[1])) {
            echo "   First 3 matches:\n";
            for ($i = 0; $i < min(3, count($matches[1])); $i++) {
                echo "      " . substr(strip_tags($matches[1][$i]), 0, 100) . "\n";
            }
        }
    }
    
    // Look for specific error messages
    $errorPatterns = [
        'No items found' => '/[Nn]o items? found/',
        'No results' => '/[Nn]o results/',
        'Empty store' => '/empty.*store|store.*empty/i',
        'Not available' => '/not available|unavailable/i'
    ];
    
    echo "\nChecking for error messages:\n";
    foreach ($errorPatterns as $name => $pattern) {
        if (preg_match($pattern, $html, $matches)) {
            echo "   ⚠ Found: {$name}\n";
            echo "      Context: " . substr($matches[0], 0, 100) . "\n";
        }
    }
    
    // Look for JSON data
    echo "\nChecking for embedded JSON data:\n";
    if (preg_match('/window\.__itemData\s*=\s*(\{.*?\});/s', $html, $matches)) {
        echo "   ✓ Found __itemData\n";
        $jsonData = json_decode($matches[1], true);
        if ($jsonData && isset($jsonData['items'])) {
            echo "   Items in JSON: " . count($jsonData['items']) . "\n";
        }
    } elseif (preg_match('/window\.__SRP_DATA\s*=\s*(\{.*?\});/s', $html, $matches)) {
        echo "   ✓ Found __SRP_DATA\n";
        $jsonData = json_decode($matches[1], true);
        if ($jsonData && isset($jsonData['app']['modules']['SEARCH_RESULTS']['totalResults'])) {
            echo "   Total results: " . $jsonData['app']['modules']['SEARCH_RESULTS']['totalResults'] . "\n";
        }
    }
    
    // Check meta description
    if (preg_match('/<meta name="description" content="([^"]+)"/', $html, $matches)) {
        echo "\nStore description: " . $matches[1] . "\n";
    }
    
} else {
    echo "❌ Could not fetch store page\n";
    echo "HTTP Code: {$httpCode}\n";
}

// Try alternative URLs
echo "\n=== Trying Alternative URLs ===\n";

$alternativeUrls = [
    'Search URL' => 'https://www.ebay.com/sch/m.html?_ssn=tristatecardsnj&_sop=10',
    'Store items' => 'https://www.ebay.com/str/tristatecardsnj/All-Categories',
    'Seller feed' => 'https://www.ebay.com/sch/tristatecardsnj/m.html'
];

foreach ($alternativeUrls as $name => $url) {
    echo "\n{$name}: {$url}\n";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_HEADER, true);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "   Status: {$httpCode}\n";
    
    if ($httpCode == 200) {
        // Quick check for item count
        if (preg_match('/(\d+)\s+results?/i', $response, $matches)) {
            echo "   Items found: {$matches[1]}\n";
        }
    }
}

echo "\n=== Analysis Complete ===\n\n";
echo "Next steps:\n";
echo "1. Open ebay_store_debug.html in a browser to see what eBay returns\n";
echo "2. Check if your store shows items when visited directly\n";
echo "3. Consider using eBay's Finding API as an alternative\n";
?>