<?php
/**
 * Payment Model
 * Handles payment data operations
 */

require_once __DIR__ . '/../../core/Model.php';

class Payment extends Model {
    protected $table = 'payments';
    protected $primaryKey = 'payment_id';
    
    public function getPaymentsByAppointment($appointmentId) {
        $sql = "SELECT * FROM {$this->table} WHERE appointment_id = ?";
        return $this->db->fetch($sql, [$appointmentId]);
    }
    
    public function getPaymentsByUser($userId, $userRole) {
        if ($userRole === 'Patient') {
            $sql = "SELECT p.*, a.appointment_date, a.time_slot,
                           d.first_name as doctor_first_name, d.last_name as doctor_last_name
                    FROM {$this->table} p 
                    JOIN appointments a ON p.appointment_id = a.appointment_id 
                    JOIN users d ON p.doctor_id = d.user_id 
                    WHERE p.patient_id = ? 
                    ORDER BY p.created_at DESC";
            return $this->db->fetchAll($sql, [$userId]);
        } else {
            // Admin/Staff - all payments
            $sql = "SELECT p.*, a.appointment_date, a.time_slot,
                           pat.first_name as patient_first_name, pat.last_name as patient_last_name,
                           d.first_name as doctor_first_name, d.last_name as doctor_last_name
                    FROM {$this->table} p 
                    JOIN appointments a ON p.appointment_id = a.appointment_id 
                    JOIN users pat ON p.patient_id = pat.user_id 
                    JOIN users d ON p.doctor_id = d.user_id 
                    ORDER BY p.created_at DESC";
            return $this->db->fetchAll($sql);
        }
    }
    
    public function getRevenueByDateRange($dateFrom, $dateTo, $filters = []) {
        $sql = "SELECT p.*, a.appointment_date,
                       pat.first_name as patient_first_name, pat.last_name as patient_last_name,
                       d.first_name as doctor_first_name, d.last_name as doctor_last_name
                FROM {$this->table} p 
                JOIN appointments a ON p.appointment_id = a.appointment_id 
                JOIN users pat ON p.patient_id = pat.user_id 
                JOIN users d ON p.doctor_id = d.user_id 
                WHERE p.payment_status = 'COMPLETED' 
                AND a.appointment_date BETWEEN ? AND ?";
        
        $params = [$dateFrom, $dateTo];
        
        if (!empty($filters['payment_method'])) {
            $sql .= " AND p.payment_method = ?";
            $params[] = $filters['payment_method'];
        }
        
        $sql .= " ORDER BY a.appointment_date DESC";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    public function getTotalRevenue($dateFrom = null, $dateTo = null) {
        $sql = "SELECT SUM(amount) as total_revenue FROM {$this->table} WHERE payment_status = 'COMPLETED'";
        $params = [];
        
        if ($dateFrom && $dateTo) {
            $sql .= " AND payment_date BETWEEN ? AND ?";
            $params = [$dateFrom, $dateTo];
        }
        
        $result = $this->db->fetch($sql, $params);
        return $result['total_revenue'] ?? 0;
    }
    
    public function getRevenueByMethod($dateFrom = null, $dateTo = null) {
        $sql = "SELECT payment_method, SUM(amount) as total 
                FROM {$this->table} 
                WHERE payment_status = 'COMPLETED'";
        $params = [];
        
        if ($dateFrom && $dateTo) {
            $sql .= " AND payment_date BETWEEN ? AND ?";
            $params = [$dateFrom, $dateTo];
        }
        
        $sql .= " GROUP BY payment_method";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    public function updatePaymentStatus($paymentId, $status, $transactionRef = null) {
        $data = [
            'payment_status' => $status,
            'payment_date' => $status === 'COMPLETED' ? date('Y-m-d H:i:s') : null
        ];
        
        if ($transactionRef) {
            $data['transaction_reference'] = $transactionRef;
        }
        
        return $this->update($paymentId, $data);
    }
}
