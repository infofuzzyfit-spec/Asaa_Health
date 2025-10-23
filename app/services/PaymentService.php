<?php
/**
 * Payment Service
 * Handles payment processing and PayHere integration
 */

require_once __DIR__ . '/../models/Payment.php';
require_once __DIR__ . '/../models/Appointment.php';
require_once __DIR__ . '/../../config/payhere.php';
require_once __DIR__ . '/EmailService.php';

class PaymentService {
    private $paymentModel;
    private $appointmentModel;
    private $emailService;
    
    public function __construct() {
        $this->paymentModel = new Payment();
        $this->appointmentModel = new Appointment();
        $this->emailService = new EmailService();
    }
    
    public function initiateCardPayment($appointmentId, $amount) {
        $appointment = $this->appointmentModel->find($appointmentId);
        
        if (!$appointment) {
            throw new Exception("Appointment not found");
        }
        
        // Create payment record
        $paymentData = [
            'appointment_id' => $appointmentId,
            'patient_id' => $appointment['patient_id'],
            'doctor_id' => $appointment['doctor_id'],
            'amount' => $amount,
            'payment_method' => 'CARD',
            'payment_status' => 'PENDING',
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $paymentId = $this->paymentModel->create($paymentData);
        
        if (!$paymentId) {
            throw new Exception("Failed to create payment record");
        }
        
        // Generate PayHere payment data
        $orderId = "APP_{$appointmentId}_{$paymentId}";
        $hash = $this->generatePayHereHash($orderId, $amount);
        
        return [
            'merchant_id' => PAYHERE_MERCHANT_ID,
            'return_url' => PAYHERE_RETURN_URL,
            'cancel_url' => PAYHERE_CANCEL_URL,
            'notify_url' => PAYHERE_NOTIFY_URL,
            'order_id' => $orderId,
            'items' => "Appointment #{$appointmentId}",
            'currency' => PAYHERE_CURRENCY,
            'amount' => $amount,
            'hash' => $hash,
            'first_name' => $appointment['patient_first_name'] ?? '',
            'last_name' => $appointment['patient_last_name'] ?? '',
            'email' => $appointment['patient_email'] ?? '',
            'phone' => $appointment['patient_mobile'] ?? '',
            'address' => 'Colombo, Sri Lanka',
            'city' => 'Colombo',
            'country' => 'Sri Lanka'
        ];
    }
    
    public function processCashPayment($appointmentId, $amount, $paidBy) {
        $appointment = $this->appointmentModel->find($appointmentId);
        
        if (!$appointment) {
            throw new Exception("Appointment not found");
        }
        
        // Create payment record
        $paymentData = [
            'appointment_id' => $appointmentId,
            'patient_id' => $appointment['patient_id'],
            'doctor_id' => $appointment['doctor_id'],
            'amount' => $amount,
            'payment_method' => 'CASH',
            'payment_status' => 'COMPLETED',
            'payment_date' => date('Y-m-d H:i:s'),
            'paid_by' => $paidBy,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $paymentId = $this->paymentModel->create($paymentData);
        
        if ($paymentId) {
            // Update appointment payment status
            $this->appointmentModel->update($appointmentId, ['payment_status' => 'COMPLETED']);
        }
        
        return $paymentId;
    }
    
    public function handlePayHereNotification($postData) {
        $orderId = $postData['order_id'] ?? '';
        $statusCode = $postData['status_code'] ?? '';
        $amount = $postData['payhere_amount'] ?? 0;
        $md5sig = $postData['md5sig'] ?? '';
        
        // Verify hash
        $expectedHash = $this->generatePayHereHash($orderId, $amount);
        if (strtoupper($md5sig) !== $expectedHash) {
            error_log("PayHere hash verification failed for order: {$orderId}");
            return false;
        }
        
        // Extract appointment and payment IDs from order ID
        if (preg_match('/APP_(\d+)_(\d+)/', $orderId, $matches)) {
            $appointmentId = $matches[1];
            $paymentId = $matches[2];
            
            if ($statusCode == '2') { // Success
                // Update payment status
                $this->paymentModel->updatePaymentStatus($paymentId, 'COMPLETED', $orderId);
                
                // Update appointment payment status
                $this->appointmentModel->update($appointmentId, ['payment_status' => 'COMPLETED']);
                
                // Send confirmation email
                $this->emailService->sendAppointmentStatusEmail($appointmentId, 'ACCEPTED');
            } else {
                // Payment failed
                $this->paymentModel->updatePaymentStatus($paymentId, 'PENDING');
            }
        }
        
        return true;
    }
    
    public function getPaymentHistory($userId, $userRole) {
        return $this->paymentModel->getPaymentsByUser($userId, $userRole);
    }
    
    public function getRevenueReport($filters) {
        $dateFrom = $filters['date_from'] ?? date('Y-m-01');
        $dateTo = $filters['date_to'] ?? date('Y-m-t');
        
        return $this->paymentModel->getRevenueByDateRange($dateFrom, $dateTo, $filters);
    }
    
    public function getTotalRevenue($dateFrom = null, $dateTo = null) {
        return $this->paymentModel->getTotalRevenue($dateFrom, $dateTo);
    }
    
    public function getRevenueByMethod($dateFrom = null, $dateTo = null) {
        return $this->paymentModel->getRevenueByMethod($dateFrom, $dateTo);
    }
    
    private function generatePayHereHash($orderId, $amount) {
        $hashedSecret = strtoupper(md5(PAYHERE_MERCHANT_SECRET));
        $amountFormatted = number_format($amount, 2, '.', '');
        
        return strtoupper(md5(
            PAYHERE_MERCHANT_ID . $orderId . $amountFormatted . PAYHERE_CURRENCY . $hashedSecret
        ));
    }
}
