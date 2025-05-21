<?php
// admin/ebay-callback.php - Handles eBay OAuth callback

session_start();
require_once '../config.php';
require_once '../classes/EbayAPI.php';

requireLogin();

// Check for errors first
if (isset($_GET['error'])) {
    $error = $_GET['error'];
    $errorDescription = isset($_GET['error_description']) ? $_GET['error_description'] : '';
    
    // Log the error
    error_log("eBay OAuth Error: {$error} - {$errorDescription}");
    
    $_SESSION['ebay_auth_error'] = true;
    header('Location: dashboard.php?error=' . urlencode("eBay authentication failed: {$error}"));
    exit();
}

// Check for authorization code
if (isset($_GET['code'])) {
    $authorizationCode = $_GET['code'];
    
    try {
        $ebayAPI = new EbayAPI();
        $success = $ebayAPI->getAccessToken($authorizationCode);
        
        if ($success) {
            $_SESSION['ebay_auth_success'] = true;
            header('Location: dashboard.php?message=eBay authentication successful!');
        } else {
            $_SESSION['ebay_auth_error'] = true;
            header('Location: dashboard.php?error=Failed to get eBay access token');
        }
    } catch (Exception $e) {
        error_log("eBay Auth Exception: " . $e->getMessage());
        $_SESSION['ebay_auth_error'] = true;
        header('Location: dashboard.php?error=' . urlencode("Error during authentication: " . $e->getMessage()));
    }
} else {
    // No code or error received
    header('Location: dashboard.php?error=No authorization code received from eBay');
}
exit();
?>