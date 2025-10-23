<?php
/**
 * Medical Record Controller
 * Handles medical record management
 */

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../services/MedicalRecordService.php';

class MedicalRecordController extends Controller {
    private $medicalRecordService;
    
    public function __construct() {
        parent::__construct();
        $this->medicalRecordService = new MedicalRecordService();
    }
    
    public function index() {
        $userId = $this->session->get('user_id');
        $userRole = $this->session->get('user_role');
        
        $records = $this->medicalRecordService->getUserMedicalRecords($userId, $userRole);
        
        $this->view('medical-records/index', [
            'records' => $records,
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }
    
    public function create() {
        if ($this->isPost()) {
            try {
                $this->validateCsrf();
                
                $data = [
                    'appointment_id' => $this->request->post('appointment_id'),
                    'patient_id' => $this->request->post('patient_id'),
                    'doctor_id' => $this->session->get('user_id'),
                    'current_diagnosis' => $this->request->post('current_diagnosis'),
                    'prescription' => $this->request->post('prescription'),
                    'next_visit_date' => $this->request->post('next_visit_date'),
                    'notes' => $this->request->post('notes')
                ];
                
                $recordId = $this->medicalRecordService->createRecord($data);
                
                if ($recordId) {
                    $this->session->flash('success', 'Medical record created successfully');
                    $this->redirect('/medical-records');
                } else {
                    $this->session->flash('error', 'Failed to create medical record');
                    $this->redirect('/medical-records/create');
                }
            } catch (Exception $e) {
                $this->session->flash('error', $e->getMessage());
                $this->redirect('/medical-records/create');
            }
        } else {
            $this->view('medical-records/create', [
                'csrf_token' => $this->generateCsrfToken()
            ]);
        }
    }
    
    public function edit($id) {
        $record = $this->medicalRecordService->getRecordById($id);
        
        if (!$record) {
            $this->redirect('/medical-records');
            return;
        }
        
        if ($this->isPost()) {
            try {
                $this->validateCsrf();
                
                $data = [
                    'current_diagnosis' => $this->request->post('current_diagnosis'),
                    'prescription' => $this->request->post('prescription'),
                    'next_visit_date' => $this->request->post('next_visit_date'),
                    'notes' => $this->request->post('notes')
                ];
                
                $result = $this->medicalRecordService->updateRecord($id, $data);
                
                if ($result) {
                    $this->session->flash('success', 'Medical record updated successfully');
                    $this->redirect('/medical-records');
                } else {
                    $this->session->flash('error', 'Failed to update medical record');
                    $this->redirect('/medical-records/edit/' . $id);
                }
            } catch (Exception $e) {
                $this->session->flash('error', $e->getMessage());
                $this->redirect('/medical-records/edit/' . $id);
            }
        } else {
            $this->view('medical-records/edit', [
                'record' => $record,
                'csrf_token' => $this->generateCsrfToken()
            ]);
        }
    }
    
    public function view($id) {
        $record = $this->medicalRecordService->getRecordById($id);
        
        if (!$record) {
            $this->redirect('/medical-records');
            return;
        }
        
        $this->view('medical-records/view', [
            'record' => $record
        ]);
    }
}
