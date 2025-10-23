<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="text-center py-5">
                <?php if ($success): ?>
                    <div class="mb-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                    </div>
                    
                    <h1 class="display-4 fw-bold text-success mb-3">Email Verified!</h1>
                    
                    <p class="lead mb-4">
                        Your email address has been successfully verified. You can now access all features of your account.
                    </p>
                    
                    <div class="alert alert-success" role="alert">
                        <i class="fas fa-check me-2"></i>
                        <strong>Account Activated!</strong><br>
                        You can now log in and start using our services.
                    </div>
                    
                    <div class="mt-4">
                        <a href="/login" class="btn btn-success btn-lg">
                            <i class="fas fa-sign-in-alt me-2"></i>
                            Continue to Login
                        </a>
                    </div>
                <?php else: ?>
                    <div class="mb-4">
                        <i class="fas fa-exclamation-triangle text-warning" style="font-size: 4rem;"></i>
                    </div>
                    
                    <h1 class="display-4 fw-bold text-warning mb-3">Verification Failed</h1>
                    
                    <p class="lead mb-4">
                        <?= htmlspecialchars($message) ?>
                    </p>
                    
                    <div class="alert alert-warning" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Unable to verify email</strong><br>
                        The verification link may be invalid or expired.
                    </div>
                    
                    <div class="mt-4">
                        <a href="/login" class="btn btn-primary btn-lg me-3">
                            <i class="fas fa-sign-in-alt me-2"></i>
                            Go to Login
                        </a>
                        <a href="/register" class="btn btn-outline-primary btn-lg">
                            <i class="fas fa-user-plus me-2"></i>
                            Register Again
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
