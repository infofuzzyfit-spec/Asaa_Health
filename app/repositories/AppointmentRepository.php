<?php
/**
 * Appointment Repository
 * Data access layer for appointment operations
 */

require_once __DIR__ . '/../../core/Database.php';

class AppointmentRepository {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function findById($id) {
        $sql = "SELECT * FROM appointments WHERE appointment_id = ?";
        return $this->db->fetch($sql, [$id]);
    }
    
    public function findAll($conditions = [], $orderBy = null, $limit = null) {
        $sql = "SELECT * FROM appointments";
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
        
        $sql = "INSERT INTO appointments (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $placeholders) . ")";
        
        $this->db->query($sql, array_values($data));
        return $this->db->lastInsertId();
    }
    
    public function update($id, $data) {
        $columns = array_keys($data);
        $setClause = [];
        
        foreach ($columns as $column) {
            $setClause[] = "{$column} = ?";
        }
        
        $sql = "UPDATE appointments SET " . implode(', ', $setClause) . " WHERE appointment_id = ?";
        $params = array_values($data);
        $params[] = $id;
        
        return $this->db->query($sql, $params);
    }
    
    public function delete($id) {
        $sql = "DELETE FROM appointments WHERE appointment_id = ?";
        return $this->db->query($sql, [$id]);
    }
    
    public function getAppointmentsByUser($userId, $userRole) {
        if ($userRole === 'Patient') {
            $sql = "SELECT a.*, u.first_name, u.last_name, s.specialization_name 
                    FROM appointments a 
                    JOIN users u ON a.doctor_id = u.user_id 
                    LEFT JOIN doctor_details dd ON u.user_id = dd.user_id 
                    LEFT JOIN specializations s ON dd.specialization_id = s.specialization_id 
                    WHERE a.patient_id = ? 
                    ORDER BY a.appointment_date DESC, a.time_slot DESC";
            return $this->db->fetchAll($sql, [$userId]);
        } elseif ($userRole === 'Doctor') {
            $sql = "SELECT a.*, u.first_name, u.last_name 
                    FROM appointments a 
                    JOIN users u ON a.patient_id = u.user_id 
                    WHERE a.doctor_id = ? 
                    ORDER BY a.appointment_date DESC, a.time_slot DESC";
            return $this->db->fetchAll($sql, [$userId]);
        } else {
            // Admin/Staff - all appointments
            $sql = "SELECT a.*, 
                           p.first_name as patient_first_name, p.last_name as patient_last_name,
                           d.first_name as doctor_first_name, d.last_name as doctor_last_name,
                           s.specialization_name 
                    FROM appointments a 
                    JOIN users p ON a.patient_id = p.user_id 
                    JOIN users d ON a.doctor_id = d.user_id 
                    LEFT JOIN doctor_details dd ON d.user_id = dd.user_id 
                    LEFT JOIN specializations s ON dd.specialization_id = s.specialization_id 
                    ORDER BY a.appointment_date DESC, a.time_slot DESC";
            return $this->db->fetchAll($sql);
        }
    }
    
    public function getAvailableSlots($doctorId, $date) {
        $sql = "SELECT time_slot 
                FROM appointments 
                WHERE doctor_id = ? AND appointment_date = ? 
                AND status NOT IN ('CANCELLED')";
        $bookedSlots = $this->db->fetchAll($sql, [$doctorId, $date]);
        
        $allSlots = $this->generateTimeSlots();
        $bookedSlotsArray = array_column($bookedSlots, 'time_slot');
        
        return array_diff($allSlots, $bookedSlotsArray);
    }
    
    public function isSlotAvailable($doctorId, $date, $timeSlot) {
        $sql = "SELECT COUNT(*) as count 
                FROM appointments 
                WHERE doctor_id = ? AND appointment_date = ? AND time_slot = ? 
                AND status NOT IN ('CANCELLED') 
                FOR UPDATE";
        $result = $this->db->fetch($sql, [$doctorId, $date, $timeSlot]);
        return $result['count'] == 0;
    }
    
    public function updateStatus($appointmentId, $status) {
        $sql = "UPDATE appointments SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE appointment_id = ?";
        return $this->db->query($sql, [$status, $appointmentId]);
    }
    
    public function getAppointmentsByDateRange($dateFrom, $dateTo, $filters = []) {
        $sql = "SELECT a.*, 
                       p.first_name as patient_first_name, p.last_name as patient_last_name,
                       d.first_name as doctor_first_name, d.last_name as doctor_last_name,
                       s.specialization_name 
                FROM appointments a 
                JOIN users p ON a.patient_id = p.user_id 
                JOIN users d ON a.doctor_id = d.user_id 
                LEFT JOIN doctor_details dd ON d.user_id = dd.user_id 
                LEFT JOIN specializations s ON dd.specialization_id = s.specialization_id 
                WHERE a.appointment_date BETWEEN ? AND ?";
        
        $params = [$dateFrom, $dateTo];
        
        if (!empty($filters['status'])) {
            $sql .= " AND a.status = ?";
            $params[] = $filters['status'];
        }
        
        if (!empty($filters['doctor_id'])) {
            $sql .= " AND a.doctor_id = ?";
            $params[] = $filters['doctor_id'];
        }
        
        $sql .= " ORDER BY a.appointment_date DESC, a.time_slot DESC";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    private function generateTimeSlots() {
        $slots = [];
        $start = strtotime('08:00');
        $end = strtotime('22:00');
        $lunchStart = strtotime('12:00');
        $lunchEnd = strtotime('13:00');
        
        for ($time = $start; $time < $end; $time += 3600) { // 1 hour slots
            if ($time >= $lunchStart && $time < $lunchEnd) {
                continue; // Skip lunch break
            }
            $slots[] = date('H:i', $time);
        }
        
        return $slots;
    }
}
