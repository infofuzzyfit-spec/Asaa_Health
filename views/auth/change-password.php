<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg border-0">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="fas fa-key text-primary" style="font-size: 3rem;"></i>
                        <h2 class="fw-bold mt-3">Change Password</h2>
                        <p class="text-muted">Update your account password</p>
                    </div>

                    <?php if (isset($success) && $success): ?>
                        <div class="alert alert-success" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            <?= htmlspecialchars($message) ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($message) && !$success): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <?= htmlspecialchars($message) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="/change-password" id="changePasswordForm">
                        <?= $csrfMiddleware->getInputField() ?>
                        
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-lock"></i>
                                </span>
                                <input type="password" 
                                       class="form-control" 
                                       id="current_password" 
                                       name="current_password" 
                                       required 
                                       autocomplete="current-password"
                                       placeholder="Enter current password">
                                <button class="btn btn-outline-secondary" 
                                        type="button" 
                                        id="toggleCurrentPassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="new_password" class="form-label">New Password</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-lock"></i>
                                </span>
                                <input type="password" 
                                       class="form-control" 
                                       id="new_password" 
                                       name="new_password" 
                                       required 
                                       autocomplete="new-password"
                                       minlength="8"
                                       placeholder="Enter new password">
                                <button class="btn btn-outline-secondary" 
                                        type="button" 
                                        id="toggleNewPassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="form-text">
                                Password must be at least 8 characters long
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="confirm_new_password" class="form-label">Confirm New Password</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-lock"></i>
                                </span>
                                <input type="password" 
                                       class="form-control" 
                                       id="confirm_new_password" 
                                       name="confirm_new_password" 
                                       required 
                                       autocomplete="new-password"
                                       minlength="8"
                                       placeholder="Confirm new password">
                                <button class="btn btn-outline-secondary" 
                                        type="button" 
                                        id="toggleConfirmNewPassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                <span class="spinner-border spinner-border-sm me-2 d-none" id="submitSpinner"></span>
                                <i class="fas fa-save me-2"></i>
                                Update Password
                            </button>
                        </div>
                    </form>

                    <div class="text-center mt-4">
                        <a href="/profile" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>
                            Back to Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('changePasswordForm');
    const submitBtn = document.getElementById('submitBtn');
    const submitSpinner = document.getElementById('submitSpinner');
    const toggleCurrentPassword = document.getElementById('toggleCurrentPassword');
    const toggleNewPassword = document.getElementById('toggleNewPassword');
    const toggleConfirmNewPassword = document.getElementById('toggleConfirmNewPassword');
    const currentPasswordInput = document.getElementById('current_password');
    const newPasswordInput = document.getElementById('new_password');
    const confirmNewPasswordInput = document.getElementById('confirm_new_password');

    // Toggle password visibility
    toggleCurrentPassword.addEventListener('click', function() {
        const type = currentPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        currentPasswordInput.setAttribute('type', type);
        
        const icon = this.querySelector('i');
        icon.classList.toggle('fa-eye');
        icon.classList.toggle('fa-eye-slash');
    });

    toggleNewPassword.addEventListener('click', function() {
        const type = newPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        newPasswordInput.setAttribute('type', type);
        
        const icon = this.querySelector('i');
        icon.classList.toggle('fa-eye');
        icon.classList.toggle('fa-eye-slash');
    });

    toggleConfirmNewPassword.addEventListener('click', function() {
        const type = confirmNewPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        confirmNewPasswordInput.setAttribute('type', type);
        
        const icon = this.querySelector('i');
        icon.classList.toggle('fa-eye');
        icon.classList.toggle('fa-eye-slash');
    });

    // Password confirmation validation
    confirmNewPasswordInput.addEventListener('input', function() {
        if (this.value !== newPasswordInput.value) {
            this.setCustomValidity('Passwords do not match');
        } else {
            this.setCustomValidity('');
        }
    });

    newPasswordInput.addEventListener('input', function() {
        if (confirmNewPasswordInput.value && this.value !== confirmNewPasswordInput.value) {
            confirmNewPasswordInput.setCustomValidity('Passwords do not match');
        } else {
            confirmNewPasswordInput.setCustomValidity('');
        }
    });

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validate passwords match
        if (newPasswordInput.value !== confirmNewPasswordInput.value) {
            showAlert('New passwords do not match', 'danger');
            return;
        }
        
        // Show loading state
        submitBtn.disabled = true;
        submitSpinner.classList.remove('d-none');
        
        // Get form data
        const formData = new FormData(form);
        
        // Submit form
        fetch('/change-password', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert(data.message, 'success');
                // Clear form
                form.reset();
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
