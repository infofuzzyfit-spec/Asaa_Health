<?php
/**
 * Appointment Service
 * Handles appointment business logic
 */

require_once __DIR__ . '/../models/Appointment.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/EmailService.php';

class AppointmentService {
    private $appointmentModel;
    private $userModel;
    private $emailService;
    
    public function __construct() {
        $this->appointmentModel = new Appointment();
        $this->userModel = new User();
        $this->emailService = new EmailService();
    }
    
    public function bookAppointment($data) {
        // Validate required fields
        $requiredFields = ['patient_id', 'doctor_id', 'appointment_date', 'time_slot'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                throw new Exception("Field {$field} is required");
            }
        }
        
        // Check if slot is available (with database lock)
        if (!$this->appointmentModel->isSlotAvailable($data['doctor_id'], $data['appointment_date'], $data['time_slot'])) {
            throw new Exception("Time slot is no longer available");
        }
        
        // Validate appointment date (not in the past)
        if (strtotime($data['appointment_date']) < strtotime('today')) {
            throw new Exception("Cannot book appointments for past dates");
        }
        
        // Set default values
        $data['status'] = 'REVIEW';
        $data['payment_status'] = 'PENDING';
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        $data['created_by'] = $data['patient_id'];
        
        $appointmentId = $this->appointmentModel->create($data);
        
        if ($appointmentId) {
            // Send email notification
            $this->emailService->sendAppointmentStatusEmail($appointmentId, 'REVIEW');
        }
        
        return $appointmentId;
    }
    
    public function getUserAppointments($userId, $userRole) {
        return $this->appointmentModel->getAppointmentsByUser($userId, $userRole);
    }
    
    public function getAvailableSlots($doctorId, $date) {
        return $this->appointmentModel->getAvailableSlots($doctorId, $date);
    }
    
    public function cancelAppointment($appointmentId, $userId) {
        $appointment = $this->appointmentModel->find($appointmentId);
        
        if (!$appointment) {
            throw new Exception("Appointment not found");
        }
        
        // Check if user can cancel this appointment
        if ($appointment['patient_id'] != $userId) {
            throw new Exception("You can only cancel your own appointments");
        }
        
        // Check cancellation rules
        $appointmentDateTime = $appointment['appointment_date'] . ' ' . $appointment['time_slot'];
        $hoursUntilAppointment = (strtotime($appointmentDateTime) - time()) / 3600;
        
        if ($hoursUntilAppointment <= 3) {
            throw new Exception("Cannot cancel appointment less than 3 hours before scheduled time");
        }
        
        if ($appointment['payment_status'] === 'COMPLETED') {
            throw new Exception("Cannot cancel paid appointments");
        }
        
        // Update appointment status
        $result = $this->appointmentModel->updateStatus($appointmentId, 'CANCELLED');
        
        if ($result) {
            // Send email notification
            $this->emailService->sendAppointmentStatusEmail($appointmentId, 'CANCELLED');
        }
        
        return $result;
    }
    
    public function updateAppointmentStatus($appointmentId, $status) {
        $result = $this->appointmentModel->updateStatus($appointmentId, $status);
        
        if ($result) {
            // Send email notification
            $this->emailService->sendAppointmentStatusEmail($appointmentId, $status);
        }
        
        return $result;
    }
    
    public function getAppointmentReport($filters) {
        $dateFrom = $filters['date_from'] ?? date('Y-m-01');
        $dateTo = $filters['date_to'] ?? date('Y-m-t');
        
        return $this->appointmentModel->getAppointmentsByDateRange($dateFrom, $dateTo, $filters);
    }
}
