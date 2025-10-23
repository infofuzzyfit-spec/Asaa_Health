<?php
/**
 * Medical Record Repository
 * Data access layer for medical record operations
 */

require_once __DIR__ . '/../../core/Database.php';

class MedicalRecordRepository {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function findById($id) {
        $sql = "SELECT * FROM medical_records WHERE record_id = ?";
        return $this->db->fetch($sql, [$id]);
    }
    
    public function findAll($conditions = [], $orderBy = null, $limit = null) {
        $sql = "SELECT * FROM medical_records";
        $params = [];
        
        if (!empty($conditions)) {
            $whereClause = [];
            foreach ($conditions as $column => $value) {
                $whereClause[] = "{$column} = ?";
                $params[] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $whereClause);
        }
        
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }
        
        if ($limit) {
            $sql .= " LIMIT {$limit}";
        }
        
        return $this->db->fetchAll($sql, $params);
    }
    
    public function create($data) {
        $columns = array_keys($data);
        $placeholders = array_fill(0, count($columns), '?');
        
        $sql = "INSERT INTO medical_records (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $placeholders) . ")";
        
        $this->db->query($sql, array_values($data));
        return $this->db->lastInsertId();
    }
    
    public function update($id, $data) {
        $columns = array_keys($data);
        $setClause = [];
        
        foreach ($columns as $column) {
            $setClause[] = "{$column} = ?";
        }
        
        $sql = "UPDATE medical_records SET " . implode(', ', $setClause) . " WHERE record_id = ?";
        $params = array_values($data);
        $params[] = $id;
        
        return $this->db->query($sql, $params);
    }
    
    public function delete($id) {
        $sql = "DELETE FROM medical_records WHERE record_id = ?";
        return $this->db->query($sql, [$id]);
    }
    
    public function getRecordsByPatient($patientId) {
        $sql = "SELECT mr.*, a.appointment_date, a.time_slot,
                       d.first_name as doctor_first_name, d.last_name as doctor_last_name,
                       s.specialization_name
                FROM medical_records mr 
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
                FROM medical_records mr 
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
                FROM medical_records mr 
                JOIN appointments a ON mr.appointment_id = a.appointment_id 
                JOIN users p ON mr.patient_id = p.user_id 
                JOIN users d ON mr.doctor_id = d.user_id 
                LEFT JOIN doctor_details dd ON d.user_id = dd.user_id 
                LEFT JOIN specializations s ON dd.specialization_id = s.specialization_id 
                WHERE mr.record_id = ?";
        return $this->db->fetch($sql, [$recordId]);
    }
    
    public function getRecordsByAppointment($appointmentId) {
        $sql = "SELECT * FROM medical_records WHERE appointment_id = ?";
        return $this->db->fetch($sql, [$appointmentId]);
    }
    
    public function getPatientHistory($patientId, $limit = null) {
        $sql = "SELECT mr.*, a.appointment_date, a.time_slot,
                       d.first_name as doctor_first_name, d.last_name as doctor_last_name,
                       s.specialization_name
                FROM medical_records mr 
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
