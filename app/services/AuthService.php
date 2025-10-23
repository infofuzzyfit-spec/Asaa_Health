<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Models\User;
use Core\Database;
use Core\Session;
use Core\Validator;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class AuthService
{
    private $userRepository;
    private $db;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
        $this->db = Database::getInstance();
    }

    /**
     * Register a new user
     */
    public function register(array $data): array
    {
        $validator = new Validator();
        
        // Validate required fields
        $errors = [];
        
        if (!$validator->required($data['first_name'])) {
            $errors['first_name'] = 'First name is required';
        }
        
        if (!$validator->required($data['last_name'])) {
            $errors['last_name'] = 'Last name is required';
        }
        
        if (!$validator->required($data['email'])) {
            $errors['email'] = 'Email is required';
        } elseif (!$validator->email($data['email'])) {
            $errors['email'] = 'Please enter a valid email address';
        } elseif ($this->userRepository->emailExists($data['email'])) {
            $errors['email'] = 'Email address is already registered';
        }
        
        if (!$validator->required($data['password'])) {
            $errors['password'] = 'Password is required';
        } elseif (!$validator->minLength($data['password'], 8)) {
            $errors['password'] = 'Password must be at least 8 characters long';
        }
        
        if (!$validator->required($data['confirm_password'])) {
            $errors['confirm_password'] = 'Please confirm your password';
        } elseif ($data['password'] !== $data['confirm_password']) {
            $errors['confirm_password'] = 'Passwords do not match';
        }
        
        if (!$validator->required($data['nic'])) {
            $errors['nic'] = 'NIC is required';
        } elseif ($this->userRepository->nicExists($data['nic'])) {
            $errors['nic'] = 'NIC is already registered';
        }
        
        if (!$validator->required($data['phone'])) {
            $errors['phone'] = 'Phone number is required';
        }
        
        if (!$validator->required($data['gender'])) {
            $errors['gender'] = 'Gender is required';
        }
        
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        try {
            $this->db->beginTransaction();

            // Create user
            $userData = [
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'password' => password_hash($data['password'], PASSWORD_DEFAULT),
                'nic' => $data['nic'],
                'phone' => $data['phone'],
                'gender' => $data['gender'],
                'role' => 'patient',
                'status' => 'active',
                'email_verified' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ];

            $userId = $this->userRepository->create($userData);

            if (!$userId) {
                throw new \Exception('Failed to create user account');
            }

            // Generate email verification token
            $verificationToken = bin2hex(random_bytes(32));
            $this->userRepository->update($userId, [
                'email_verification_token' => $verificationToken,
                'email_verification_expires' => date('Y-m-d H:i:s', strtotime('+24 hours'))
            ]);

            // Send verification email
            $this->sendVerificationEmail($data['email'], $data['first_name'], $verificationToken);

            $this->db->commit();

            return [
                'success' => true,
                'message' => 'Registration successful! Please check your email to verify your account.',
                'user_id' => $userId
            ];

        } catch (\Exception $e) {
            $this->db->rollback();
            return [
                'success' => false,
                'message' => 'Registration failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Login user
     */
    public function login(string $email, string $password, bool $rememberMe = false): array
    {
        $validator = new Validator();
        
        if (!$validator->required($email)) {
            return ['success' => false, 'message' => 'Email is required'];
        }
        
        if (!$validator->required($password)) {
            return ['success' => false, 'message' => 'Password is required'];
        }

        $user = $this->userRepository->findByEmail($email);

        if (!$user) {
            return ['success' => false, 'message' => 'Invalid email or password'];
        }

        if ($user['status'] !== 'active') {
            return ['success' => false, 'message' => 'Your account is not active. Please contact support.'];
        }

        if (!password_verify($password, $user['password'])) {
            return ['success' => false, 'message' => 'Invalid email or password'];
        }

        // Check if email is verified
        if (!$user['email_verified']) {
            return [
                'success' => false,
                'message' => 'Please verify your email address before logging in.',
                'needs_verification' => true,
                'email' => $user['email']
            ];
        }

        try {
            // Update last login
            $this->userRepository->update($user['id'], [
                'last_login' => date('Y-m-d H:i:s')
            ]);

            // Set session
            Session::start();
            Session::set('user_id', $user['id']);
            Session::set('user_email', $user['email']);
            Session::set('user_role', $user['role']);
            Session::set('user_name', $user['first_name'] . ' ' . $user['last_name']);
            Session::set('is_logged_in', true);

            // Set remember me cookie if requested
            if ($rememberMe) {
                $rememberToken = bin2hex(random_bytes(32));
                $this->userRepository->update($user['id'], [
                    'remember_token' => $rememberToken,
                    'remember_token_expires' => date('Y-m-d H:i:s', strtotime('+30 days'))
                ]);
                
                setcookie('remember_token', $rememberToken, time() + (30 * 24 * 60 * 60), '/', '', false, true);
            }

            return [
                'success' => true,
                'message' => 'Login successful',
                'user' => [
                    'id' => $user['id'],
                    'email' => $user['email'],
                    'role' => $user['role'],
                    'name' => $user['first_name'] . ' ' . $user['last_name']
                ]
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Login failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Logout user
     */
    public function logout(): bool
    {
        try {
            Session::start();
            
            // Clear remember me token
            if (isset($_COOKIE['remember_token'])) {
                $token = $_COOKIE['remember_token'];
                $user = $this->userRepository->findByRememberToken($token);
                
                if ($user) {
                    $this->userRepository->update($user['id'], [
                        'remember_token' => null,
                        'remember_token_expires' => null
                    ]);
                }
                
                setcookie('remember_token', '', time() - 3600, '/', '', false, true);
            }
            
            // Destroy session
            Session::destroy();
            
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Check if user is logged in
     */
    public function isLoggedIn(): bool
    {
        Session::start();
        return Session::get('is_logged_in', false);
    }

    /**
     * Get current user
     */
    public function getCurrentUser(): ?array
    {
        if (!$this->isLoggedIn()) {
            return null;
        }

        $userId = Session::get('user_id');
        return $this->userRepository->find($userId);
    }

    /**
     * Check if user has specific role
     */
    public function hasRole(string $role): bool
    {
        if (!$this->isLoggedIn()) {
            return false;
        }

        $userRole = Session::get('user_role');
        return $userRole === $role;
    }

    /**
     * Check if user has any of the specified roles
     */
    public function hasAnyRole(array $roles): bool
    {
        if (!$this->isLoggedIn()) {
            return false;
        }

        $userRole = Session::get('user_role');
        return in_array($userRole, $roles);
    }

    /**
     * Verify email address
     */
    public function verifyEmail(string $token): array
    {
        $user = $this->userRepository->findByVerificationToken($token);

        if (!$user) {
            return ['success' => false, 'message' => 'Invalid verification token'];
        }

        if ($user['email_verification_expires'] < date('Y-m-d H:i:s')) {
            return ['success' => false, 'message' => 'Verification token has expired'];
        }

        if ($user['email_verified']) {
            return ['success' => false, 'message' => 'Email is already verified'];
        }

        try {
            $this->userRepository->update($user['id'], [
                'email_verified' => 1,
                'email_verification_token' => null,
                'email_verification_expires' => null
            ]);

            return ['success' => true, 'message' => 'Email verified successfully'];

        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Email verification failed: ' . $e->getMessage()];
        }
    }

    /**
     * Resend verification email
     */
    public function resendVerificationEmail(string $email): array
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user) {
            return ['success' => false, 'message' => 'Email address not found'];
        }

        if ($user['email_verified']) {
            return ['success' => false, 'message' => 'Email is already verified'];
        }

        try {
            // Generate new verification token
            $verificationToken = bin2hex(random_bytes(32));
            $this->userRepository->update($user['id'], [
                'email_verification_token' => $verificationToken,
                'email_verification_expires' => date('Y-m-d H:i:s', strtotime('+24 hours'))
            ]);

            // Send verification email
            $this->sendVerificationEmail($email, $user['first_name'], $verificationToken);

            return ['success' => true, 'message' => 'Verification email sent successfully'];

        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Failed to send verification email: ' . $e->getMessage()];
        }
    }

    /**
     * Request password reset
     */
    public function requestPasswordReset(string $email): array
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user) {
            return ['success' => false, 'message' => 'Email address not found'];
        }

        try {
            // Generate reset token
            $resetToken = bin2hex(random_bytes(32));
            $this->userRepository->update($user['id'], [
                'password_reset_token' => $resetToken,
                'password_reset_expires' => date('Y-m-d H:i:s', strtotime('+1 hour'))
            ]);

            // Send reset email
            $this->sendPasswordResetEmail($email, $user['first_name'], $resetToken);

            return ['success' => true, 'message' => 'Password reset instructions sent to your email'];

        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Failed to send password reset email: ' . $e->getMessage()];
        }
    }

    /**
     * Reset password
     */
    public function resetPassword(string $token, string $newPassword): array
    {
        $validator = new Validator();
        
        if (!$validator->required($newPassword)) {
            return ['success' => false, 'message' => 'Password is required'];
        }
        
        if (!$validator->minLength($newPassword, 8)) {
            return ['success' => false, 'message' => 'Password must be at least 8 characters long'];
        }

        $user = $this->userRepository->findByPasswordResetToken($token);

        if (!$user) {
            return ['success' => false, 'message' => 'Invalid reset token'];
        }

        if ($user['password_reset_expires'] < date('Y-m-d H:i:s')) {
            return ['success' => false, 'message' => 'Reset token has expired'];
        }

        try {
            $this->userRepository->update($user['id'], [
                'password' => password_hash($newPassword, PASSWORD_DEFAULT),
                'password_reset_token' => null,
                'password_reset_expires' => null
            ]);

            return ['success' => true, 'message' => 'Password reset successfully'];

        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Password reset failed: ' . $e->getMessage()];
        }
    }

    /**
     * Change password (for logged in users)
     */
    public function changePassword(int $userId, string $currentPassword, string $newPassword): array
    {
        $validator = new Validator();
        
        if (!$validator->required($currentPassword)) {
            return ['success' => false, 'message' => 'Current password is required'];
        }
        
        if (!$validator->required($newPassword)) {
            return ['success' => false, 'message' => 'New password is required'];
        }
        
        if (!$validator->minLength($newPassword, 8)) {
            return ['success' => false, 'message' => 'New password must be at least 8 characters long'];
        }

        $user = $this->userRepository->find($userId);

        if (!$user) {
            return ['success' => false, 'message' => 'User not found'];
        }

        if (!password_verify($currentPassword, $user['password'])) {
            return ['success' => false, 'message' => 'Current password is incorrect'];
        }

        try {
            $this->userRepository->update($userId, [
                'password' => password_hash($newPassword, PASSWORD_DEFAULT)
            ]);

            return ['success' => true, 'message' => 'Password changed successfully'];

        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Password change failed: ' . $e->getMessage()];
        }
    }

    /**
     * Check remember me token
     */
    public function checkRememberMe(): bool
    {
        if (!isset($_COOKIE['remember_token'])) {
            return false;
        }

        $token = $_COOKIE['remember_token'];
        $user = $this->userRepository->findByRememberToken($token);

        if (!$user || $user['remember_token_expires'] < date('Y-m-d H:i:s')) {
            return false;
        }

        // Auto login
        Session::start();
        Session::set('user_id', $user['id']);
        Session::set('user_email', $user['email']);
        Session::set('user_role', $user['role']);
        Session::set('user_name', $user['first_name'] . ' ' . $user['last_name']);
        Session::set('is_logged_in', true);

        return true;
    }

    /**
     * Send verification email
     */
    private function sendVerificationEmail(string $email, string $name, string $token): void
    {
        $mail = new PHPMailer(true);
        
        try {
            $mail->isSMTP();
            $mail->Host = SMTP_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = SMTP_USERNAME;
            $mail->Password = SMTP_PASSWORD;
            $mail->SMTPSecure = SMTP_ENCRYPTION;
            $mail->Port = SMTP_PORT;

            $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
            $mail->addAddress($email, $name);

            $mail->isHTML(true);
            $mail->Subject = 'Verify Your Email Address';
            
            $verificationUrl = APP_URL . '/verify-email?token=' . $token;
            
            $mail->Body = "
                <h2>Welcome to " . APP_NAME . "!</h2>
                <p>Hi " . htmlspecialchars($name) . ",</p>
                <p>Thank you for registering with us. Please click the link below to verify your email address:</p>
                <p><a href='" . $verificationUrl . "' style='background-color: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Verify Email Address</a></p>
                <p>Or copy and paste this link in your browser:</p>
                <p>" . $verificationUrl . "</p>
                <p>This link will expire in 24 hours.</p>
                <p>If you didn't create an account, please ignore this email.</p>
            ";

            $mail->send();
        } catch (Exception $e) {
            error_log('Email verification failed: ' . $mail->ErrorInfo);
            throw new \Exception('Failed to send verification email');
        }
    }

    /**
     * Send password reset email
     */
    private function sendPasswordResetEmail(string $email, string $name, string $token): void
    {
        $mail = new PHPMailer(true);
        
        try {
            $mail->isSMTP();
            $mail->Host = SMTP_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = SMTP_USERNAME;
            $mail->Password = SMTP_PASSWORD;
            $mail->SMTPSecure = SMTP_ENCRYPTION;
            $mail->Port = SMTP_PORT;

            $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
            $mail->addAddress($email, $name);

            $mail->isHTML(true);
            $mail->Subject = 'Reset Your Password';
            
            $resetUrl = APP_URL . '/reset-password?token=' . $token;
            
            $mail->Body = "
                <h2>Password Reset Request</h2>
                <p>Hi " . htmlspecialchars($name) . ",</p>
                <p>You requested to reset your password. Click the link below to reset it:</p>
                <p><a href='" . $resetUrl . "' style='background-color: #dc3545; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Reset Password</a></p>
                <p>Or copy and paste this link in your browser:</p>
                <p>" . $resetUrl . "</p>
                <p>This link will expire in 1 hour.</p>
                <p>If you didn't request this, please ignore this email.</p>
            ";

            $mail->send();
        } catch (Exception $e) {
            error_log('Password reset email failed: ' . $mail->ErrorInfo);
            throw new \Exception('Failed to send password reset email');
        }
    }
}