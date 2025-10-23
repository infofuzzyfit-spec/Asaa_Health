<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid">
    <div class="row min-vh-100">
        <div class="col-lg-6 d-flex align-items-center justify-content-center bg-primary">
            <div class="text-center text-white">
                <h1 class="display-4 fw-bold mb-4"><?= APP_NAME ?></h1>
                <p class="lead mb-4">Your trusted healthcare partner</p>
                <div class="row text-center">
                    <div class="col-md-4 mb-3">
                        <i class="fas fa-user-md fa-3x mb-3"></i>
                        <h5>Expert Doctors</h5>
                        <p>Qualified medical professionals</p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <i class="fas fa-calendar-check fa-3x mb-3"></i>
                        <h5>Easy Booking</h5>
                        <p>Simple appointment scheduling</p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <i class="fas fa-shield-alt fa-3x mb-3"></i>
                        <h5>Secure & Private</h5>
                        <p>Your data is safe with us</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 d-flex align-items-center justify-content-center">
            <div class="w-100" style="max-width: 400px;">
                <div class="text-center mb-4">
                    <h2 class="fw-bold">Welcome Back</h2>
                    <p class="text-muted">Please sign in to your account</p>
                </div>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="/login" id="loginForm">
                    <?= $csrfMiddleware->getInputField() ?>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-envelope"></i>
                            </span>
                            <input type="email" 
                                   class="form-control" 
                                   id="email" 
                                   name="email" 
                                   value="<?= htmlspecialchars($email ?? '') ?>"
                                   required 
                                   autocomplete="email">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input type="password" 
                                   class="form-control" 
                                   id="password" 
                                   name="password" 
                                   required 
                                   autocomplete="current-password">
                            <button class="btn btn-outline-secondary" 
                                    type="button" 
                                    id="togglePassword">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" 
                               class="form-check-input" 
                               id="remember_me" 
                               name="remember_me">
                        <label class="form-check-label" for="remember_me">
                            Remember me
                        </label>
                    </div>

                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-primary btn-lg" id="loginBtn">
                            <span class="spinner-border spinner-border-sm me-2 d-none" id="loginSpinner"></span>
                            Sign In
                        </button>
                    </div>

                    <div class="text-center">
                        <a href="/forgot-password" class="text-decoration-none">
                            Forgot your password?
                        </a>
                    </div>
                </form>

                <div class="text-center mt-4">
                    <p class="text-muted">
                        Don't have an account? 
                        <a href="/register" class="text-decoration-none fw-bold">
                            Sign up here
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const loginBtn = document.getElementById('loginBtn');
    const loginSpinner = document.getElementById('loginSpinner');
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');

    // Toggle password visibility
    togglePassword.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        
        const icon = this.querySelector('i');
        icon.classList.toggle('fa-eye');
        icon.classList.toggle('fa-eye-slash');
    });

    // Handle form submission
    loginForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Show loading state
        loginBtn.disabled = true;
        loginSpinner.classList.remove('d-none');
        
        // Get form data
        const formData = new FormData(loginForm);
        
        // Submit form
        fetch('/login', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Redirect to dashboard
                window.location.href = data.redirect || '/dashboard';
            } else {
                // Show error message
                showAlert(data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('An error occurred. Please try again.', 'danger');
        })
        .finally(() => {
            // Hide loading state
            loginBtn.disabled = false;
            loginSpinner.classList.add('d-none');
        });
    });

    function showAlert(message, type) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            <i class="fas fa-exclamation-circle me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        // Remove existing alerts
        const existingAlerts = document.querySelectorAll('.alert');
        existingAlerts.forEach(alert => alert.remove());
        
        // Insert new alert
        loginForm.insertBefore(alertDiv, loginForm.firstChild);
    }
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
