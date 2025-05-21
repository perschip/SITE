<?php
// check-ebay-store.php - Check eBay store for actual listings

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== Checking eBay Store Directly ===\n\n";

$storeUrl = 'https://www.ebay.com/str/tristatecardsnj';
echo "Store URL: {$storeUrl}\n\n";

// Use cURL to fetch the store page
$ch = curl_init($storeUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$html = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Status: {$httpCode}\n";
echo "Page length: " . strlen($html) . " bytes\n\n";

if ($httpCode == 200 && $html) {
    // Look for item listings in the HTML
    $itemCount = preg_match_all('/class="[^"]*item[^"]*"/', $html, $matches);
    echo "Potential items found: {$itemCount}\n\n";
    
    // Look for specific eBay listing patterns
    $patterns = [
        'item link' => '/href="https:\/\/www\.ebay\.com\/itm\/([^"]+)"/',
        'item title' => '/data-item-title="([^"]+)"/',
        'item price' => '/"price":\s*"([^"]+)"/',
        'item image' => '/"imageUrl":\s*"([^"]+)"/'
    ];
    
    foreach ($patterns as $name => $pattern) {
        preg_match_all($pattern, $html, $matches);
        echo "{$name} matches: " . count($matches[1] ?? []) . "\n";
        if (!empty($matches[1])) {
            echo "   Sample: " . htmlspecialchars($matches[1][0]) . "\n";
        }
    }
    
    // Check for "no items" messages
    if (strpos($html, 'no items') !== false || strpos($html, 'No items') !== false) {
        echo "\n⚠ Store page may show 'no items' message\n";
    }
    
    // Check if store exists
    if (strpos($html, '404') !== false || strpos($html, 'not found') !== false) {
        echo "\n❌ Store may not exist or is not accessible\n";
    }
    
    // Check for store name verification
    if (strpos($html, 'tristatecardsnj') !== false) {
        echo "\n✓ Store name verified on page\n";
    }
    
} else {
    echo "❌ Could not fetch store page\n";
    echo "HTTP Code: {$httpCode}\n";
}

echo "\n=== Alternative Check ===\n";

// Try the eBay member profile URL
$profileUrl = 'https://www.ebay.com/sch/m.html?_ssn=tristatecardsnj';
echo "Profile URL: {$profileUrl}\n";

$ch = curl_init($profileUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
$html2 = curl_exec($ch);
$httpCode2 = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Status: {$httpCode2}\n";

if ($httpCode2 == 200 && $html2) {
    // Look for listing count
    if (preg_match('/(\d+)\s+results?/i', $html2, $matches)) {
        echo "Items found on profile: {$matches[1]}\n";
    }
    
    // Look for item listings
    $itemLinks = preg_match_all('/href="https:\/\/www\.ebay\.com\/itm\/([^"]+)"/', $html2, $matches);
    echo "Items on profile page: {$itemLinks}\n";
}

echo "\n=== Check Complete ===\n";
echo "\nIf both checks show no items, your eBay store might be:\n";
echo "1. Empty (no active listings)\n";
echo "2. Set to private/not public\n";
echo "3. Using a different store URL\n";
echo "4. Temporarily unavailable\n";
?>