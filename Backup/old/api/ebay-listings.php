<?php
// api/ebay-listings.php
header('Content-Type: application/json');
/*header('Access-Control-Allow-Origin: *'); // Allow cross-origin requests

// Always return static data for now
$listings = [
    [
        'title' => "2023 Panini Prizm Basketball Card - Rookie Edition",
        'price' => "49.99",
        'image' => 'images/placeholder.jpg',
        'viewItemURL' => "https://www.ebay.com/str/tristatecardsnj",
        'timeLeft' => "2 days 5 hours",
        'bids' => "12"
    ],
    [
        'title' => "2023 Topps Chrome Baseball Card Pack - Factory Sealed",
        'price' => "29.99",
        'image' => 'images/placeholder.jpg',
        'viewItemURL' => "https://www.ebay.com/str/tristatecardsnj",
        'timeLeft' => "1 day 12 hours",
        'bids' => "7"
    ],
    [
        'title' => "Patrick Mahomes Signed Rookie Card - PSA Graded",
        'price' => "199.99",
        'image' => 'images/placeholder.jpg',
        'viewItemURL' => "https://www.ebay.com/str/tristatecardsnj",
        'timeLeft' => "3 days 4 hours",
        'bids' => "22"
    ],
    [
        'title' => "2023 NFL Panini Mosaic Football Card Box Set",
        'price' => "149.99",
        'image' => 'images/placeholder.jpg',
        'viewItemURL' => "https://www.ebay.com/str/tristatecardsnj",
        'timeLeft' => "2 days 7 hours",
        'bids' => "15"
    ]
];

echo json_encode($listings); */

