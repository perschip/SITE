<?php
// admin/debug-sync.php - Debug version of sync endpoint

session_start();
require_once '../config.php';
require_once '../classes/EbayAPI.php';

// Enable error display for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

requireLogin();

header('Content-Type: application/json');

$debug = [];
$debug['timestamp'] = date('Y-m-d H:i:s');
$debug['session'] = [
    'admin_id' => $_SESSION['admin_id'] ?? 'not set',
    'logged_in' => isset($_SESSION['admin_id'])
];

try {
    $debug['step1'] = 'Creating EbayAPI instance';
    $ebayAPI = new EbayAPI();
    
    $debug['step2'] = 'Calling updateLocalListings';
    $result = $ebayAPI->updateLocalListings();
    
    $debug['step3'] = 'Result received: ' . ($result ? 'true' : 'false');
    
    if ($result) {
        $debug['step4'] = 'Updating last sync time';
        $stmt = $pdo->prepare("INSERT INTO site_settings (setting_key, setting_value) VALUES ('last_ebay_sync', NOW()) ON DUPLICATE KEY UPDATE setting_value = NOW()");
        $stmt->execute();
        
        $debug['step5'] = 'Getting listings count';
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM ebay_listings WHERE status = 'active'");
        $count = $stmt->fetch()['count'];
        $debug['active_listings'] = $count;
        
        echo json_encode([
            'success' => true,
            'message' => 'eBay listings synchronized successfully',
            'timestamp' => date('Y-m-d H:i:s'),
            'listings_count' => $count,
            'debug' => $debug
        ]);
    } else {
        $debug['error'] = 'updateLocalListings returned false';
        
        echo json_encode([
            'success' => false,
            'error' => 'Failed to sync eBay listings',
            'message' => 'Check debug information for details',
            'debug' => $debug
        ]);
    }
} catch (Exception $e) {
    $debug['exception'] = [
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ];
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Exception occurred',
        'message' => $e->getMessage(),
        'debug' => $debug
    ]);
}

// Also write to a separate debug log file
$debugLog = __DIR__ . '/../logs/debug-sync.log';
$logEntry = date('Y-m-d H:i:s') . " - " . json_encode($debug, JSON_PRETTY_PRINT) . "\n\n";
file_put_contents($debugLog, $logEntry, FILE_APPEND | LOCK_EX);
?>