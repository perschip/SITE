<?php
// test-sync.php - Test eBay sync functionality

session_start();
require_once 'config.php';
require_once 'classes/EbayAPI.php';

header('Content-Type: text/html');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test eBay Sync</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3>Test eBay Sync</h3>
                    </div>
                    <div class="card-body">
                        <?php
                        try {
                            echo "<h5>Testing eBay Sync...</h5>";
                            
                            $ebayAPI = new EbayAPI();
                            
                            echo "<div class='mt-3'>";
                            echo "<strong>1. Testing API Connection:</strong><br>";
                            
                            // Test client credentials
                            $tokenResult = $ebayAPI->getAccessToken();
                            if ($tokenResult) {
                                echo "<span class='text-success'>✓ Successfully obtained access token</span><br>";
                            } else {
                                echo "<span class='text-danger'>✗ Failed to obtain access token</span><br>";
                            }
                            echo "</div>";
                            
                            echo "<div class='mt-3'>";
                            echo "<strong>2. Current Listings Count:</strong><br>";
                            $stmt = $pdo->query("SELECT COUNT(*) as count FROM ebay_listings");
                            $before = $stmt->fetch()['count'];
                            echo "Listings before sync: {$before}<br>";
                            echo "</div>";
                            
                            echo "<div class='mt-3'>";
                            echo "<strong>3. Running Sync:</strong><br>";
                            $result = $ebayAPI->updateLocalListings();
                            
                            if ($result) {
                                echo "<span class='text-success'>✓ Sync completed successfully</span><br>";
                                
                                $stmt = $pdo->query("SELECT COUNT(*) as count FROM ebay_listings");
                                $after = $stmt->fetch()['count'];
                                echo "Listings after sync: {$after}<br>";
                                
                                $difference = $after - $before;
                                if ($difference > 0) {
                                    echo "<span class='text-success'>Added {$difference} new listings</span>";
                                } elseif ($difference < 0) {
                                    echo "<span class='text-warning'>Removed " . abs($difference) . " listings</span>";
                                } else {
                                    echo "<span class='text-info'>No changes detected</span>";
                                }
                            } else {
                                echo "<span class='text-danger'>✗ Sync failed</span><br>";
                            }
                            echo "</div>";
                            
                            echo "<div class='mt-3'>";
                            echo "<strong>4. Testing AJAX Endpoint:</strong><br>";
                            ?>
                            <button onclick="testAjaxSync()" class="btn btn-primary">Test AJAX Sync</button>
                            <div id="ajax-result" class="mt-2"></div>
                            
                        } catch (Exception $e) {
                            echo '<div class="alert alert-danger mt-3">';
                            echo '<h4>Error:</h4>';
                            echo '<p>' . $e->getMessage() . '</p>';
                            echo '<p>File: ' . $e->getFile() . '</p>';
                            echo '<p>Line: ' . $e->getLine() . '</p>';
                            echo '</div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        async function testAjaxSync() {
            const resultDiv = document.getElementById('ajax-result');
            resultDiv.innerHTML = 'Testing...';
            
            try {
                const response = await fetch('/admin/sync-ebay.php');
                const data = await response.json();
                
                if (data.success) {
                    resultDiv.innerHTML = '<div class="alert alert-success">AJAX sync successful!</div>';
                } else {
                    resultDiv.innerHTML = '<div class="alert alert-danger">AJAX sync failed: ' + (data.message || data.error) + '</div>';
                }
            } catch (error) {
                resultDiv.innerHTML = '<div class="alert alert-danger">AJAX error: ' + error.message + '</div>';
            }
        }
    </script>
</body>
</html>