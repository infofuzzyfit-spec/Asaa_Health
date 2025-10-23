<?php
/**
 * Appointment Controller
 * Handles appointment booking and management
 */

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../services/AppointmentService.php';

class AppointmentController extends Controller {
    private $appointmentService;
    
    public function __construct() {
        parent::__construct();
        $this->appointmentService = new AppointmentService();
    }
    
    public function index() {
        $userId = $this->session->get('user_id');
        $userRole = $this->session->get('user_role');
        
        $appointments = $this->appointmentService->getUserAppointments($userId, $userRole);
        
        $this->view('frontend/my-appointments', [
            'appointments' => $appointments,
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }
    
    public function book() {
        if ($this->isPost()) {
            try {
                $this->validateCsrf();
                
                $data = [
                    'patient_id' => $this->session->get('user_id'),
                    'doctor_id' => $this->request->post('doctor_id'),
                    'appointment_date' => $this->request->post('appointment_date'),
                    'time_slot' => $this->request->post('time_slot'),
                    'notes' => $this->request->post('notes')
                ];
                
                $appointmentId = $this->appointmentService->bookAppointment($data);
                
                if ($appointmentId) {
                    $this->session->flash('success', 'Appointment booked successfully');
                    $this->redirect('/appointments');
                } else {
                    $this->session->flash('error', 'Failed to book appointment');
                    $this->redirect('/appointments/book');
                }
            } catch (Exception $e) {
                $this->session->flash('error', $e->getMessage());
                $this->redirect('/appointments/book');
            }
        } else {
            $this->view('frontend/appointment-booking', [
                'csrf_token' => $this->generateCsrfToken()
            ]);
        }
    }
    
    public function cancel($id) {
        try {
            $this->validateCsrf();
            
            $result = $this->appointmentService->cancelAppointment($id, $this->session->get('user_id'));
            
            if ($result) {
                $this->session->flash('success', 'Appointment cancelled successfully');
            } else {
                $this->session->flash('error', 'Failed to cancel appointment');
            }
        } catch (Exception $e) {
            $this->session->flash('error', $e->getMessage());
        }
        
        $this->redirect('/appointments');
    }
    
    public function getAvailableSlots() {
        if (!$this->isAjax()) {
            $this->json(['error' => 'Invalid request'], 400);
            return;
        }
        
        $doctorId = $this->request->get('doctor_id');
        $date = $this->request->get('date');
        
        $slots = $this->appointmentService->getAvailableSlots($doctorId, $date);
        
        $this->json(['slots' => $slots]);
    }
}
