<?php
/**
 * Login Form Component
 * User login form with validation
 */

require_once __DIR__ . '/../Component.php';

class LoginForm extends Component {
    public function render(): string {
        $action = $this->prop('action', '/login');
        $method = $this->prop('method', 'POST');
        $csrfToken = $this->prop('csrfToken', '');
        $errors = $this->prop('errors', []);
        $rememberMe = $this->prop('rememberMe', true);
        $forgotPassword = $this->prop('forgotPassword', true);
        $registerLink = $this->prop('registerLink', true);
        
        $classes = $this->buildClass('login-form');
        
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
        
        $rememberMeHtml = '';
        if ($rememberMe) {
            $rememberMeHtml = "
            <div class='form-check mb-3'>
                <input class='form-check-input' type='checkbox' name='remember' id='remember'>
                <label class='form-check-label' for='remember'>Remember me</label>
            </div>";
        }
        
        $forgotPasswordHtml = '';
        if ($forgotPassword) {
            $forgotPasswordHtml = "<div class='text-end mb-3'><a href='/forgot-password' class='text-decoration-none'>Forgot password?</a></div>";
        }
        
        $registerHtml = '';
        if ($registerLink) {
            $registerHtml = "
            <div class='text-center mt-3'>
                <p class='mb-0'>Don't have an account? <a href='/register' class='text-decoration-none'>Register here</a></p>
            </div>";
        }
        
        return "
        <form {$this->buildAttributes()} action='{$action}' method='{$method}'>
            {$errorHtml}
            <input type='hidden' name='csrf_token' value='{$this->escape($csrfToken)}'>
            
            <div class='mb-3'>
                <label for='email' class='form-label'>Email Address</label>
                <input type='email' class='form-control' id='email' name='email' required>
            </div>
            
            <div class='mb-3'>
                <label for='password' class='form-label'>Password</label>
                <input type='password' class='form-control' id='password' name='password' required>
            </div>
            
            {$rememberMeHtml}
            {$forgotPasswordHtml}
            
            <div class='d-grid'>
                <button type='submit' class='btn btn-primary btn-lg'>Login</button>
            </div>
            
            {$registerHtml}
        </form>";
    }
}
