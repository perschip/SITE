<?php
// includes/footer.php
?>
        </div>
    </main>
    
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>About Us</h3>
                    <p>Tristate Cards is your premier source for sports card breaks, collectibles, and more. Based in New Jersey, we serve collectors across the tristate area and beyond.</p>
                </div>
                
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="<?php echo SITE_URL; ?>">Home</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/ebay">eBay Listings</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/blog">Blog</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/contact">Contact</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h3>Connect With Us</h3>
                    <ul>
                        <li><a href="https://www.whatnot.com/user/tscardbreaks" target="_blank">Whatnot</a></li>
                        <li><a href="https://www.ebay.com/str/tristatecardsnj?_trksid=p4429486.m3561.l161211" target="_blank">eBay</a></li>
                        <li><a href="https://www.instagram.com/tristatecards" target="_blank">Instagram</a></li>
                        <li><a href="https://www.twitter.com/tristatecards" target="_blank">Twitter</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
            </div>
        </div>
    </footer>
    
    <script src="<?php echo SITE_URL; ?>/js/dark-mode.js"></script>
    <?php if (isset($pageScripts) && is_array($pageScripts)): ?>
        <?php foreach ($pageScripts as $script): ?>
            <script src="<?php echo SITE_URL; ?>/js/<?php echo $script; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
    <script>
// Mobile menu toggle for main site
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
    const mainNav = document.getElementById('main-nav');
    
    if (mobileMenuToggle && mainNav) {
        mobileMenuToggle.addEventListener('click', function() {
            mainNav.classList.toggle('mobile-active');
            this.classList.toggle('active');
        });
    }
    
    // Mobile menu for admin panel
    const adminMobileToggle = document.getElementById('admin-mobile-toggle');
    const adminNav = document.getElementById('admin-nav');
    
    if (adminMobileToggle && adminNav) {
        adminMobileToggle.addEventListener('click', function() {
            adminNav.classList.toggle('mobile-active');
            this.classList.toggle('active');
        });
    }
});
</script>
</body>
</html>