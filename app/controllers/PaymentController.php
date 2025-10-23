<?php
/**
 * Payment Controller
 * Handles payment processing
 */

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../services/PaymentService.php';

class PaymentController extends Controller {
    private $paymentService;
    
    public function __construct() {
        parent::__construct();
        $this->paymentService = new PaymentService();
    }
    
    public function processCard() {
        if ($this->isPost()) {
            try {
                $this->validateCsrf();
                
                $appointmentId = $this->request->post('appointment_id');
                $amount = $this->request->post('amount');
                
                $paymentData = $this->paymentService->initiateCardPayment($appointmentId, $amount);
                
                if ($paymentData) {
                    $this->view('payment/payhere-form', $paymentData);
                } else {
                    $this->session->flash('error', 'Failed to initiate payment');
                    $this->redirect('/appointments');
                }
            } catch (Exception $e) {
                $this->session->flash('error', $e->getMessage());
                $this->redirect('/appointments');
            }
        }
    }
    
    public function processCash() {
        if ($this->isPost()) {
            try {
                $this->validateCsrf();
                
                $appointmentId = $this->request->post('appointment_id');
                $amount = $this->request->post('amount');
                $paidBy = $this->session->get('user_id');
                
                $result = $this->paymentService->processCashPayment($appointmentId, $amount, $paidBy);
                
                if ($result) {
                    $this->session->flash('success', 'Cash payment recorded successfully');
                } else {
                    $this->session->flash('error', 'Failed to record cash payment');
                }
            } catch (Exception $e) {
                $this->session->flash('error', $e->getMessage());
            }
        }
        
        $this->redirect('/appointments');
    }
    
    public function notify() {
        // PayHere webhook handler
        $this->paymentService->handlePayHereNotification($_POST);
    }
    
    public function return() {
        $status = $this->request->get('status');
        $orderId = $this->request->get('order_id');
        
        if ($status === '2') {
            $this->session->flash('success', 'Payment completed successfully');
        } else {
            $this->session->flash('error', 'Payment failed or was cancelled');
        }
        
        $this->redirect('/appointments');
    }
    
    public function cancel() {
        $this->session->flash('error', 'Payment was cancelled');
        $this->redirect('/appointments');
    }
}
