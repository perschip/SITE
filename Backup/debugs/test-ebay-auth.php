<?php
// test-ebay-auth.php - Test eBay authentication

session_start();
require_once 'config.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>eBay Authentication Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3>eBay Authentication Test</h3>
                    </div>
                    <div class="card-body">
                        <?php
                        echo "<h5>Current Configuration:</h5>";
                        echo "<ul>";
                        echo "<li><strong>Client ID:</strong> " . EBAY_CLIENT_ID . "</li>";
                        echo "<li><strong>Site URL:</strong> " . SITE_URL . "</li>";
                        echo "<li><strong>Redirect URI:</strong> " . SITE_URL . "/admin/ebay-callback.php</li>";
                        echo "</ul>";
                        
                        $authUrl = "https://auth.ebay.com/oauth2/authorize?" . http_build_query([
                            'client_id' => EBAY_CLIENT_ID,
                            'response_type' => 'code',
                            'redirect_uri' => SITE_URL . '/admin/ebay-callback.php',
                            'scope' => 'https://api.ebay.com/oauth/api_scope'
                        ]);
                        
                        echo "<div class='mt-4'>";
                        echo "<h5>Authentication URL:</h5>";
                        echo "<p class='text-break'>" . htmlspecialchars($authUrl) . "</p>";
                        echo "</div>";
                        
                        // Show any error from URL parameters
                        if (isset($_GET['error'])) {
                            echo "<div class='alert alert-danger mt-4'>";
                            echo "<h5>Error Received:</h5>";
                            echo "<strong>Error:</strong> " . htmlspecialchars($_GET['error']) . "<br>";
                            if (isset($_GET['error_description'])) {
                                echo "<strong>Description:</strong> " . htmlspecialchars($_GET['error_description']);
                            }
                            echo "</div>";
                        }
                        
                        // Show authorization code if present
                        if (isset($_GET['code'])) {
                            echo "<div class='alert alert-success mt-4'>";
                            echo "<h5>Authorization Code Received:</h5>";
                            echo "<code>" . htmlspecialchars($_GET['code']) . "</code>";
                            echo "</div>";
                        }
                        ?>
                        
                        <div class="mt-4">
                            <a href="<?php echo $authUrl; ?>" class="btn btn-primary">
                                Start eBay Authentication
                            </a>
                            <a href="debug-ebay-oauth.php" class="btn btn-info">
                                Debug OAuth Parameters
                            </a>
                        </div>
                        
                        <div class="mt-4">
                            <h5>Troubleshooting Steps:</h5>
                            <ol>
                                <li>Make sure your eBay Developer account has the correct redirect URI</li>
                                <li>Verify you're using production keys, not sandbox</li>
                                <li>Ensure SITE_URL uses HTTPS for production</li>
                                <li>Check that Client ID matches exactly</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>