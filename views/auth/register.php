<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid">
    <div class="row min-vh-100">
        <div class="col-lg-6 d-flex align-items-center justify-content-center bg-primary">
            <div class="text-center text-white">
                <h1 class="display-4 fw-bold mb-4"><?= APP_NAME ?></h1>
                <p class="lead mb-4">Join thousands of satisfied patients</p>
                <div class="row text-center">
                    <div class="col-md-4 mb-3">
                        <i class="fas fa-user-plus fa-3x mb-3"></i>
                        <h5>Easy Registration</h5>
                        <p>Quick and simple signup</p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <i class="fas fa-clock fa-3x mb-3"></i>
                        <h5>24/7 Access</h5>
                        <p>Book appointments anytime</p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <i class="fas fa-heart fa-3x mb-3"></i>
                        <h5>Quality Care</h5>
                        <p>Best healthcare services</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 d-flex align-items-center justify-content-center">
            <div class="w-100" style="max-width: 500px;">
                <div class="text-center mb-4">
                    <h2 class="fw-bold">Create Account</h2>
                    <p class="text-muted">Join our healthcare community</p>
                </div>

                <?php if (isset($errors) && !empty($errors)): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <strong>Please fix the following errors:</strong>
                        <ul class="mb-0 mt-2">
                            <?php foreach ($errors as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" action="/register" id="registerForm">
                    <?= $csrfMiddleware->getInputField() ?>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="first_name" class="form-label">First Name *</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="first_name" 
                                   name="first_name" 
                                   value="<?= htmlspecialchars($old['first_name'] ?? '') ?>"
                                   required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="last_name" class="form-label">Last Name *</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="last_name" 
                                   name="last_name" 
                                   value="<?= htmlspecialchars($old['last_name'] ?? '') ?>"
                                   required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address *</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-envelope"></i>
                            </span>
                            <input type="email" 
                                   class="form-control" 
                                   id="email" 
                                   name="email" 
                                   value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                                   required 
                                   autocomplete="email">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="nic" class="form-label">NIC Number *</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-id-card"></i>
                            </span>
                            <input type="text" 
                                   class="form-control" 
                                   id="nic" 
                                   name="nic" 
                                   value="<?= htmlspecialchars($old['nic'] ?? '') ?>"
                                   required 
                                   placeholder="123456789V">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone Number *</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-phone"></i>
                            </span>
                            <input type="tel" 
                                   class="form-control" 
                                   id="phone" 
                                   name="phone" 
                                   value="<?= htmlspecialchars($old['phone'] ?? '') ?>"
                                   required 
                                   placeholder="0771234567">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="gender" class="form-label">Gender *</label>
                        <select class="form-select" id="gender" name="gender" required>
                            <option value="">Select Gender</option>
                            <option value="male" <?= ($old['gender'] ?? '') === 'male' ? 'selected' : '' ?>>Male</option>
                            <option value="female" <?= ($old['gender'] ?? '') === 'female' ? 'selected' : '' ?>>Female</option>
                            <option value="other" <?= ($old['gender'] ?? '') === 'other' ? 'selected' : '' ?>>Other</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password *</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input type="password" 
                                   class="form-control" 
                                   id="password" 
                                   name="password" 
                                   required 
                                   autocomplete="new-password">
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

                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm Password *</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input type="password" 
                                   class="form-control" 
                                   id="confirm_password" 
                                   name="confirm_password" 
                                   required 
                                   autocomplete="new-password">
                            <button class="btn btn-outline-secondary" 
                                    type="button" 
                                    id="toggleConfirmPassword">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" 
                               class="form-check-input" 
                               id="terms" 
                               required>
                        <label class="form-check-label" for="terms">
                            I agree to the <a href="/terms" target="_blank">Terms of Service</a> and <a href="/privacy" target="_blank">Privacy Policy</a>
                        </label>
                    </div>

                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-primary btn-lg" id="registerBtn">
                            <span class="spinner-border spinner-border-sm me-2 d-none" id="registerSpinner"></span>
                            Create Account
                        </button>
                    </div>
                </form>

                <div class="text-center mt-4">
                    <p class="text-muted">
                        Already have an account? 
                        <a href="/login" class="text-decoration-none fw-bold">
                            Sign in here
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const registerForm = document.getElementById('registerForm');
    const registerBtn = document.getElementById('registerBtn');
    const registerSpinner = document.getElementById('registerSpinner');
    const togglePassword = document.getElementById('togglePassword');
    const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirm_password');

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

    // Handle form submission
    registerForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validate passwords match
        if (passwordInput.value !== confirmPasswordInput.value) {
            showAlert('Passwords do not match', 'danger');
            return;
        }
        
        // Show loading state
        registerBtn.disabled = true;
        registerSpinner.classList.remove('d-none');
        
        // Get form data
        const formData = new FormData(registerForm);
        
        // Submit form
        fetch('/register', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message and redirect
                showAlert(data.message, 'success');
                setTimeout(() => {
                    window.location.href = '/register-success';
                }, 2000);
            } else {
                // Show error messages
                if (data.errors) {
                    showValidationErrors(data.errors);
                } else {
                    showAlert(data.message, 'danger');
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('An error occurred. Please try again.', 'danger');
        })
        .finally(() => {
            // Hide loading state
            registerBtn.disabled = false;
            registerSpinner.classList.add('d-none');
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
        registerForm.insertBefore(alertDiv, registerForm.firstChild);
    }

    function showValidationErrors(errors) {
        let errorHtml = '<div class="alert alert-danger" role="alert">';
        errorHtml += '<i class="fas fa-exclamation-circle me-2"></i>';
        errorHtml += '<strong>Please fix the following errors:</strong>';
        errorHtml += '<ul class="mb-0 mt-2">';
        
        Object.values(errors).forEach(error => {
            errorHtml += `<li>${error}</li>`;
        });
        
        errorHtml += '</ul></div>';
        
        // Remove existing alerts
        const existingAlerts = document.querySelectorAll('.alert');
        existingAlerts.forEach(alert => alert.remove());
        
        // Insert new alert
        registerForm.insertBefore(document.createElement('div').innerHTML = errorHtml, registerForm.firstChild);
    }
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
