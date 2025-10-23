<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg border-0">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="fas fa-lock text-primary" style="font-size: 3rem;"></i>
                        <h2 class="fw-bold mt-3">Reset Password</h2>
                        <p class="text-muted">Enter your new password below.</p>
                    </div>

                    <?php if (isset($success) && $success): ?>
                        <div class="alert alert-success" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            <?= htmlspecialchars($message) ?>
                        </div>
                        
                        <div class="text-center mt-4">
                            <a href="/login" class="btn btn-success btn-lg">
                                <i class="fas fa-sign-in-alt me-2"></i>
                                Continue to Login
                            </a>
                        </div>
                    <?php else: ?>
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <?= htmlspecialchars($error) ?>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($message) && !$success): ?>
                            <div class="alert alert-danger" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <?= htmlspecialchars($message) ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="/reset-password" id="resetPasswordForm">
                            <?= $csrfMiddleware->getInputField() ?>
                            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">New Password</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input type="password" 
                                           class="form-control" 
                                           id="password" 
                                           name="password" 
                                           required 
                                           autocomplete="new-password"
                                           minlength="8"
                                           placeholder="Enter new password">
                                    <button class="btn btn-outline-secondary" 
                                            type="button" 
                                            id="togglePassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="form-text">
                                    Password must be at least 8 characters long
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="confirm_password" class="form-label">Confirm New Password</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input type="password" 
                                           class="form-control" 
                                           id="confirm_password" 
                                           name="confirm_password" 
                                           required 
                                           autocomplete="new-password"
                                           minlength="8"
                                           placeholder="Confirm new password">
                                    <button class="btn btn-outline-secondary" 
                                            type="button" 
                                            id="toggleConfirmPassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                    <span class="spinner-border spinner-border-sm me-2 d-none" id="submitSpinner"></span>
                                    <i class="fas fa-save me-2"></i>
                                    Reset Password
                                </button>
                            </div>
                        </form>

                        <div class="text-center mt-4">
                            <p class="text-muted">
                                Remember your password? 
                                <a href="/login" class="text-decoration-none fw-bold">
                                    Sign in here
                                </a>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('resetPasswordForm');
    const submitBtn = document.getElementById('submitBtn');
    const submitSpinner = document.getElementById('submitSpinner');
    const togglePassword = document.getElementById('togglePassword');
    const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirm_password');

    if (form) {
        // Toggle password visibility
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            const icon = this.querySelector('i');
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        });

        toggleConfirmPassword.addEventListener('click', function() {
            const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            confirmPasswordInput.setAttribute('type', type);
            
            const icon = this.querySelector('i');
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        });

        // Password confirmation validation
        confirmPasswordInput.addEventListener('input', function() {
            if (this.value !== passwordInput.value) {
                this.setCustomValidity('Passwords do not match');
            } else {
                this.setCustomValidity('');
            }
        });

        passwordInput.addEventListener('input', function() {
            if (confirmPasswordInput.value && this.value !== confirmPasswordInput.value) {
                confirmPasswordInput.setCustomValidity('Passwords do not match');
            } else {
                confirmPasswordInput.setCustomValidity('');
            }
        });

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validate passwords match
            if (passwordInput.value !== confirmPasswordInput.value) {
                showAlert('Passwords do not match', 'danger');
                return;
            }
            
            // Show loading state
            submitBtn.disabled = true;
            submitSpinner.classList.remove('d-none');
            
            // Get form data
            const formData = new FormData(form);
            
            // Submit form
            fetch('/reset-password', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    showAlert(data.message, 'success');
                    // Hide form and show success message
                    form.style.display = 'none';
                    const successDiv = document.createElement('div');
                    successDiv.innerHTML = `
                        <div class="alert alert-success" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            ${data.message}
                        </div>
                        <div class="text-center mt-4">
                            <a href="/login" class="btn btn-success btn-lg">
                                <i class="fas fa-sign-in-alt me-2"></i>
                                Continue to Login
                            </a>
                        </div>
                    `;
                    form.parentNode.insertBefore(successDiv, form.nextSibling);
                } else {
                    showAlert(data.message, 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('An error occurred. Please try again.', 'danger');
            })
            .finally(() => {
                // Hide loading state
                submitBtn.disabled = false;
                submitSpinner.classList.add('d-none');
            });
        });
    }

    function showAlert(message, type) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        // Remove existing alerts
        const existingAlerts = document.querySelectorAll('.alert');
        existingAlerts.forEach(alert => alert.remove());
        
        // Insert new alert
        form.parentNode.insertBefore(alertDiv, form);
    }
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
