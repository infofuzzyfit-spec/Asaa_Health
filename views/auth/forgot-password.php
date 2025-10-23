<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg border-0">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="fas fa-key text-primary" style="font-size: 3rem;"></i>
                        <h2 class="fw-bold mt-3">Forgot Password?</h2>
                        <p class="text-muted">Enter your email address and we'll send you a link to reset your password.</p>
                    </div>

                    <?php if (isset($success) && $success): ?>
                        <div class="alert alert-success" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            <?= htmlspecialchars($message) ?>
                        </div>
                        
                        <div class="text-center mt-4">
                            <a href="/login" class="btn btn-primary">
                                <i class="fas fa-sign-in-alt me-2"></i>
                                Back to Login
                            </a>
                        </div>
                    <?php else: ?>
                        <?php if (isset($message)): ?>
                            <div class="alert alert-danger" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <?= htmlspecialchars($message) ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="/forgot-password" id="forgotPasswordForm">
                            <?= $csrfMiddleware->getInputField() ?>
                            
                            <div class="mb-4">
                                <label for="email" class="form-label">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                    <input type="email" 
                                           class="form-control" 
                                           id="email" 
                                           name="email" 
                                           required 
                                           autocomplete="email"
                                           placeholder="Enter your email address">
                                </div>
                            </div>

                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                    <span class="spinner-border spinner-border-sm me-2 d-none" id="submitSpinner"></span>
                                    <i class="fas fa-paper-plane me-2"></i>
                                    Send Reset Link
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
    const form = document.getElementById('forgotPasswordForm');
    const submitBtn = document.getElementById('submitBtn');
    const submitSpinner = document.getElementById('submitSpinner');

    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Show loading state
            submitBtn.disabled = true;
            submitSpinner.classList.remove('d-none');
            
            // Get form data
            const formData = new FormData(form);
            
            // Submit form
            fetch('/forgot-password', {
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
                            <a href="/login" class="btn btn-primary">
                                <i class="fas fa-sign-in-alt me-2"></i>
                                Back to Login
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
