<?php
// api/stream-status.php

header('Content-Type: application/json');

require_once '../config.php';

try {
    // Check if stream is live
    $stmt = $pdo->prepare("
        SELECT * FROM stream_schedule 
        WHERE is_live = TRUE 
        ORDER BY scheduled_time DESC 
        LIMIT 1
    ");
    $stmt->execute();
    $liveStream = $stmt->fetch();
    
    // Get next scheduled stream
    $stmt = $pdo->prepare("
        SELECT * FROM stream_schedule 
        WHERE scheduled_time > NOW() 
        ORDER BY scheduled_time ASC 
        LIMIT 1
    ");
    $stmt->execute();
    $nextStream = $stmt->fetch();
    
    $response = [
        'success' => true,
        'is_live' => $liveStream ? true : false,
        'stream_url' => $liveStream ? "https://whatnot.com/user/" . WHATNOT_CHANNEL : null,
    ];
    
    // Format next stream data properly
    if ($nextStream) {
        $response['next_stream'] = [
            'title' => $nextStream['title'],
            'time' => $nextStream['scheduled_time'],
            'description' => $nextStream['description']
        ];
    } else {
        $response['next_stream'] = null;
    }
    
    echo json_encode($response);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to check stream status',
        'message' => $e->getMessage()
    ]);
}
?>