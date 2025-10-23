<?php
/**
 * Component Usage Examples
 * Demonstrates how to use the component system
 */

require_once __DIR__ . '/../ComponentFactory.php';
require_once __DIR__ . '/../helpers/ComponentHelper.php';

// Example usage of components
class ComponentUsageExamples {
    
    public static function getLoginPage() {
        return '
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Login - ASAA Healthcare</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
            <link href="/assets/css/custom.css" rel="stylesheet">
        </head>
        <body>
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-6 col-lg-4">
                        <div class="card mt-5">
                            <div class="card-header text-center">
                                <h4>Login to ASAA Healthcare</h4>
                            </div>
                            <div class="card-body">
                                ' . ComponentFactory::render('loginForm', [
                                    'csrfToken' => 'your-csrf-token-here',
                                    'errors' => []
                                ]) . '
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script src="/assets/js/components.js"></script>
        </body>
        </html>';
    }
    
    public static function getDashboardPage() {
        $header = ComponentFactory::render('header', [
            'user' => [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'role' => 'Patient'
            ],
            'menuItems' => [
                ['text' => 'Home', 'url' => '/', 'active' => true],
                ['text' => 'Appointments', 'url' => '/appointments'],
                ['text' => 'Medical Records', 'url' => '/medical-records']
            ],
            'notifications' => [
                ['message' => 'Your appointment is confirmed', 'time' => '2 hours ago', 'type' => 'appointment'],
                ['message' => 'Payment received', 'time' => '1 day ago', 'type' => 'payment']
            ]
        ]);
        
        $sidebar = ComponentFactory::render('sidebar', [
            'userRole' => 'Patient',
            'menuItems' => [
                ['text' => 'Dashboard', 'icon' => 'fas fa-tachometer-alt', 'url' => '/dashboard', 'active' => true],
                ['text' => 'My Appointments', 'icon' => 'fas fa-calendar', 'url' => '/appointments'],
                ['text' => 'Book Appointment', 'icon' => 'fas fa-plus-circle', 'url' => '/book-appointment'],
                ['text' => 'Medical Records', 'icon' => 'fas fa-file-medical', 'url' => '/medical-records'],
                ['text' => 'Payments', 'icon' => 'fas fa-credit-card', 'url' => '/payments']
            ]
        ]);
        
        $dashboardCards = '
        <div class="row">
            <div class="col-md-3">
                ' . ComponentHelper::dashboardCard('Total Appointments', '12', 'fas fa-calendar', 'primary', ['direction' => 'up', 'value' => '15']) . '
            </div>
            <div class="col-md-3">
                ' . ComponentHelper::dashboardCard('Upcoming Appointments', '3', 'fas fa-clock', 'info') . '
            </div>
            <div class="col-md-3">
                ' . ComponentHelper::dashboardCard('Medical Records', '8', 'fas fa-file-medical', 'success') . '
            </div>
            <div class="col-md-3">
                ' . ComponentHelper::dashboardCard('Total Payments', 'LKR 25,000', 'fas fa-credit-card', 'warning') . '
            </div>
        </div>';
        
        $recentAppointments = ComponentFactory::render('table', [
            'headers' => [
                ['text' => 'Date', 'key' => 'date'],
                ['text' => 'Doctor', 'key' => 'doctor'],
                ['text' => 'Status', 'key' => 'status'],
                ['text' => 'Actions', 'key' => 'actions']
            ],
            'data' => [
                [
                    'date' => '2024-01-15',
                    'doctor' => 'Dr. Sarah Johnson',
                    'status' => ComponentHelper::statusBadge('ACCEPTED'),
                    'actions' => 'View | Edit'
                ],
                [
                    'date' => '2024-01-16',
                    'doctor' => 'Dr. Michael Chen',
                    'status' => ComponentHelper::statusBadge('REVIEW'),
                    'actions' => 'View | Edit'
                ]
            ]
        ]);
        
        $footer = ComponentFactory::render('footer', [
            'links' => [
                ['text' => 'About Us', 'url' => '/about'],
                ['text' => 'Contact', 'url' => '/contact'],
                ['text' => 'Privacy Policy', 'url' => '/privacy'],
                ['text' => 'Terms of Service', 'url' => '/terms']
            ],
            'socialLinks' => [
                ['icon' => 'fab fa-facebook', 'url' => '#'],
                ['icon' => 'fab fa-twitter', 'url' => '#'],
                ['icon' => 'fab fa-linkedin', 'url' => '#']
            ]
        ]);
        
        return '
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Dashboard - ASAA Healthcare</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
            <link href="/assets/css/custom.css" rel="stylesheet">
        </head>
        <body>
            ' . $header . '
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-2">
                        ' . $sidebar . '
                    </div>
                    <div class="col-md-10">
                        <main class="p-4">
                            <h1>Dashboard</h1>
                            ' . $dashboardCards . '
                            <div class="row mt-4">
                                <div class="col-md-8">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5>Recent Appointments</h5>
                                        </div>
                                        <div class="card-body">
                                            ' . $recentAppointments . '
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5>Quick Actions</h5>
                                        </div>
                                        <div class="card-body">
                                            ' . ComponentFactory::render('button', [
                                                'text' => 'Book New Appointment',
                                                'icon' => 'fas fa-plus',
                                                'class' => 'btn btn-primary w-100 mb-2'
                                            ]) . '
                                            ' . ComponentFactory::render('button', [
                                                'text' => 'View Medical Records',
                                                'icon' => 'fas fa-file-medical',
                                                'class' => 'btn btn-outline-primary w-100 mb-2'
                                            ]) . '
                                            ' . ComponentFactory::render('button', [
                                                'text' => 'Make Payment',
                                                'icon' => 'fas fa-credit-card',
                                                'class' => 'btn btn-outline-success w-100'
                                            ]) . '
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </main>
                    </div>
                </div>
            </div>
            ' . $footer . '
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script src="/assets/js/components.js"></script>
        </body>
        </html>';
    }
    
    public static function getAppointmentBookingPage() {
        $appointmentForm = ComponentFactory::render('appointmentForm', [
            'csrfToken' => 'your-csrf-token-here',
            'specializations' => [
                ['specialization_id' => 1, 'specialization_name' => 'Cardiology'],
                ['specialization_id' => 2, 'specialization_name' => 'Dermatology'],
                ['specialization_id' => 3, 'specialization_name' => 'Pediatrics']
            ],
            'doctors' => [
                ['user_id' => 1, 'first_name' => 'Dr. Sarah', 'last_name' => 'Johnson', 'specialization_name' => 'Cardiology'],
                ['user_id' => 2, 'first_name' => 'Dr. Michael', 'last_name' => 'Chen', 'specialization_name' => 'Dermatology']
            ]
        ]);
        
        return '
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Book Appointment - ASAA Healthcare</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
            <link href="/assets/css/custom.css" rel="stylesheet">
        </head>
        <body>
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="card mt-5">
                            <div class="card-header">
                                <h4>Book New Appointment</h4>
                            </div>
                            <div class="card-body">
                                ' . $appointmentForm . '
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script src="/assets/js/components.js"></script>
        </body>
        </html>';
    }
    
    public static function getChartExample() {
        $lineChart = ComponentFactory::render('lineChart', [
            'title' => 'Appointment Trends',
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            'data' => [
                [
                    'label' => 'Appointments',
                    'data' => [12, 19, 3, 5, 2, 3],
                    'borderColor' => 'rgb(75, 192, 192)',
                    'tension' => 0.1
                ]
            ]
        ]);
        
        $pieChart = ComponentFactory::render('pieChart', [
            'title' => 'Payment Methods',
            'labels' => ['Card', 'Cash'],
            'data' => [
                [
                    'data' => [70, 30],
                    'backgroundColor' => ['#FF6384', '#36A2EB']
                ]
            ]
        ]);
        
        return '
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Charts - ASAA Healthcare</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
            <link href="/assets/css/custom.css" rel="stylesheet">
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        </head>
        <body>
            <div class="container mt-5">
                <div class="row">
                    <div class="col-md-8">
                        ' . $lineChart . '
                    </div>
                    <div class="col-md-4">
                        ' . $pieChart . '
                    </div>
                </div>
            </div>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script src="/assets/js/components.js"></script>
        </body>
        </html>';
    }
}

// Usage examples
echo ComponentUsageExamples::getLoginPage();
// echo ComponentUsageExamples::getDashboardPage();
// echo ComponentUsageExamples::getAppointmentBookingPage();
// echo ComponentUsageExamples::getChartExample();
