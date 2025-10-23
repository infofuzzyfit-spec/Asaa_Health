<?php
/**
 * User Controller
 * Handles user management
 */

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../services/AuthService.php';

class UserController extends Controller {
    private $authService;
    
    public function __construct() {
        parent::__construct();
        $this->authService = new AuthService();
    }
    
    public function index() {
        // Check if user is admin or staff
        $userRole = $this->session->get('user_role');
        if (!in_array($userRole, ['Admin', 'Staff'])) {
            $this->redirect('/dashboard');
            return;
        }
        
        $users = $this->authService->getAllUsers();
        
        $this->view('admin/users/index', [
            'users' => $users,
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }
    
    public function create() {
        if ($this->isPost()) {
            try {
                $this->validateCsrf();
                
                $data = [
                    'first_name' => $this->request->post('first_name'),
                    'last_name' => $this->request->post('last_name'),
                    'email' => $this->request->post('email'),
                    'mobile_number' => $this->request->post('mobile_number'),
                    'nic' => $this->request->post('nic'),
                    'gender' => $this->request->post('gender'),
                    'role' => $this->request->post('role'),
                    'position' => $this->request->post('position')
                ];
                
                $userId = $this->authService->createUser($data);
                
                if ($userId) {
                    $this->session->flash('success', 'User created successfully');
                    $this->redirect('/users');
                } else {
                    $this->session->flash('error', 'Failed to create user');
                    $this->redirect('/users/create');
                }
            } catch (Exception $e) {
                $this->session->flash('error', $e->getMessage());
                $this->redirect('/users/create');
            }
        } else {
            $this->view('admin/users/create', [
                'csrf_token' => $this->generateCsrfToken()
            ]);
        }
    }
    
    public function edit($id) {
        $user = $this->authService->getUserById($id);
        
        if (!$user) {
            $this->redirect('/users');
            return;
        }
        
        if ($this->isPost()) {
            try {
                $this->validateCsrf();
                
                $data = [
                    'first_name' => $this->request->post('first_name'),
                    'last_name' => $this->request->post('last_name'),
                    'email' => $this->request->post('email'),
                    'mobile_number' => $this->request->post('mobile_number'),
                    'nic' => $this->request->post('nic'),
                    'gender' => $this->request->post('gender'),
                    'role' => $this->request->post('role'),
                    'position' => $this->request->post('position'),
                    'status' => $this->request->post('status')
                ];
                
                $result = $this->authService->updateUser($id, $data);
                
                if ($result) {
                    $this->session->flash('success', 'User updated successfully');
                    $this->redirect('/users');
                } else {
                    $this->session->flash('error', 'Failed to update user');
                    $this->redirect('/users/edit/' . $id);
                }
            } catch (Exception $e) {
                $this->session->flash('error', $e->getMessage());
                $this->redirect('/users/edit/' . $id);
            }
        } else {
            $this->view('admin/users/edit', [
                'user' => $user,
                'csrf_token' => $this->generateCsrfToken()
            ]);
        }
    }
    
    public function delete($id) {
        try {
            $this->validateCsrf();
            
            $result = $this->authService->deleteUser($id);
            
            if ($result) {
                $this->session->flash('success', 'User deleted successfully');
            } else {
                $this->session->flash('error', 'Failed to delete user');
            }
        } catch (Exception $e) {
            $this->session->flash('error', $e->getMessage());
        }
        
        $this->redirect('/users');
    }
}
