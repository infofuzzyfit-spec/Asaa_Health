    </main>

    <!-- Footer -->
    <footer class="bg-dark text-light py-5 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-heartbeat me-2"></i>
                        <?= APP_NAME ?>
                    </h5>
                    <p class="text-muted">
                        Your trusted healthcare partner providing quality medical services 
                        and easy appointment booking.
                    </p>
                    <div class="social-links">
                        <a href="#" class="text-light me-3">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="text-light me-3">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="text-light me-3">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="#" class="text-light">
                            <i class="fab fa-instagram"></i>
                        </a>
                    </div>
                </div>
                
                <div class="col-md-2 mb-4">
                    <h6 class="fw-bold mb-3">Quick Links</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <a href="/" class="text-muted text-decoration-none">Home</a>
                        </li>
                        <li class="mb-2">
                            <a href="/doctors" class="text-muted text-decoration-none">Doctors</a>
                        </li>
                        <li class="mb-2">
                            <a href="/appointments" class="text-muted text-decoration-none">Appointments</a>
                        </li>
                        <li class="mb-2">
                            <a href="/about" class="text-muted text-decoration-none">About Us</a>
                        </li>
                    </ul>
                </div>
                
                <div class="col-md-2 mb-4">
                    <h6 class="fw-bold mb-3">Services</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <a href="/specializations" class="text-muted text-decoration-none">Specializations</a>
                        </li>
                        <li class="mb-2">
                            <a href="/emergency" class="text-muted text-decoration-none">Emergency Care</a>
                        </li>
                        <li class="mb-2">
                            <a href="/telemedicine" class="text-muted text-decoration-none">Telemedicine</a>
                        </li>
                        <li class="mb-2">
                            <a href="/health-checkup" class="text-muted text-decoration-none">Health Checkup</a>
                        </li>
                    </ul>
                </div>
                
                <div class="col-md-4 mb-4">
                    <h6 class="fw-bold mb-3">Contact Info</h6>
                    <div class="contact-info">
                        <div class="mb-2">
                            <i class="fas fa-map-marker-alt me-2"></i>
                            <span class="text-muted">123 Health Street, Medical City</span>
                        </div>
                        <div class="mb-2">
                            <i class="fas fa-phone me-2"></i>
                            <span class="text-muted">+94 11 234 5678</span>
                        </div>
                        <div class="mb-2">
                            <i class="fas fa-envelope me-2"></i>
                            <span class="text-muted">info@<?= strtolower(str_replace(' ', '', APP_NAME)) ?>.com</span>
                        </div>
                        <div class="mb-2">
                            <i class="fas fa-clock me-2"></i>
                            <span class="text-muted">Mon - Fri: 8:00 AM - 6:00 PM</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <hr class="my-4">
            
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="text-muted mb-0">
                        &copy; <?= date('Y') ?> <?= APP_NAME ?>. All rights reserved.
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="/privacy" class="text-muted text-decoration-none me-3">Privacy Policy</a>
                    <a href="/terms" class="text-muted text-decoration-none">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <button class="btn btn-primary back-to-top" id="backToTop" style="display: none;">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    
    <!-- Custom JS -->
    <script src="/assets/js/components.js"></script>
    
    <!-- Additional JS -->
    <?php if (isset($additional_js)): ?>
        <?php foreach ($additional_js as $js): ?>
            <script src="<?= htmlspecialchars($js) ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- CSRF Token for AJAX -->
    <script>
        // Set up CSRF token for AJAX requests
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-Token': csrfToken.getAttribute('content')
                }
            });
        }
    </script>

    <!-- Back to Top Button Script -->
    <script>
        // Back to top button
        window.addEventListener('scroll', function() {
            const backToTop = document.getElementById('backToTop');
            if (window.pageYOffset > 300) {
                backToTop.style.display = 'block';
            } else {
                backToTop.style.display = 'none';
            }
        });

        document.getElementById('backToTop').addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    </script>
</body>
</html>
