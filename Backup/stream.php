<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Stream - Tri-State Cards NJ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body class="theme-light">
    <?php
    require_once 'config.php';
    
    // Get stream status and schedule
    $stmt = $pdo->query("SELECT * FROM stream_schedule WHERE is_live = TRUE ORDER BY scheduled_time DESC LIMIT 1");
    $liveStream = $stmt->fetch();
    
    $stmt = $pdo->query("SELECT * FROM stream_schedule WHERE scheduled_time > NOW() ORDER BY scheduled_time ASC LIMIT 5");
    $upcomingStreams = $stmt->fetchAll();
    ?>
    
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="images/logo.png" alt="Tri-State Cards NJ" height="40">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="listings.php">eBay Listings</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="blog.php">Blog</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="stream.php">Whatnot Stream</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <button class="btn btn-link nav-link" id="theme-toggle">
                            <i class="fas fa-moon" id="theme-icon"></i>
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Stream Status -->
    <section class="py-3 bg-primary text-white">
        <div class="container text-center">
            <div id="stream-status">
                <?php if ($liveStream): ?>
                    <h5><i class="fas fa-circle text-success me-2"></i>We're Live Now!</h5>
                <?php else: ?>
                    <h5><i class="fas fa-circle text-danger me-2"></i>Currently Offline</h5>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Live Stream -->
    <?php if ($liveStream): ?>
        <section class="py-5">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="card shadow">
                            <div class="card-header bg-dark text-white">
                                <h4 class="mb-0"><?php echo htmlspecialchars($liveStream['title']); ?></h4>
                            </div>
                            <div class="card-body p-0">
                                <div class="stream-container">
                                    <!-- Whatnot embed placeholder -->
                                    <iframe 
                                        src="https://www.whatnot.com/embed/<?php echo WHATNOT_CHANNEL; ?>"
                                        allowfullscreen>
                                    </iframe>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4 text-center">
                            <a href="https://whatnot.com/user/<?php echo WHATNOT_CHANNEL; ?>" 
                               class="btn btn-primary btn-lg" 
                               target="_blank">
                                <i class="fas fa-external-link-alt me-2"></i>Watch on Whatnot
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- Upcoming Streams -->
    <section class="py-5 <?php echo $liveStream ? 'bg-light' : ''; ?>">
        <div class="container">
            <h2 class="text-center mb-4">Upcoming Streams</h2>
            
            <?php if (empty($upcomingStreams)): ?>
                <div class="text-center">
                    <p class="lead">No scheduled streams at the moment.</p>
                    <p>Check back soon for our streaming schedule!</p>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($upcomingStreams as $stream): ?>
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($stream['title']); ?></h5>
                                    <p class="card-text">
                                        <i class="fas fa-calendar-alt me-2"></i>
                                        <?php echo date('F j, Y \at g:i A', strtotime($stream['scheduled_time'])); ?>
                                    </p>
                                    <?php if ($stream['description']): ?>
                                        <p class="card-text"><?php echo htmlspecialchars($stream['description']); ?></p>
                                    <?php endif; ?>
                                </div>
                                <div class="card-footer bg-transparent">
                                    <button class="btn btn-outline-primary btn-sm w-100" onclick="addToCalendar('<?php echo htmlspecialchars($stream['scheduled_time']); ?>', '<?php echo htmlspecialchars($stream['title']); ?>')">
                                        <i class="fas fa-calendar-plus me-2"></i>Add to Calendar
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Stream Info -->
    <section class="py-5 bg-dark text-white">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h2>Join Us on Whatnot</h2>
                    <p class="lead">Experience live card breaks, auctions, and exclusive deals!</p>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check me-2"></i>Live card breaks</li>
                        <li><i class="fas fa-check me-2"></i>Real-time bidding</li>
                        <li><i class="fas fa-check me-2"></i>Interactive chat</li>
                        <li><i class="fas fa-check me-2"></i>Special promotions</li>
                    </ul>
                    <a href="https://whatnot.com/user/<?php echo WHATNOT_CHANNEL; ?>" 
                       class="btn btn-light btn-lg" 
                       target="_blank">
                        <i class="fab fa-twitch me-2"></i>Follow on Whatnot
                    </a>
                </div>
                <div class="col-md-6 text-center">
                    <img src="/api/placeholder/400/300" class="img-fluid rounded" alt="Whatnot stream preview">
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white pt-5 pb-4">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5>Tri-State Cards NJ</h5>
                    <p>Your premier destination for sports cards and collectibles.</p>
                </div>
                <div class="col-md-4 mb-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="listings.php" class="text-light">eBay Listings</a></li>
                        <li><a href="blog.php" class="text-light">Blog</a></li>
                        <li><a href="stream.php" class="text-light">Live Stream</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-4">
                    <h5>Follow Us</h5>
                    <div class="d-flex gap-3">
                        <a href="https://www.ebay.com/str/tristatecardsnj" class="text-light fs-4"><i class="fab fa-ebay"></i></a>
                        <a href="https://whatnot.com/user/tscardbreaks" class="text-light fs-4"><i class="fas fa-video"></i></a>
                    </div>
                </div>
            </div>
            <hr>
            <div class="text-center">
                <p class="mb-0">&copy; <?php echo date('Y'); ?> Tri-State Cards NJ. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/app.js"></script>
    <script>
        // Auto-refresh stream status
        setInterval(function() {
            fetch('api/stream-status.php')
                .then(response => response.json())
                .then(data => {
                    const statusDiv = document.getElementById('stream-status');
                    
                    if (data.is_live) {
                        statusDiv.innerHTML = '<h5><i class="fas fa-circle text-success me-2"></i>We\'re Live Now!</h5>';
                        
                        // Reload page if currently showing offline
                        if (!document.querySelector('.stream-container')) {
                            window.location.reload();
                        }
                    } else {
                        statusDiv.innerHTML = '<h5><i class="fas fa-circle text-danger me-2"></i>Currently Offline</h5>';
                    }
                })
                .catch(error => console.error('Error checking stream status:', error));
        }, 30000);
        
        // Add to calendar function
        function addToCalendar(datetime, title) {
            const event = {
                title: title,
                start: new Date(datetime).toISOString().replace(/[:.-]/g, ''),
                details: 'Join us on Whatnot: https://whatnot.com/user/<?php echo WHATNOT_CHANNEL; ?>',
                location: 'Whatnot Live Stream'
            };
            
            const googleUrl = `https://calendar.google.com/calendar/render?action=TEMPLATE&text=${encodeURIComponent(event.title)}&dates=${event.start}/${event.start}&details=${encodeURIComponent(event.details)}&location=${encodeURIComponent(event.location)}`;
            
            window.open(googleUrl, '_blank');
        }
    </script>
</body>
</html>