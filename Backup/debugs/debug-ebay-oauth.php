<?php
// debug-ebay-oauth.php - Debug eBay OAuth parameters

require_once 'config.php';

echo "=== eBay OAuth Debug Information ===\n\n";

// Check required constants
echo "1. eBay Configuration:\n";
echo "   Client ID: " . (defined('EBAY_CLIENT_ID') ? "✓ Defined" : "✗ Not defined") . "\n";
echo "   Client Secret: " . (defined('EBAY_CLIENT_SECRET') ? "✓ Defined" : "✗ Not defined") . "\n";
echo "   Dev ID: " . (defined('EBAY_DEV_ID') ? "✓ Defined" : "✗ Not defined") . "\n";
echo "   Site URL: " . (defined('SITE_URL') ? SITE_URL : "✗ Not defined") . "\n";

if (defined('EBAY_CLIENT_ID')) {
    echo "   Client ID Value: " . EBAY_CLIENT_ID . "\n";
}

// Check redirect URI
$redirectUri = SITE_URL . '/admin/ebay-callback.php';
echo "\n2. Redirect URI:\n";
echo "   Expected: {$redirectUri}\n";
echo "   URL encoded: " . urlencode($redirectUri) . "\n";

// Build the authentication URL
$authUrl = "https://auth.ebay.com/oauth2/authorize?" . http_build_query([
    'client_id' => EBAY_CLIENT_ID,
    'response_type' => 'code',
    'redirect_uri' => $redirectUri,
    'scope' => 'https://api.ebay.com/oauth/api_scope'
]);

echo "\n3. Complete Auth URL:\n";
echo "   {$authUrl}\n";

// Check if we have any error parameters
if (isset($_GET['error'])) {
    echo "\n4. eBay Error Response:\n";
    echo "   Error: " . htmlspecialchars($_GET['error']) . "\n";
    echo "   Error Description: " . (isset($_GET['error_description']) ? htmlspecialchars($_GET['error_description']) : 'N/A') . "\n";
}

// Check callback file
echo "\n5. Callback File:\n";
$callbackFile = __DIR__ . '/admin/ebay-callback.php';
if (file_exists($callbackFile)) {
    echo "   ✓ Exists\n";
    echo "   Path: {$callbackFile}\n";
} else {
    echo "   ✗ Does not exist\n";
}

// Test OAuth token request
echo "\n6. Test Token Request:\n";
try {
    $authString = base64_encode(EBAY_CLIENT_ID . ':' . EBAY_CLIENT_SECRET);
    
    $data = [
        'grant_type' => 'client_credentials',
        'scope' => 'https://api.ebay.com/oauth/api_scope'
    ];
    
    $ch = curl_init('https://api.ebay.com/identity/v1/oauth2/token');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Basic {$authString}",
        "Content-Type: application/x-www-form-urlencoded"
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "   HTTP Status: {$httpCode}\n";
    
    if ($httpCode == 200) {
        echo "   ✓ Client credentials grant successful\n";
        $tokenData = json_decode($response, true);
        echo "   Token type: " . ($tokenData['token_type'] ?? 'N/A') . "\n";
        echo "   Expires in: " . ($tokenData['expires_in'] ?? 'N/A') . " seconds\n";
    } else {
        echo "   ✗ Failed\n";
        echo "   Response: {$response}\n";
    }
} catch (Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
}

echo "\n=== Common Issues ===\n";
echo "1. Make sure your redirect_uri exactly matches what's in your eBay Developer account\n";
echo "2. Ensure SITE_URL in config.php is using HTTPS (required for production)\n";
echo "3. Check that the Client ID is correct (no extra spaces or characters)\n";
echo "4. Verify you're using the production keys, not sandbox\n";

echo "\n=== Next Steps ===\n";
echo "1. Update your eBay app's redirect URI to: {$redirectUri}\n";
echo "2. Make sure SITE_URL in config.php uses HTTPS\n";
echo "3. Try the authentication URL: {$authUrl}\n";
?>