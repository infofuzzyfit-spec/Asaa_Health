<?php
/**
 * Report Service
 * Handles reports and analytics
 */

require_once __DIR__ . '/../models/Appointment.php';
require_once __DIR__ . '/../models/Payment.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Doctor.php';

class ReportService {
    private $appointmentModel;
    private $paymentModel;
    private $userModel;
    private $doctorModel;
    
    public function __construct() {
        $this->appointmentModel = new Appointment();
        $this->paymentModel = new Payment();
        $this->userModel = new User();
        $this->doctorModel = new Doctor();
    }
    
    public function getAdminStats() {
        $db = Database::getInstance();
        
        // Total appointments this month
        $totalAppointments = $db->fetch(
            "SELECT COUNT(*) as count FROM appointments WHERE MONTH(appointment_date) = MONTH(CURDATE()) AND YEAR(appointment_date) = YEAR(CURDATE())"
        )['count'];
        
        // Total revenue this month
        $totalRevenue = $this->paymentModel->getTotalRevenue(
            date('Y-m-01'),
            date('Y-m-t')
        );
        
        // New patients this month
        $newPatients = $db->fetch(
            "SELECT COUNT(*) as count FROM users WHERE role = 'Patient' AND MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())"
        )['count'];
        
        // Active doctors
        $activeDoctors = $db->fetch(
            "SELECT COUNT(*) as count FROM users WHERE role = 'Doctor' AND status = 'ACTIVE'"
        )['count'];
        
        // Recent appointments
        $recentAppointments = $this->appointmentModel->getAppointmentsByDateRange(
            date('Y-m-d', strtotime('-7 days')),
            date('Y-m-d')
        );
        
        // Revenue by method
        $revenueByMethod = $this->paymentModel->getRevenueByMethod(
            date('Y-m-01'),
            date('Y-m-t')
        );
        
        return [
            'total_appointments' => $totalAppointments,
            'total_revenue' => $totalRevenue,
            'new_patients' => $newPatients,
            'active_doctors' => $activeDoctors,
            'recent_appointments' => $recentAppointments,
            'revenue_by_method' => $revenueByMethod
        ];
    }
    
    public function getStaffStats() {
        $db = Database::getInstance();
        
        // Today's appointments
        $todayAppointments = $db->fetch(
            "SELECT COUNT(*) as count FROM appointments WHERE appointment_date = CURDATE()"
        )['count'];
        
        // Pending appointments
        $pendingAppointments = $db->fetch(
            "SELECT COUNT(*) as count FROM appointments WHERE status = 'REVIEW'"
        )['count'];
        
        // Completed appointments today
        $completedToday = $db->fetch(
            "SELECT COUNT(*) as count FROM appointments WHERE appointment_date = CURDATE() AND status = 'COMPLETED'"
        )['count'];
        
        // Pending payments
        $pendingPayments = $db->fetch(
            "SELECT COUNT(*) as count FROM payments WHERE payment_status = 'PENDING'"
        )['count'];
        
        return [
            'today_appointments' => $todayAppointments,
            'pending_appointments' => $pendingAppointments,
            'completed_today' => $completedToday,
            'pending_payments' => $pendingPayments
        ];
    }
    
    public function getDoctorStats($doctorId) {
        $stats = $this->doctorModel->getDoctorStats($doctorId);
        
        // Today's appointments
        $db = Database::getInstance();
        $todayAppointments = $db->fetch(
            "SELECT COUNT(*) as count FROM appointments WHERE doctor_id = ? AND appointment_date = CURDATE()",
            [$doctorId]
        )['count'];
        
        $stats['today_appointments'] = $todayAppointments;
        
        return $stats;
    }
    
    public function getPatientStats($patientId) {
        $db = Database::getInstance();
        
        // Total appointments
        $totalAppointments = $db->fetch(
            "SELECT COUNT(*) as count FROM appointments WHERE patient_id = ?",
            [$patientId]
        )['count'];
        
        // Upcoming appointments
        $upcomingAppointments = $db->fetch(
            "SELECT COUNT(*) as count FROM appointments WHERE patient_id = ? AND appointment_date >= CURDATE() AND status IN ('REVIEW', 'ACCEPTED')",
            [$patientId]
        )['count'];
        
        // Completed appointments
        $completedAppointments = $db->fetch(
            "SELECT COUNT(*) as count FROM appointments WHERE patient_id = ? AND status = 'COMPLETED'",
            [$patientId]
        )['count'];
        
        // Medical records
        $medicalRecords = $db->fetch(
            "SELECT COUNT(*) as count FROM medical_records WHERE patient_id = ?",
            [$patientId]
        )['count'];
        
        return [
            'total_appointments' => $totalAppointments,
            'upcoming_appointments' => $upcomingAppointments,
            'completed_appointments' => $completedAppointments,
            'medical_records' => $medicalRecords
        ];
    }
    
    public function getAppointmentReport($filters) {
        return $this->appointmentModel->getAppointmentsByDateRange(
            $filters['date_from'] ?? date('Y-m-01'),
            $filters['date_to'] ?? date('Y-m-t'),
            $filters
        );
    }
    
    public function getRevenueReport($filters) {
        return $this->paymentModel->getRevenueByDateRange(
            $filters['date_from'] ?? date('Y-m-01'),
            $filters['date_to'] ?? date('Y-m-t'),
            $filters
        );
    }
    
    public function getPatientReport($filters) {
        $db = Database::getInstance();
        
        $sql = "SELECT u.*, 
                       COUNT(a.appointment_id) as total_appointments,
                       MAX(a.appointment_date) as last_appointment
                FROM users u 
                LEFT JOIN appointments a ON u.user_id = a.patient_id 
                WHERE u.role = 'Patient'";
        
        $params = [];
        
        if (!empty($filters['date_from']) && !empty($filters['date_to'])) {
            $sql .= " AND u.created_at BETWEEN ? AND ?";
            $params[] = $filters['date_from'];
            $params[] = $filters['date_to'];
        }
        
        if (!empty($filters['gender'])) {
            $sql .= " AND u.gender = ?";
            $params[] = $filters['gender'];
        }
        
        $sql .= " GROUP BY u.user_id ORDER BY u.created_at DESC";
        
        return $db->fetchAll($sql, $params);
    }
}
