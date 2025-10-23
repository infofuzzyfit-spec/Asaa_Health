<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-user me-2"></i>
                        My Profile
                    </h4>
                </div>
                <div class="card-body p-4">
                    <?php if (isset($_GET['success'])): ?>
                        <div class="alert alert-success" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            Profile updated successfully!
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            Failed to update profile. Please try again.
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="/profile" id="profileForm">
                        <?= $csrfMiddleware->getInputField() ?>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="first_name" class="form-label">First Name</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="first_name" 
                                       name="first_name" 
                                       value="<?= htmlspecialchars($user['first_name'] ?? '') ?>"
                                       required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="last_name" class="form-label">Last Name</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="last_name" 
                                       name="last_name" 
                                       value="<?= htmlspecialchars($user['last_name'] ?? '') ?>"
                                       required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" 
                                   class="form-control" 
                                   id="email" 
                                   name="email" 
                                   value="<?= htmlspecialchars($user['email'] ?? '') ?>"
                                   required 
                                   readonly>
                            <div class="form-text">Email cannot be changed</div>
                        </div>

                        <div class="mb-3">
                            <label for="nic" class="form-label">NIC Number</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="nic" 
                                   name="nic" 
                                   value="<?= htmlspecialchars($user['nic'] ?? '') ?>"
                                   required 
                                   readonly>
                            <div class="form-text">NIC cannot be changed</div>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" 
                                   class="form-control" 
                                   id="phone" 
                                   name="phone" 
                                   value="<?= htmlspecialchars($user['phone'] ?? '') ?>"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label for="gender" class="form-label">Gender</label>
                            <select class="form-select" id="gender" name="gender" required>
                                <option value="male" <?= ($user['gender'] ?? '') === 'male' ? 'selected' : '' ?>>Male</option>
                                <option value="female" <?= ($user['gender'] ?? '') === 'female' ? 'selected' : '' ?>>Female</option>
                                <option value="other" <?= ($user['gender'] ?? '') === 'other' ? 'selected' : '' ?>>Other</option>
                            </select>
                        </div>

                        <?php if ($user['role'] === 'doctor'): ?>
                            <div class="mb-3">
                                <label for="qualification" class="form-label">Qualification</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="qualification" 
                                       name="qualification" 
                                       value="<?= htmlspecialchars($user['qualification'] ?? '') ?>">
                            </div>

                            <div class="mb-3">
                                <label for="experience" class="form-label">Experience (Years)</label>
                                <input type="number" 
                                       class="form-control" 
                                       id="experience" 
                                       name="experience" 
                                       value="<?= htmlspecialchars($user['experience'] ?? '') ?>"
                                       min="0">
                            </div>

                            <div class="mb-3">
                                <label for="consultation_fee" class="form-label">Consultation Fee (LKR)</label>
                                <input type="number" 
                                       class="form-control" 
                                       id="consultation_fee" 
                                       name="consultation_fee" 
                                       value="<?= htmlspecialchars($user['consultation_fee'] ?? '') ?>"
                                       min="0"
                                       step="0.01">
                            </div>

                            <div class="mb-3">
                                <label for="bio" class="form-label">Bio</label>
                                <textarea class="form-control" 
                                          id="bio" 
                                          name="bio" 
                                          rows="4"
                                          placeholder="Tell us about yourself..."><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
                            </div>
                        <?php endif; ?>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="/change-password" class="btn btn-outline-warning me-md-2">
                                <i class="fas fa-key me-2"></i>
                                Change Password
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <span class="spinner-border spinner-border-sm me-2 d-none" id="submitSpinner"></span>
                                <i class="fas fa-save me-2"></i>
                                Update Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('profileForm');
    const submitBtn = document.getElementById('submitBtn');
    const submitSpinner = document.getElementById('submitSpinner');

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Show loading state
        submitBtn.disabled = true;
        submitSpinner.classList.remove('d-none');
        
        // Get form data
        const formData = new FormData(form);
        
        // Submit form
        fetch('/profile', {
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
                // Reload page after 2 seconds
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
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
