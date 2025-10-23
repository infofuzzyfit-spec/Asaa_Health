<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Patient Dashboard</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <button type="button" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus me-1"></i>
                        Book Appointment
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Welcome Message -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-primary" role="alert">
                <h4 class="alert-heading">
                    <i class="fas fa-heart me-2"></i>
                    Welcome back, <?= htmlspecialchars($_SESSION['user_name'] ?? 'Patient') ?>!
                </h4>
                <p class="mb-0">
                    We're here to help you manage your healthcare needs. 
                    Book appointments, view your medical records, and stay connected with your healthcare providers.
                </p>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Upcoming Appointments
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">2</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Completed Appointments
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">12</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Medical Records
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">8</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-medical fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending Payments
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">1</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-credit-card fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Upcoming Appointments -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Upcoming Appointments</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Date & Time</th>
                                    <th>Doctor</th>
                                    <th>Specialization</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Tomorrow, 10:00 AM</td>
                                    <td>Dr. Smith</td>
                                    <td>Cardiology</td>
                                    <td><span class="badge bg-info">Confirmed</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary">View</button>
                                        <button class="btn btn-sm btn-outline-danger">Cancel</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Next Week, 2:30 PM</td>
                                    <td>Dr. Johnson</td>
                                    <td>Dermatology</td>
                                    <td><span class="badge bg-warning">Pending</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary">View</button>
                                        <button class="btn btn-sm btn-outline-danger">Cancel</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <a href="/appointments/book" class="btn btn-outline-primary w-100">
                                <i class="fas fa-calendar-plus fa-2x mb-2"></i><br>
                                Book Appointment
                            </a>
                        </div>
                        <div class="col-md-6 mb-3">
                            <a href="/appointments" class="btn btn-outline-success w-100">
                                <i class="fas fa-calendar-check fa-2x mb-2"></i><br>
                                My Appointments
                            </a>
                        </div>
                        <div class="col-md-6 mb-3">
                            <a href="/medical-records" class="btn btn-outline-info w-100">
                                <i class="fas fa-file-medical fa-2x mb-2"></i><br>
                                Medical Records
                            </a>
                        </div>
                        <div class="col-md-6 mb-3">
                            <a href="/doctors" class="btn btn-outline-warning w-100">
                                <i class="fas fa-user-md fa-2x mb-2"></i><br>
                                Find Doctors
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Activity</h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Appointment Booked</h6>
                                <small class="text-muted">Cardiology consultation with Dr. Smith</small>
                            </div>
                            <small class="text-muted">2 days ago</small>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Medical Record Updated</h6>
                                <small class="text-muted">Blood test results added</small>
                            </div>
                            <small class="text-muted">1 week ago</small>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Payment Processed</h6>
                                <small class="text-muted">Consultation fee paid</small>
                            </div>
                            <small class="text-muted">1 week ago</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
