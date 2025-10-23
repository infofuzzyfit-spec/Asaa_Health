<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="text-center py-5">
                <div class="mb-4">
                    <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                </div>
                
                <h1 class="display-4 fw-bold text-success mb-3">Registration Successful!</h1>
                
                <p class="lead mb-4">
                    Thank you for joining <?= APP_NAME ?>. We're excited to have you as part of our healthcare community.
                </p>
                
                <div class="alert alert-info" role="alert">
                    <i class="fas fa-envelope me-2"></i>
                    <strong>Please check your email!</strong><br>
                    We've sent a verification link to <strong><?= htmlspecialchars($email) ?></strong><br>
                    Click the link in the email to activate your account.
                </div>
                
                <div class="row text-center mt-5">
                    <div class="col-md-4 mb-3">
                        <i class="fas fa-shield-alt fa-2x text-primary mb-2"></i>
                        <h5>Secure Account</h5>
                        <p class="text-muted">Your data is protected with industry-standard security</p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <i class="fas fa-calendar-check fa-2x text-primary mb-2"></i>
                        <h5>Easy Booking</h5>
                        <p class="text-muted">Book appointments with our qualified doctors</p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <i class="fas fa-user-md fa-2x text-primary mb-2"></i>
                        <h5>Expert Care</h5>
                        <p class="text-muted">Access to experienced medical professionals</p>
                    </div>
                </div>
                
                <div class="mt-5">
                    <p class="text-muted mb-3">
                        Didn't receive the email? Check your spam folder or
                    </p>
                    <form method="POST" action="/resend-verification" class="d-inline">
                        <?= $csrfMiddleware->getInputField() ?>
                        <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="fas fa-paper-plane me-2"></i>
                            Resend Verification Email
                        </button>
                    </form>
                </div>
                
                <div class="mt-4">
                    <a href="/login" class="btn btn-primary btn-lg">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        Go to Login
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-resend verification email after 30 seconds if not verified
    setTimeout(function() {
        const resendBtn = document.querySelector('button[type="submit"]');
        if (resendBtn) {
            resendBtn.click();
        }
    }, 30000);
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
