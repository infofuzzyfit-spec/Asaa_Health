<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="text-center py-5">
                <div class="mb-4">
                    <i class="fas fa-exclamation-triangle text-warning" style="font-size: 5rem;"></i>
                </div>
                
                <h1 class="display-4 fw-bold text-warning mb-3">Access Denied</h1>
                
                <p class="lead mb-4">
                    You don't have permission to access this page or perform this action.
                </p>
                
                <div class="alert alert-warning" role="alert">
                    <i class="fas fa-shield-alt me-2"></i>
                    <strong>Unauthorized Access</strong><br>
                    Please contact your administrator if you believe this is an error.
                </div>
                
                <div class="mt-5">
                    <a href="/dashboard" class="btn btn-primary btn-lg me-3">
                        <i class="fas fa-home me-2"></i>
                        Go to Dashboard
                    </a>
                    <a href="/login" class="btn btn-outline-primary btn-lg">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        Login Again
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
