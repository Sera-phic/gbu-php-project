    </main>
    <footer class="main-footer">
        <div class="container footer-content">
            <div class="footer-section about">
                <h3>About GBU</h3>
                <p>School of Information and Communication Technology, Gautam Buddha University, Greater Noida, Uttar Pradesh.</p>
                <div class="contact-info">
                    <span><i class="fas fa-phone"></i> 0120-234 6070</span>
                    <span><i class="fas fa-fax"></i> Fax: +91-0120-234 6070</span>
                    <span><i class="fas fa-globe"></i> gbu.ac.in</span>
                </div>
            </div>
            
            <div class="footer-section quick-links">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="register.php">Register</a></li>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="admin/login.php">Admin Portal</a></li>
                </ul>
            </div>
            
            <div class="footer-section developed-by">
                <h3>Developed By</h3>
                <p><a href="developer.php" class="dev-link">Krishan and Abhinav</a></p>
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-linkedin"></i></a>
                </div>
            </div>
        </div>
        
        <div class="footer-bottom">
            <div class="container">
                <p>&copy; <?php echo date('Y'); ?> Gautam Buddha University. All Rights Reserved.</p>
                <div class="visitor-counter">
                    <i class="fas fa-eye"></i> Visitors: <span id="visitor-count">0</span>
                </div>
            </div>
        </div>
    </footer>
    
    <script src="assets/js/main.js"></script>
    <script>
        // Visitor Counter Script
        document.addEventListener("DOMContentLoaded", function () {
            let count = localStorage.getItem("visitorCount");
            if (!count) {
                count = 1;
            } else {
                count = parseInt(count) + 1;
            }
            localStorage.setItem("visitorCount", count);
            document.getElementById("visitor-count").textContent = count;
            
            // Mobile Menu Toggle
            const navToggle = document.getElementById('navToggle');
            const navMenu = document.getElementById('navMenu');
            if(navToggle && navMenu) {
                navToggle.addEventListener('click', () => {
                    navMenu.classList.toggle('show');
                });
            }
        });
    </script>
</body>
</html>