// Function to get eBay listings
function getEbayListings() {
    $sellerId = 'tristatecardsnj'; // Hardcoded for simplicity
    
    try {
        // First, return dummy data for testing
        $listings = [];
        
        for ($i = 1; $i <= 8; $i++) {
            $listings[] = [
                'title' => "2023 Panini Prizm Basketball Card #$i - Rookie Edition",
                'price' => rand(5, 200) . '.' . rand(0, 99),
                'image' => 'images/placeholder.jpg',
                'viewItemURL' => "https://www.ebay.com/str/$sellerId",
                'timeLeft' => rand(1, 5) . ' days ' . rand(1, 23) . ' hours',
                'bids' => rand(0, 20)
            ];
        }
        
        return $listings;
        
        // Real implementation would use eBay API
        // This is commented out until we can get it working properly
        
        $appID = 'PaulPers-TSCBOT-PRD-c0e716bda-e070fe46';
        $oauthToken = 'v^1.1#i^1#f^0#p^1#r^0#I^3#t^H4sIAAAAAAAA/+VYe2wURRjv9QGpcBgjwYKNORb4g5Ldnd2954Y7cn0AF6/tlTsLVAnO7s62a/d2l9092oKaUoEAakywMfJIrAmKivEZJT4iEUWJNEFREmJiQDECNhhEfIM6uy2lrYRXL7GJ989lvvnmm9/vN983Mzugc1xpxfqF63/1esYX9nSCzkKPh5kASseVzJlUVDitpAAMcfD0dM7sLO4qOjnXglnV4Bchy9A1C/nas6pm8a4xSuRMjdehpVi8BrPI4m2RT8drkzxLAd4wdVsXdZXwJaqjRMgvI5GFYYkLsxInIWzVLsbM6FECBoMCKyI5HGQ4hpNk3G9ZOZTQLBtqdpRgARsggZ9kwxmW4/2AZwEVjESaCF8jMi1F17ALBYiYC5d3x5pDsF4ZKrQsZNo4CBFLxOen6+OJ6pq6zFx6SKzYgA5pG9o5a3irSpeQrxGqOXTlaSzXm0/nRBFZFkHH+mcYHpSPXwRzA/BdqSFWMBASsMwRrCjw50XK+bqZhfaVcTgWRSJl15VHmq3YHVdTFKsh3IdEe6BVh0Mkqn3OX0MOqoqsIDNK1FTGl8ZTKSKWgjk1hUGSmXRVZX2GTC2qJkWAQkxQkCCJQAjIyB8cmKY/1oDII+ap0jVJcSSzfHW6XYkwZjRSGW6IMtipXqs347Lt4Bn0i2QAc1HBcKDJWdL+NczZLZqzqiiLZfC5zavrPzjatk1FyNloMMLIDlcgvNKGoUjEyE43EweSp92KEi22bfA03dbWRrVxlG420ywADL2kNpkWW1AWEq6vU+uOv3L1AaTiUhFxEWN/3u4wMJZ2nKkYgNZMxAJsgAtzA7oPhxUbaf2XYQhneng95Ks+mIAEAJJDcpARZBZF8lEfsYEUpR0cSIAdZBaarcg2VCgiUsR5lssiU5F4LiCzXFhGpBSMyKQ/IsukEJCCJCMjBBASBDES/v+UybUmehqJJrLzlOl5yvIVNYmOuuqGBZWZRK1kBlJViAspsKFujpCiI6GklaxbYFSbq5R4ZUP0WmvhsuSrVAUrk8Hz50sAp9bzI8JC3bKRNCp6aVE3UEpXFbFjbC0wZ0opaNodaaSq2DAqknHDSORrp84TvevaJG6MdT7Pp//kbLosK8tJ2LHFyhlv4QDQUCjn9KFEPUvrEF87aKfWsXm5i3pUvBV8Zx1TrDHJfraK1H/ZpFzKlLVSpExk6TkT37Opeuf2ldFbkYZPM9vUVRWZjcyoqzmbzdlQUNFYK+s8JLgCx9hRy4T8gXCYC7JgVLxE9yBdPta2pPxtxMXzrvNCTQ//uI8VuD+my7MXdHneK/R4wFwwi5kBpo8ruqu4aOI0S7ERpUCZspRmDX+zmohqRR0GVMzCWwsOTkpKaxYmf+4UcrsXn5sXLvAOeVvoWQbKBl8XSouYCUOeGkD5pZ4S5ubbvGwA+Nkwy/kBC5rAjEu9xcyU4snlrVt6V6y+8P60b/Rb4OtvQnb3U7OBd9DJ4ykpKO7yFMzaIGw2Pjt4bM+5M28s2fpB+zvR3o9aspnNP5C/sY/88v2+83+8QpV9fBb13X1gcu/4wO3rylduf7ly99Ijyyed9XZdyHk3b/2r9sHHdyw5sX/X0Z6lmUN1xYsmzdhYsbf771cDv2/zHdq+7/47kqt2lj2z9oS0ZtmucJ/36cPHJ1QcqNXeuse7Ntn8LJpqP3bnpqP7T5V4j59qn0pvfGFd39fqmjNfzgTfPfbVnk/PrX+RvnfW9Nmnt3++5fz4P5/vfuDoujmbXoNPlC8uzezoK/WVTCmbeKyipfvAQ7GdD/c+eqT38E87P5z45LfsSf9zp+kFhzd80vZFw+Lp9N7aPZ2rhZrmxm3dTW//eNPpl95tjrT3r+U/tIbKcfURAAA=';
        
        // eBay API endpoint - use the Finding API
        $endpoint = 'https://api.ebay.com/buy/browse/v1/item_summary/search';
        
        // Parameters to search for active listings by seller
        $params = [
            'q' => 'sports cards',
            'filter' => "sellers:{$sellerId}",
            'limit' => 12
        ];
        
        // Create curl resource
        $ch = curl_init();
        
        // Set curl options
        curl_setopt_array($ch, [
            CURLOPT_URL => $endpoint . '?' . http_build_query($params),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $oauthToken,
                'X-EBAY-C-MARKETPLACE-ID: EBAY_US',
                'Content-Type: application/json'
            ],
            CURLOPT_SSL_VERIFYPEER => false
        ]);
        
        // Execute curl and get response
        $response = curl_exec($ch);
        
        if (curl_errno($ch)) {
            throw new Exception('cURL Error: ' . curl_error($ch));
        }
        
        // Get HTTP status code
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        curl_close($ch);
        
        // Check for HTTP error
        if ($httpCode >= 400) {
            throw new Exception('HTTP Error: ' . $httpCode);
        }
        
        // Decode JSON response
        $data = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('JSON Error: ' . json_last_error_msg());
        }
        
        // Extract listings
        $listings = [];
        
        if (isset($data['itemSummaries']) && is_array($data['itemSummaries'])) {
            foreach ($data['itemSummaries'] as $item) {
                $listings[] = [
                    'title' => $item['title'] ?? 'Sports Card',
                    'price' => isset($item['price']['value']) ? $item['price']['value'] : '0.00',
                    'image' => isset($item['image']['imageUrl']) ? $item['image']['imageUrl'] : 'images/placeholder.jpg',
                    'viewItemURL' => $item['itemWebUrl'] ?? "https://www.ebay.com/str/$sellerId",
                    'timeLeft' => isset($item['itemEndDate']) ? formatTimeRemaining($item['itemEndDate']) : 'Ending soon',
                    'bids' => isset($item['bidCount']) ? $item['bidCount'] : '0'
                ];
            }
        }
        
        return $listings;
        
    } catch (Exception $e) {
        return ['error' => $e->getMessage()];
    }
}

// Helper function to format time remaining
function formatTimeRemaining($endDate) {
    $end = new DateTime($endDate);
    $now = new DateTime();
    $interval = $now->diff($end);
    
    if ($interval->days > 0) {
        return $interval->days . ' days ' . $interval->h . ' hours';
    } else {
        return $interval->h . ' hours ' . $interval->i . ' minutes';
    }
}

// Return JSON response
echo json_encode(getEbayListings());
?>