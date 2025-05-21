<?php
// api/whatnot-status.php
header('Content-Type: application/json');
// Check if the user is live on Whatnot
function checkWhatnotStatus() {
    $username = 'tscardbreaks'; // Hardcoded for simplicity
    
    try {
        // First, return dummy data for testing
        return [
            'isLive' => false, // Change to true to test live status
            'username' => $username,
            'streamTitle' => 'Live Sports Card Break!',
            'viewerCount' => rand(10, 100)
        ];
        
        // Real implementation would check Whatnot's website or API
        // This is commented out until we can get it working properly
        
        $url = "https://www.whatnot.com/user/{$username}";
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false
        ]);
        
        $response = curl_exec($ch);
        
        if (curl_errno($ch)) {
            throw new Exception('cURL Error: ' . curl_error($ch));
        }
        
        curl_close($ch);
        
        // Look for indicators that the user is live
        $isLive = (strpos($response, 'LIVE') !== false || 
                  strpos($response, 'is live') !== false || 
                  strpos($response, 'livestream') !== false);
        
        if ($isLive) {
            // Extract stream title and viewer count
            preg_match('/<title>(.*?)<\/title>/i', $response, $titleMatches);
            $streamTitle = '';
            if (!empty($titleMatches[1])) {
                $streamTitle = str_replace(' | Whatnot', '', $titleMatches[1]);
                $streamTitle = str_replace($username, '', $streamTitle);
                $streamTitle = trim($streamTitle);
            }
            
            if (empty($streamTitle)) {
                $streamTitle = 'Live Sports Card Break';
            }
            
            // Extract viewer count
            preg_match('/(\d+)\s*viewers?/i', $response, $viewerMatches);
            $viewerCount = !empty($viewerMatches[1]) ? $viewerMatches[1] : rand(10, 100);
            
            return [
                'isLive' => true,
                'username' => $username,
                'streamTitle' => $streamTitle,
                'viewerCount' => $viewerCount
            ];
        } else {
            return [
                'isLive' => false,
                'username' => $username,
                'streamTitle' => '',
                'viewerCount' => 0
            ];
        }
        
    } catch (Exception $e) {
        return [
            'error' => $e->getMessage(),
            'isLive' => false,
            'username' => $username
        ];
    }
}

// Return JSON response
echo json_encode(checkWhatnotStatus());
?>