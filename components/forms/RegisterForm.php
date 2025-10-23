<?php
/**
 * Register Form Component
 * User registration form with validation
 */

require_once __DIR__ . '/../Component.php';

class RegisterForm extends Component {
    public function render(): string {
        $action = $this->prop('action', '/register');
        $method = $this->prop('method', 'POST');
        $csrfToken = $this->prop('csrfToken', '');
        $errors = $this->prop('errors', []);
        $userTypes = $this->prop('userTypes', ['Patient']);
        $selectedType = $this->prop('selectedType', 'Patient');
        
        $classes = $this->buildClass('register-form');
        
        $attributes = $this->attributes;
        $attributes['class'] = $classes;
        $this->attributes = $attributes;
        
        $errorHtml = '';
        if (!empty($errors)) {
            $errorHtml = '<div class="alert alert-danger">';
            foreach ($errors as $error) {
                $errorHtml .= "<p class='mb-0'>{$this->escape($error)}</p>";
            }
            $errorHtml .= '</div>';
        }
        
        $userTypeOptions = '';
        foreach ($userTypes as $type) {
            $selected = $type === $selectedType ? 'selected' : '';
            $userTypeOptions .= "<option value='{$type}' {$selected}>{$type}</option>";
        }
        
        return "
        <form {$this->buildAttributes()} action='{$action}' method='{$method}'>
            {$errorHtml}
            <input type='hidden' name='csrf_token' value='{$this->escape($csrfToken)}'>
            
            <div class='row'>
                <div class='col-md-6'>
                    <div class='mb-3'>
                        <label for='first_name' class='form-label'>First Name</label>
                        <input type='text' class='form-control' id='first_name' name='first_name' required>
                    </div>
                </div>
                <div class='col-md-6'>
                    <div class='mb-3'>
                        <label for='last_name' class='form-label'>Last Name</label>
                        <input type='text' class='form-control' id='last_name' name='last_name' required>
                    </div>
                </div>
            </div>
            
            <div class='mb-3'>
                <label for='email' class='form-label'>Email Address</label>
                <input type='email' class='form-control' id='email' name='email' required>
            </div>
            
            <div class='mb-3'>
                <label for='mobile_number' class='form-label'>Mobile Number</label>
                <input type='tel' class='form-control' id='mobile_number' name='mobile_number' required>
            </div>
            
            <div class='mb-3'>
                <label for='nic' class='form-label'>NIC Number</label>
                <input type='text' class='form-control' id='nic' name='nic' required>
            </div>
            
            <div class='mb-3'>
                <label for='gender' class='form-label'>Gender</label>
                <select class='form-select' id='gender' name='gender' required>
                    <option value=''>Select Gender</option>
                    <option value='Male'>Male</option>
                    <option value='Female'>Female</option>
                    <option value='Other'>Other</option>
                </select>
            </div>
            
            <div class='mb-3'>
                <label for='user_type' class='form-label'>User Type</label>
                <select class='form-select' id='user_type' name='user_type' required>
                    {$userTypeOptions}
                </select>
            </div>
            
            <div class='mb-3'>
                <label for='password' class='form-label'>Password</label>
                <input type='password' class='form-control' id='password' name='password' required>
            </div>
            
            <div class='mb-3'>
                <label for='password_confirmation' class='form-label'>Confirm Password</label>
                <input type='password' class='form-control' id='password_confirmation' name='password_confirmation' required>
            </div>
            
            <div class='form-check mb-3'>
                <input class='form-check-input' type='checkbox' id='terms' name='terms' required>
                <label class='form-check-label' for='terms'>
                    I agree to the <a href='/terms' target='_blank'>Terms and Conditions</a>
                </label>
            </div>
            
            <div class='d-grid'>
                <button type='submit' class='btn btn-primary btn-lg'>Register</button>
            </div>
            
            <div class='text-center mt-3'>
                <p class='mb-0'>Already have an account? <a href='/login' class='text-decoration-none'>Login here</a></p>
            </div>
        </form>";
    }
}
