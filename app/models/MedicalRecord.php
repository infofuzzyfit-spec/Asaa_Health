<?php
/**
 * Medical Record Model
 * Handles medical record data operations
 */

require_once __DIR__ . '/../../core/Model.php';

class MedicalRecord extends Model {
    protected $table = 'medical_records';
    protected $primaryKey = 'record_id';
    
    public function getRecordsByPatient($patientId) {
        $sql = "SELECT mr.*, a.appointment_date, a.time_slot,
                       d.first_name as doctor_first_name, d.last_name as doctor_last_name,
                       s.specialization_name
                FROM {$this->table} mr 
                JOIN appointments a ON mr.appointment_id = a.appointment_id 
                JOIN users d ON mr.doctor_id = d.user_id 
                LEFT JOIN doctor_details dd ON d.user_id = dd.user_id 
                LEFT JOIN specializations s ON dd.specialization_id = s.specialization_id 
                WHERE mr.patient_id = ? 
                ORDER BY mr.created_at DESC";
        return $this->db->fetchAll($sql, [$patientId]);
    }
    
    public function getRecordsByDoctor($doctorId) {
        $sql = "SELECT mr.*, a.appointment_date, a.time_slot,
                       p.first_name as patient_first_name, p.last_name as patient_last_name
                FROM {$this->table} mr 
                JOIN appointments a ON mr.appointment_id = a.appointment_id 
                JOIN users p ON mr.patient_id = p.user_id 
                WHERE mr.doctor_id = ? 
                ORDER BY mr.created_at DESC";
        return $this->db->fetchAll($sql, [$doctorId]);
    }
    
    public function getRecordWithDetails($recordId) {
        $sql = "SELECT mr.*, a.appointment_date, a.time_slot, a.status as appointment_status,
                       p.first_name as patient_first_name, p.last_name as patient_last_name,
                       d.first_name as doctor_first_name, d.last_name as doctor_last_name,
                       s.specialization_name
                FROM {$this->table} mr 
                JOIN appointments a ON mr.appointment_id = a.appointment_id 
                JOIN users p ON mr.patient_id = p.user_id 
                JOIN users d ON mr.doctor_id = d.user_id 
                LEFT JOIN doctor_details dd ON d.user_id = dd.user_id 
                LEFT JOIN specializations s ON dd.specialization_id = s.specialization_id 
                WHERE mr.record_id = ?";
        return $this->db->fetch($sql, [$recordId]);
    }
    
    public function getRecordsByAppointment($appointmentId) {
        $sql = "SELECT * FROM {$this->table} WHERE appointment_id = ?";
        return $this->db->fetch($sql, [$appointmentId]);
    }
    
    public function getPatientHistory($patientId, $limit = null) {
        $sql = "SELECT mr.*, a.appointment_date, a.time_slot,
                       d.first_name as doctor_first_name, d.last_name as doctor_last_name,
                       s.specialization_name
                FROM {$this->table} mr 
                JOIN appointments a ON mr.appointment_id = a.appointment_id 
                JOIN users d ON mr.doctor_id = d.user_id 
                LEFT JOIN doctor_details dd ON d.user_id = dd.user_id 
                LEFT JOIN specializations s ON dd.specialization_id = s.specialization_id 
                WHERE mr.patient_id = ? 
                ORDER BY mr.created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT {$limit}";
        }
        
        return $this->db->fetchAll($sql, [$patientId]);
    }
}
