<?php
/**
 * Medical Record Service
 * Handles medical record business logic
 */

require_once __DIR__ . '/../models/MedicalRecord.php';
require_once __DIR__ . '/../models/Appointment.php';

class MedicalRecordService {
    private $medicalRecordModel;
    private $appointmentModel;
    
    public function __construct() {
        $this->medicalRecordModel = new MedicalRecord();
        $this->appointmentModel = new Appointment();
    }
    
    public function createRecord($data) {
        // Validate required fields
        $requiredFields = ['appointment_id', 'patient_id', 'doctor_id', 'current_diagnosis'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                throw new Exception("Field {$field} is required");
            }
        }
        
        // Check if appointment exists and is completed
        $appointment = $this->appointmentModel->find($data['appointment_id']);
        if (!$appointment) {
            throw new Exception("Appointment not found");
        }
        
        if ($appointment['status'] !== 'COMPLETED') {
            throw new Exception("Can only add medical records for completed appointments");
        }
        
        // Check if record already exists for this appointment
        $existingRecord = $this->medicalRecordModel->getRecordsByAppointment($data['appointment_id']);
        if ($existingRecord) {
            throw new Exception("Medical record already exists for this appointment");
        }
        
        // Set default values
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        return $this->medicalRecordModel->create($data);
    }
    
    public function getUserMedicalRecords($userId, $userRole) {
        if ($userRole === 'Patient') {
            return $this->medicalRecordModel->getRecordsByPatient($userId);
        } elseif ($userRole === 'Doctor') {
            return $this->medicalRecordModel->getRecordsByDoctor($userId);
        } else {
            // Admin/Staff - all records
            return $this->medicalRecordModel->findAll([], 'created_at DESC');
        }
    }
    
    public function getRecordById($id) {
        return $this->medicalRecordModel->getRecordWithDetails($id);
    }
    
    public function updateRecord($id, $data) {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->medicalRecordModel->update($id, $data);
    }
    
    public function getPatientHistory($patientId, $limit = null) {
        return $this->medicalRecordModel->getPatientHistory($patientId, $limit);
    }
    
    public function getRecordsByAppointment($appointmentId) {
        return $this->medicalRecordModel->getRecordsByAppointment($appointmentId);
    }
}
