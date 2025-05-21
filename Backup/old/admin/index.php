<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Tristate Cards</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Tristate Cards Admin</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#ebay-settings">eBay Settings</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#whatnot-settings">Whatnot Settings</a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../" target="_blank">View Site</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <h1 class="mb-4">Admin Dashboard</h1>
        
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title">Whatnot Status</h5>
                        <p class="card-text" id="admin-whatnot-status">Checking...</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">eBay Listings</h5>
                        <p class="card-text" id="admin-ebay-count">Checking...</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5 class="card-title">Website Status</h5>
                        <p class="card-text">Online</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- eBay Settings Section -->
        <div class="card mb-4" id="ebay-settings">
            <div class="card-header">
                <h2 class="h5 mb-0">eBay API Settings</h2>
            </div>
            <div class="card-body">
                <form id="ebay-settings-form">
                    <div class="mb-3">
                        <label for="ebay-seller-id" class="form-label">eBay Seller ID</label>
                        <input type="text" class="form-control" id="ebay-seller-id" name="ebay_seller_id" value="tristatecards">
                        <div class="form-text">Your eBay seller ID or store name.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="ebay-app-id" class="form-label">App ID (Client ID)</label>
                        <input type="text" class="form-control" id="ebay-app-id" name="ebay_app_id" value="PaulPers-TSCBOT-PRD-c0e716bda-e070fe46">
                    </div>
                    
                    <div class="mb-3">
                        <label for="ebay-dev-id" class="form-label">Dev ID</label>
                        <input type="text" class="form-control" id="ebay-dev-id" name="ebay_dev_id" value="25a2a87a-6273-4022-855d-66da51ab543e">
                    </div>
                    
                    <div class="mb-3">
                        <label for="ebay-cert-id" class="form-label">Cert ID (Client Secret)</label>
                        <input type="text" class="form-control" id="ebay-cert-id" name="ebay_cert_id" value="PRD-0e716bdad618-d1f5-45d0-8ff1-53af">
                    </div>
                    
                    <div class="mb-3">
                        <label for="ebay-oauth-token" class="form-label">OAuth Token</label>
                        <textarea class="form-control" id="ebay-oauth-token" name="ebay_oauth_token" rows="3">v^1.1#i^1#f^0#p^1#r^0#I^3#t^H4sIAAAAAAAA/+VYe2wURRjv9QGpcBgjwYKNORb4g5Ldnd2954Y7cn0AF6/tlTsLVAnO7s62a/d2l9092oKaUoEAakywMfJIrAmKivEZJT4iEUWJNEFREmJiQDECNhhEfIM6uy2lrYRXL7GJ989lvvnmm9/vN983Mzugc1xpxfqF63/1esYX9nSCzkKPh5kASseVzJlUVDitpAAMcfD0dM7sLO4qOjnXglnV4Bchy9A1C/nas6pm8a4xSuRMjdehpVi8BrPI4m2RT8drkzxLAd4wdVsXdZXwJaqjRMgvI5GFYYkLsxInIWzVLsbM6FECBoMCKyI5HGQ4hpNk3G9ZOZTQLBtqdpRgARsggZ9kwxmW4/2AZwEVjESaCF8jMi1F17ALBYiYC5d3x5pDsF4ZKrQsZNo4CBFLxOen6+OJ6pq6zFx6SKzYgA5pG9o5a3irSpeQrxGqOXTlaSzXm0/nRBFZFkHH+mcYHpSPXwRzA/BdqSFWMBASsMwRrCjw50XK+bqZhfaVcTgWRSJl15VHmq3YHVdTFKsh3IdEe6BVh0Mkqn3OX0MOqoqsIDNK1FTGl8ZTKSKWgjk1hUGSmXRVZX2GTC2qJkWAQkxQkCCJQAjIyB8cmKY/1oDII+ap0jVJcSSzfHW6XYkwZjRSGW6IMtipXqs347Lt4Bn0i2QAc1HBcKDJWdL+NczZLZqzqiiLZfC5zavrPzjatk1FyNloMMLIDlcgvNKGoUjEyE43EweSp92KEi22bfA03dbWRrVxlG420ywADL2kNpkWW1AWEq6vU+uOv3L1AaTiUhFxEWN/3u4wMJZ2nKkYgNZMxAJsgAtzA7oPhxUbaf2XYQhneng95Ks+mIAEAJJDcpARZBZF8lEfsYEUpR0cSIAdZBaarcg2VCgiUsR5lssiU5F4LiCzXFhGpBSMyKQ/IsukEJCCJCMjBBASBDES/v+UybUmehqJJrLzlOl5yvIVNYmOuuqGBZWZRK1kBlJViAspsKFujpCiI6GklaxbYFSbq5R4ZUP0WmvhsuSrVAUrk8Hz50sAp9bzI8JC3bKRNCp6aVE3UEpXFbFjbC0wZ0opaNodaaSq2DAqknHDSORrp84TvevaJG6MdT7Pp//kbLosK8tJ2LHFyhlv4QDQUCjn9KFEPUvrEF87aKfWsXm5i3pUvBV8Zx1TrDHJfraK1H/ZpFzKlLVSpExk6TkT37Opeuf2ldFbkYZPM9vUVRWZjcyoqzmbzdlQUNFYK+s8JLgCx9hRy4T8gXCYC7JgVLxE9yBdPta2pPxtxMXzrvNCTQ//uI8VuD+my7MXdHneK/R4wFwwi5kBpo8ruqu4aOI0S7ERpUCZspRmDX+zmohqRR0GVMzCWwsOTkpKaxYmf+4UcrsXn5sXLvAOeVvoWQbKBl8XSouYCUOeGkD5pZ4S5ubbvGwA+Nkwy/kBC5rAjEu9xcyU4snlrVt6V6y+8P60b/Rb4OtvQnb3U7OBd9DJ4ykpKO7yFMzaIGw2Pjt4bM+5M28s2fpB+zvR3o9aspnNP5C/sY/88v2+83+8QpV9fBb13X1gcu/4wO3rylduf7ly99Ijyyed9XZdyHk3b/2r9sHHdyw5sX/X0Z6lmUN1xYsmzdhYsbf771cDv2/zHdq+7/47kqt2lj2z9oS0ZtmucJ/36cPHJ1QcqNXeuse7Ntn8LJpqP3bnpqP7T5V4j59qn0pvfGFd39fqmjNfzgTfPfbVnk/PrX+RvnfW9Nmnt3++5fz4P5/vfuDoujmbXoNPlC8uzezoK/WVTCmbeKyipfvAQ7GdD/c+eqT38E87P5z45LfsSf9zp+kFhzd80vZFw+Lp9N7aPZ2rhZrmxm3dTW//eNPpl95tjrT3r+U/tIbKcfURAAA=</textarea>
                        <div class="form-text">Note: OAuth tokens expire, you may need to update this periodically.</div>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="button" class="btn btn-primary" id="test-ebay-api">Test API Connection</button>
                        <button type="submit" class="btn btn-success">Save Settings</button>
                    </div>
                </form>
                
                <div id="ebay-test-result" class="mt-3" style="display: none;"></div>
            </div>
        </div>
        
        <!-- Whatnot Settings Section -->
        <div class="card" id="whatnot-settings">
            <div class="card-header">
                <h2 class="h5 mb-0">Whatnot Settings</h2>
            </div>
            <div class="card-body">
                <form id="whatnot-settings-form">
                    <div class="mb-3">
                        <label for="whatnot-username" class="form-label">Whatnot Username</label>
                        <input type="text" class="form-control" id="whatnot-username" name="whatnot_username" value="tscardbreaks">
                        <div class="form-text">Your Whatnot username for checking live status.</div>
                    </div>
                    
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="show-whatnot-status" checked>
                        <label class="form-check-label" for="show-whatnot-status">Show Whatnot Status on Homepage</label>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="button" class="btn btn-primary" id="test-whatnot">Test Whatnot Status</button>
                        <button type="submit" class="btn btn-success">Save Settings</button>
                    </div>
                </form>
                
                <div id="whatnot-test-result" class="mt-3" style="display: none;"></div>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-white text-center py-3 mt-5">
        <div class="container">
            <p class="mb-0">&copy; <?php echo date('Y'); ?> Tristate Cards Admin Panel</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Check admin dashboard data
            function checkWhatnotStatus() {
                $.get('../api/whatnot-status.php', function(data) {
                    if (data.isLive) {
                        $('#admin-whatnot-status').html('<span class="badge bg-danger">LIVE NOW</span> ' + data.streamTitle);
                    } else {
                        $('#admin-whatnot-status').text('Currently Offline');
                    }
                });
            }
            
            function checkEbayListings() {
                $.get('../api/ebay-listings.php', function(data) {
                    if (Array.isArray(data)) {
                        $('#admin-ebay-count').text(data.length + ' Active Listings');
                    } else {
                        $('#admin-ebay-count').text('Error fetching listings');
                    }
                });
            }
            
            // Test eBay API connection
            $('#test-ebay-api').click(function() {
                const resultDiv = $('#ebay-test-result');
                resultDiv.html('<div class="alert alert-info">Testing eBay API connection...</div>').show();
                
                $.get('../api/ebay-listings.php', function(data) {
                    if (data.error) {
                        resultDiv.html('<div class="alert alert-danger">Error: ' + data.error + '</div>');
                    } else if (Array.isArray(data) && data.length > 0) {
                        resultDiv.html('<div class="alert alert-success">Connection successful! Found ' + data.length + ' listings.</div>');
                    } else {
                        resultDiv.html('<div class="alert alert-warning">Connected, but no listings found.</div>');
                    }
                }).fail(function() {
                    resultDiv.html('<div class="alert alert-danger">Connection failed. Check your API credentials and server configuration.</div>');
                });
            });
            
            // Test Whatnot connection
            $('#test-whatnot').click(function() {
                const resultDiv = $('#whatnot-test-result');
                resultDiv.html('<div class="alert alert-info">Checking Whatnot status...</div>').show();
                
                $.get('../api/whatnot-status.php', function(data) {
                    if (data.error) {
                        resultDiv.html('<div class="alert alert-danger">Error: ' + data.error + '</div>');
                    } else if (data.isLive) {
                        resultDiv.html('<div class="alert alert-success">Connection successful! You are currently LIVE with ' + data.viewerCount + ' viewers.</div>');
                    } else {
                        resultDiv.html('<div class="alert alert-success">Connection successful! You are currently offline.</div>');
                    }
                }).fail(function() {
                    resultDiv.html('<div class="alert alert-danger">Failed to check Whatnot status. Check your server configuration.</div>');
                });
            });
            
            // Form submissions
            $('#ebay-settings-form').submit(function(e) {
                e.preventDefault();
                alert('Settings saved! (This would save to a database in a production environment)');
            });
            
            $('#whatnot-settings-form').submit(function(e) {
                e.preventDefault();
                alert('Settings saved! (This would save to a database in a production environment)');
            });
            
            // Initialize
            checkWhatnotStatus();
            checkEbayListings();
        });
    </script>
</body>
</html>