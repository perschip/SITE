<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>eBay Listings - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid py-4">
        <h1>eBay Listings Debug</h1>
        
        <?php
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        
        try {
            session_start();
            require_once '../config.php';
            
            // Check if logged in
            if (!isset($_SESSION['admin_id'])) {
                echo '<div class="alert alert-danger">Please log in first</div>';
                echo '<a href="login.php" class="btn btn-primary">Go to Login</a>';
                exit();
            }
            
            echo '<div class="alert alert-success">Successfully connected</div>';
            
            // Get simple listing count
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM ebay_listings");
            $count = $stmt->fetch()['count'];
            
            echo "<p>Total listings: {$count}</p>";
            
            // Get first 5 listings
            $stmt = $pdo->query("SELECT * FROM ebay_listings LIMIT 5");
            $listings = $stmt->fetchAll();
            
            if (!empty($listings)) {
                echo '<table class="table">';
                echo '<thead><tr><th>ID</th><th>Title</th><th>Price</th><th>Status</th></tr></thead>';
                echo '<tbody>';
                foreach ($listings as $listing) {
                    echo "<tr>";
                    echo "<td>" . $listing['id'] . "</td>";
                    echo "<td>" . htmlspecialchars($listing['title']) . "</td>";
                    echo "<td>$" . number_format($listing['price'], 2) . "</td>";
                    echo "<td>" . $listing['status'] . "</td>";
                    echo "</tr>";
                }
                echo '</tbody></table>';
            } else {
                echo '<p>No listings found</p>';
            }
            
        } catch (Exception $e) {
            echo '<div class="alert alert-danger">';
            echo '<h4>Error:</h4>';
            echo '<p>' . $e->getMessage() . '</p>';
            echo '<p>File: ' . $e->getFile() . '</p>';
            echo '<p>Line: ' . $e->getLine() . '</p>';
            echo '</div>';
        }
        ?>
        
        <div class="mt-4">
            <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
            <a href="listings.php" class="btn btn-primary">Try Full Listings Page</a>
        </div>
    </div>
</body>
</html>