<?php
// pages/contact.php
$pageTitle = 'Contact Us';

$message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $message_content = $_POST['message'] ?? '';
    
    // Simple validation
    if (!empty($name) && !empty($email) && !empty($message_content)) {
        // Send email (in a real application, you would use mail() or a library like PHPMailer)
        $to = 'your-email@tristatecards.com';
        $headers = "From: $name <$email>" . "\r\n";
        $sent = mail($to, $subject, $message_content, $headers);
        
        if ($sent) {
            $message = '<div class="success-message">Your message has been sent! We\'ll get back to you soon.</div>';
        } else {
            $message = '<div class="error-message">Failed to send your message. Please try again later.</div>';
        }
    } else {
        $message = '<div class="error-message">Please fill out all required fields.</div>';
    }
}
?>

<h1 class="page-title">Contact Us</h1>

<?php echo $message; ?>

<div class="contact-container">
    <div class="contact-form">
        <form action="<?php echo SITE_URL; ?>/contact" method="post">
            <div class="form-group">
                <label for="name">Your Name *</label>
                <input type="text" id="name" name="name" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email Address *</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="subject">Subject</label>
                <input type="text" id="subject" name="subject">
            </div>
            
            <div class="form-group">
                <label for="message">Your Message *</label>
                <textarea id="message" name="message" rows="6" required></textarea>
            </div>
            
            <button type="submit" class="btn">Send Message</button>
        </form>
    </div>
    
    <div class="contact-info">
        <h2>Get In Touch</h2>
        
        <div class="info-item">
            <h3>Follow Us</h3>
            <p>Stay updated with our latest breaks and listings!</p>
            <div class="social-links">
                <a href="https://www.whatnot.com/user/tscardbreaks" target="_blank">Whatnot</a>
                <a href="https://www.ebay.com/usr/tscardbreaks" target="_blank">eBay</a>
                <a href="https://www.instagram.com/tristatecards" target="_blank">Instagram</a>
                <a href="https://www.twitter.com/tristatecards" target="_blank">Twitter</a>
            </div>
        </div>
        
        <div class="info-item">
            <h3>Business Hours</h3>
            <p>Monday - Friday: 9am - 5pm</p>
            <p>Saturday: 10am - 4pm</p>
            <p>Sunday: Closed</p>
        </div>
    </div>
</div>