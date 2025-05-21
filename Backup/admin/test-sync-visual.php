<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sync Test Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .log-output { font-family: monospace; white-space: pre-wrap; background: #f8f9fa; padding: 15px; border-radius: 5px; }
        .step { margin: 10px 0; padding: 10px; border-left: 3px solid #ccc; }
        .step.success { border-color: #28a745; }
        .step.error { border-color: #dc3545; }
    </style>
</head>
<body>
    <div class="container-fluid py-4">
        <h1 class="mb-4">eBay Sync Test Dashboard</h1>
        
        <div class="row mb-4">
            <div class="col-md-6">
                <button class="btn btn-primary" onclick="testSync()">
                    <i class="fas fa-sync me-2"></i>Test Sync
                </button>
                <button class="btn btn-secondary" onclick="clearLogs()">
                    <i class="fas fa-trash me-2"></i>Clear Logs
                </button>
            </div>
        </div>
        
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Test Results</h5>
                    </div>
                    <div class="card-body">
                        <div id="test-results" class="log-output">
                            Click "Test Sync" to start testing...
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <button class="btn btn-info mb-2" onclick="testApi()">Test API Endpoint</button>
                        <button class="btn btn-warning mb-2" onclick="testRealSync()">Test Real Sync</button>
                        <button class="btn btn-success mb-2" onclick="testDashboardSync()">Test Dashboard Sync</button>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Debug Information</h5>
                    </div>
                    <div class="card-body" id="debug-info">
                        No debug info yet
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function log(message, type = 'info') {
            const results = document.getElementById('test-results');
            const timestamp = new Date().toLocaleTimeString();
            const icon = type === 'success' ? 'check' : type === 'error' ? 'times' : 'info';
            const color = type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info';
            
            results.innerHTML += `<div class="step ${type}">
                <i class="fas fa-${icon} text-${color} me-2"></i>
                [${timestamp}] ${message}
            </div>`;
        }
        
        function clearLogs() {
            document.getElementById('test-results').innerHTML = '';
        }
        
        async function testSync() {
            clearLogs();
            log('Starting eBay sync test...');
            
            try {
                log('Testing debug sync endpoint...');
                const response = await fetch('debug-sync.php');
                const data = await response.json();
                
                if (data.success) {
                    log('Sync successful!', 'success');
                    log(`Message: ${data.message}`, 'success');
                    if (data.listings_count) {
                        log(`Active listings: ${data.listings_count}`, 'success');
                    }
                } else {
                    log('Sync failed!', 'error');
                    log(`Error: ${data.error}`, 'error');
                    log(`Message: ${data.message}`, 'error');
                }
                
                // Show debug info
                if (data.debug) {
                    document.getElementById('debug-info').innerHTML = '<pre>' + JSON.stringify(data.debug, null, 2) + '</pre>';
                }
                
            } catch (error) {
                log('Error during test: ' + error.message, 'error');
            }
        }
        
        async function testApi() {
            log('Testing API endpoint directly...');
            
            try {
                const response = await fetch('sync-ebay.php');
                const data = await response.json();
                
                log('API response received');
                log('Response: ' + JSON.stringify(data, null, 2));
                
            } catch (error) {
                log('API test failed: ' + error.message, 'error');
            }
        }
        
        async function testRealSync() {
            log('Testing real sync process...');
            
            try {
                const response = await fetch('/test-simple-sync.php');
                const text = await response.text();
                
                log('Sync test completed');
                log('Output: ' + text);
                
            } catch (error) {
                log('Real sync test failed: ' + error.message, 'error');
            }
        }
        
        async function testDashboardSync() {
            log('Testing dashboard sync button...');
            
            try {
                // Simulate the dashboard sync function
                syncEbayListings();
                
            } catch (error) {
                log('Dashboard sync test failed: ' + error.message, 'error');
            }
        }
        
        // Copy of dashboard sync function for testing
        async function syncEbayListings() {
            log('Simulating dashboard sync...');
            
            try {
                const response = await fetch('sync-ebay.php');
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                
                if (data.success) {
                    log('Dashboard sync successful!', 'success');
                    log(`Message: ${data.message}`, 'success');
                } else {
                    log('Dashboard sync failed!', 'error');
                    log(`Error: ${data.error || data.message}`, 'error');
                }
            } catch (error) {
                log('Dashboard sync error: ' + error.message, 'error');
            }
        }
    </script>
</body>
</html>