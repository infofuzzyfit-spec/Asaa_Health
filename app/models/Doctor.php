<?php
/**
 * Doctor Model
 * Handles doctor-specific data operations
 */

require_once __DIR__ . '/../../core/Model.php';

class Doctor extends Model {
    protected $table = 'doctor_details';
    protected $primaryKey = 'doctor_detail_id';
    
    public function getDoctorWithSpecialization($userId) {
        $sql = "SELECT dd.*, u.first_name, u.last_name, u.email, u.mobile_number,
                       s.specialization_name, s.description
                FROM {$this->table} dd 
                JOIN users u ON dd.user_id = u.user_id 
                JOIN specializations s ON dd.specialization_id = s.specialization_id 
                WHERE dd.user_id = ?";
        return $this->db->fetch($sql, [$userId]);
    }
    
    public function getAllDoctorsWithSpecializations() {
        $sql = "SELECT dd.*, u.first_name, u.last_name, u.email, u.mobile_number, u.status,
                       s.specialization_name, s.description
                FROM {$this->table} dd 
                JOIN users u ON dd.user_id = u.user_id 
                JOIN specializations s ON dd.specialization_id = s.specialization_id 
                WHERE u.role = 'Doctor' AND u.status = 'ACTIVE'
                ORDER BY u.first_name, u.last_name";
        return $this->db->fetchAll($sql);
    }
    
    public function getDoctorsBySpecialization($specializationId) {
        $sql = "SELECT dd.*, u.first_name, u.last_name, u.email, u.mobile_number,
                       s.specialization_name
                FROM {$this->table} dd 
                JOIN users u ON dd.user_id = u.user_id 
                JOIN specializations s ON dd.specialization_id = s.specialization_id 
                WHERE dd.specialization_id = ? AND u.status = 'ACTIVE'
                ORDER BY u.first_name, u.last_name";
        return $this->db->fetchAll($sql, [$specializationId]);
    }
    
    public function searchDoctors($query) {
        $sql = "SELECT dd.*, u.first_name, u.last_name, u.email, u.mobile_number,
                       s.specialization_name
                FROM {$this->table} dd 
                JOIN users u ON dd.user_id = u.user_id 
                JOIN specializations s ON dd.specialization_id = s.specialization_id 
                WHERE u.status = 'ACTIVE' 
                AND (u.first_name LIKE ? OR u.last_name LIKE ? OR s.specialization_name LIKE ?)
                ORDER BY u.first_name, u.last_name";
        $searchTerm = "%{$query}%";
        return $this->db->fetchAll($sql, [$searchTerm, $searchTerm, $searchTerm]);
    }
    
    public function getDoctorStats($doctorId) {
        $sql = "SELECT 
                    COUNT(DISTINCT a.appointment_id) as total_appointments,
                    COUNT(DISTINCT CASE WHEN a.status = 'COMPLETED' THEN a.appointment_id END) as completed_appointments,
                    COUNT(DISTINCT CASE WHEN a.status = 'CANCELLED' THEN a.appointment_id END) as cancelled_appointments,
                    COUNT(DISTINCT a.patient_id) as unique_patients,
                    AVG(p.amount) as avg_consultation_fee
                FROM appointments a 
                LEFT JOIN payments p ON a.appointment_id = p.appointment_id 
                WHERE a.doctor_id = ? 
                AND a.appointment_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
        
        return $this->db->fetch($sql, [$doctorId]);
    }
}
