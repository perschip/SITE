<?php
// api/listings.php

header('Content-Type: application/json');

require_once '../config.php';
require_once '../classes/EbayAPI.php';

$ebayAPI = new EbayAPI();

// Get parameters
$featured = isset($_GET['featured']) && $_GET['featured'] === 'true';
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 12;
$offset = ($page - 1) * $perPage;

try {
    if ($featured) {
        // Get featured listings
        $listings = $ebayAPI->getFeaturedListings(6);
        $total = count($listings);
    } else {
        // Get all active listings
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as total 
            FROM ebay_listings 
            WHERE status = 'active' 
            AND (:search = '' OR title LIKE :search_like)
        ");
        $searchParam = "%{$search}%";
        $stmt->execute([
            ':search' => $search,
            ':search_like' => $searchParam
        ]);
        $totalResult = $stmt->fetch();
        $total = $totalResult ? $totalResult['total'] : 0;
        
        $stmt = $pdo->prepare("
            SELECT * FROM ebay_listings 
            WHERE status = 'active' 
            AND (:search = '' OR title LIKE :search_like)
            ORDER BY last_updated DESC 
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':search', $search, PDO::PARAM_STR);
        $stmt->bindValue(':search_like', $searchParam, PDO::PARAM_STR);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $listings = $stmt->fetchAll();
    }
    
    // Format the response
    $response = [
        'success' => true,
        'listings' => $listings,
        'pagination' => [
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'total_pages' => ceil($total / $perPage)
        ]
    ];
    
    echo json_encode($response);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to load listings',
        'message' => $e->getMessage(),
        'debug' => [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]
    ]);
}
?>