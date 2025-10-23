<?php
/**
 * Report Controller
 * Handles reports and analytics
 */

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../services/ReportService.php';

class ReportController extends Controller {
    private $reportService;
    
    public function __construct() {
        parent::__construct();
        $this->reportService = new ReportService();
    }
    
    public function dashboard() {
        $userRole = $this->session->get('user_role');
        
        switch ($userRole) {
            case 'Admin':
                $this->adminDashboard();
                break;
            case 'Staff':
                $this->staffDashboard();
                break;
            case 'Doctor':
                $this->doctorDashboard();
                break;
            case 'Patient':
                $this->patientDashboard();
                break;
            default:
                $this->redirect('/login');
        }
    }
    
    private function adminDashboard() {
        $stats = $this->reportService->getAdminStats();
        
        $this->view('admin/dashboard', [
            'stats' => $stats,
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }
    
    private function staffDashboard() {
        $stats = $this->reportService->getStaffStats();
        
        $this->view('staff/dashboard', [
            'stats' => $stats,
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }
    
    private function doctorDashboard() {
        $doctorId = $this->session->get('user_id');
        $stats = $this->reportService->getDoctorStats($doctorId);
        
        $this->view('doctor/dashboard', [
            'stats' => $stats,
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }
    
    private function patientDashboard() {
        $patientId = $this->session->get('user_id');
        $stats = $this->reportService->getPatientStats($patientId);
        
        $this->view('frontend/dashboard', [
            'stats' => $stats,
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }
    
    public function appointments() {
        $filters = [
            'date_from' => $this->request->get('date_from'),
            'date_to' => $this->request->get('date_to'),
            'status' => $this->request->get('status'),
            'doctor_id' => $this->request->get('doctor_id')
        ];
        
        $appointments = $this->reportService->getAppointmentReport($filters);
        
        $this->view('admin/reports/appointments', [
            'appointments' => $appointments,
            'filters' => $filters,
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }
    
    public function revenue() {
        $filters = [
            'date_from' => $this->request->get('date_from'),
            'date_to' => $this->request->get('date_to'),
            'payment_method' => $this->request->get('payment_method')
        ];
        
        $revenue = $this->reportService->getRevenueReport($filters);
        
        $this->view('admin/reports/revenue', [
            'revenue' => $revenue,
            'filters' => $filters,
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }
    
    public function patients() {
        $filters = [
            'date_from' => $this->request->get('date_from'),
            'date_to' => $this->request->get('date_to'),
            'gender' => $this->request->get('gender')
        ];
        
        $patients = $this->reportService->getPatientReport($filters);
        
        $this->view('admin/reports/patients', [
            'patients' => $patients,
            'filters' => $filters,
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }
}
