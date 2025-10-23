<?php
/**
 * My Appointments View
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Appointments - <?= APP_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/custom.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <h1>My Appointments</h1>
                
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Doctor</th>
                                        <th>Status</th>
                                        <th>Payment</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($appointments as $appointment): ?>
                                    <tr>
                                        <td><?= date('M j, Y', strtotime($appointment['appointment_date'])) ?></td>
                                        <td><?= $appointment['time_slot'] ?></td>
                                        <td><?= $appointment['first_name'] . ' ' . $appointment['last_name'] ?></td>
                                        <td>
                                            <span class="badge bg-<?= $this->getStatusColor($appointment['status']) ?>">
                                                <?= $appointment['status'] ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= $appointment['payment_status'] === 'COMPLETED' ? 'success' : 'warning' ?>">
                                                <?= $appointment['payment_status'] ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($appointment['status'] === 'REVIEW' && $appointment['payment_status'] !== 'COMPLETED'): ?>
                                            <form method="POST" action="/appointments/cancel/<?= $appointment['appointment_id'] ?>" class="d-inline">
                                                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Cancel</button>
                                            </form>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
function getStatusColor($status) {
    switch ($status) {
        case 'REVIEW': return 'warning';
        case 'ACCEPTED': return 'info';
        case 'CONSULTING': return 'primary';
        case 'COMPLETED': return 'success';
        case 'CANCELLED': return 'danger';
        default: return 'secondary';
    }
}
?>
