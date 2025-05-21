<?php
// admin/ebay-settings.php
$pageTitle = 'eBay Settings';
require_once 'includes/header.php';

$message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appId = $_POST['app_id'] ?? '';
    $devId = $_POST['dev_id'] ?? '';
    $certId = $_POST['cert_id'] ?? '';
    $oauthToken = $_POST['oauth_token'] ?? '';
    
    // Update config file
    $configFile = '../config/config.php';
    $configContent = file_get_contents($configFile);
    
    // Replace eBay API settings
    $configContent = preg_replace('/define\(\'EBAY_APP_ID\', \'.*?\'\);/', "define('EBAY_APP_ID', '$appId');", $configContent);
    $configContent = preg_replace('/define\(\'EBAY_DEV_ID\', \'.*?\'\);/', "define('EBAY_DEV_ID', '$devId');", $configContent);
    $configContent = preg_replace('/define\(\'EBAY_CERT_ID\', \'.*?\'\);/', "define('EBAY_CERT_ID', '$certId');", $configContent);
    $configContent = preg_replace('/define\(\'EBAY_OAUTH_TOKEN\', \'.*?\'\);/', "define('EBAY_OAUTH_TOKEN', '$oauthToken');", $configContent);
    
    if (file_put_contents($configFile, $configContent)) {
        $message = '<div class="success-message">eBay settings saved successfully!</div>';
    } else {
        $message = '<div class="error-message">Error saving eBay settings. Check file permissions.</div>';
    }
}

// Get current settings
$appId = defined('EBAY_APP_ID') ? EBAY_APP_ID : '';
$devId = defined('EBAY_DEV_ID') ? EBAY_DEV_ID : '';
$certId = defined('EBAY_CERT_ID') ? EBAY_CERT_ID : '';
$oauthToken = defined('EBAY_OAUTH_TOKEN') ? EBAY_OAUTH_TOKEN : '';
?>

<div class="admin-container">
    <h1>eBay API Settings</h1>
    
    <?php echo $message; ?>
    
    <form action="ebay-settings.php" method="post">
        <div class="form-group">
            <label for="app_id">App ID (Client ID)</label>
            <input type="text" id="app_id" name="app_id" value="<?php echo $appId; ?>">
        </div>
        
        <div class="form-group">
            <label for="dev_id">Dev ID</label>
            <input type="text" id="dev_id" name="dev_id" value="<?php echo $devId; ?>">
        </div>
        
        <div class="form-group">
            <label for="cert_id">Cert ID (Client Secret)</label>
            <input type="text" id="cert_id" name="cert_id" value="<?php echo $certId; ?>">
        </div>
        
        <div class="form-group">
            <label for="oauth_token">OAuth Token</label>
            <textarea id="oauth_token" name="oauth_token" rows="5"><?php echo $oauthToken; ?></textarea>
            <small>Note: OAuth tokens expire periodically. You may need to update this token regularly.</small>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn">Save Settings</button>
        </div>
    </form>
    
    <div class="api-test">
        <h2>Test API Connection</h2>
        <p>Click the button below to test your eBay API connection:</p>
        <button id="test-api" class="btn btn-secondary">Test Connection</button>
        <div id="test-result" class="message" style="display: none;"></div>
    </div>
</div>

<script>
document.getElementById('test-api').addEventListener('click', function() {
    const resultDiv = document.getElementById('test-result');
    resultDiv.innerHTML = 'Testing connection...';
    resultDiv.style.display = 'block';
    resultDiv.className = 'message';
    
    fetch('../api/ebay-test.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                resultDiv.innerHTML = 'Connection successful! API is working properly.';
                resultDiv.className = 'message success-message';
            } else {
                resultDiv.innerHTML = 'Connection failed: ' + data.error;
                resultDiv.className = 'message error-message';
            }
        })
        .catch(error => {
            resultDiv.innerHTML = 'Error testing connection: ' + error;
            resultDiv.className = 'message error-message';
        });
});
</script>

<?php require_once 'includes/footer.php'; ?>