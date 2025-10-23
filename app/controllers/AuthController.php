<?php

namespace App\Controllers;

use App\Services\AuthService;
use App\Repositories\UserRepository;
use Core\Controller;
use Core\Request;
use Core\Response;
use App\Middleware\AuthMiddleware;
use App\Middleware\CSRFMiddleware;

class AuthController extends Controller
{
    private $authService;
    private $userRepository;
    private $authMiddleware;
    private $csrfMiddleware;

    public function __construct()
    {
        $this->authService = new AuthService();
        $this->userRepository = new UserRepository();
        $this->authMiddleware = new AuthMiddleware();
        $this->csrfMiddleware = new CSRFMiddleware();
    }

    /**
     * Show login form
     */
    public function showLogin(Request $request, Response $response)
    {
        // Redirect if already logged in
        if ($this->authService->isLoggedIn()) {
            $user = $this->authService->getCurrentUser();
            $redirectUrl = $this->getRedirectUrlForRole($user['role']);
            return $response->redirect($redirectUrl);
        }

        $data = [
            'title' => 'Login - ' . APP_NAME,
            'csrf_token' => $this->csrfMiddleware->getToken()
        ];

        return $this->view('auth/login', $data);
    }

    /**
     * Handle login
     */
    public function login(Request $request, Response $response)
    {
        if ($request->getMethod() !== 'POST') {
            return $response->redirect('/login');
        }

        $email = $request->get('email');
        $password = $request->get('password');
        $rememberMe = $request->get('remember_me') === 'on';

        $result = $this->authService->login($email, $password, $rememberMe);

        if ($result['success']) {
            if ($request->isAjax()) {
                return $response->json($result);
            }

            $redirectUrl = $this->getRedirectUrlForRole($result['user']['role']);
            return $response->redirect($redirectUrl);
        } else {
            if ($request->isAjax()) {
                return $response->json($result, 400);
            }

            $data = [
                'title' => 'Login - ' . APP_NAME,
                'error' => $result['message'],
                'email' => $email,
                'csrf_token' => $this->csrfMiddleware->getToken()
            ];

            return $this->view('auth/login', $data);
        }
    }

    /**
     * Show registration form
     */
    public function showRegister(Request $request, Response $response)
    {
        // Redirect if already logged in
        if ($this->authService->isLoggedIn()) {
            $user = $this->authService->getCurrentUser();
            $redirectUrl = $this->getRedirectUrlForRole($user['role']);
            return $response->redirect($redirectUrl);
        }

        $data = [
            'title' => 'Register - ' . APP_NAME,
            'csrf_token' => $this->csrfMiddleware->getToken()
        ];

        return $this->view('auth/register', $data);
    }

    /**
     * Handle registration
     */
    public function register(Request $request, Response $response)
    {
        if ($request->getMethod() !== 'POST') {
            return $response->redirect('/register');
        }

        $data = $request->all();
        $result = $this->authService->register($data);

        if ($result['success']) {
            if ($request->isAjax()) {
                return $response->json($result);
            }

            $data = [
                'title' => 'Registration Successful - ' . APP_NAME,
                'message' => $result['message'],
                'email' => $data['email']
            ];

            return $this->view('auth/register-success', $data);
        } else {
            if ($request->isAjax()) {
                return $response->json($result, 400);
            }

            $data = [
                'title' => 'Register - ' . APP_NAME,
                'errors' => $result['errors'] ?? [],
                'old' => $request->all(),
                'csrf_token' => $this->csrfMiddleware->getToken()
            ];

            return $this->view('auth/register', $data);
        }
    }

    /**
     * Logout
     */
    public function logout(Request $request, Response $response)
    {
        $this->authService->logout();

        if ($request->isAjax()) {
            return $response->json([
                'success' => true,
                'message' => 'Logged out successfully',
                'redirect' => '/login'
            ]);
        }

        return $response->redirect('/login');
    }

    /**
     * Verify email
     */
    public function verifyEmail(Request $request, Response $response)
    {
        $token = $request->get('token');

        if (!$token) {
            return $this->view('auth/verify-email', [
                'title' => 'Email Verification - ' . APP_NAME,
                'success' => false,
                'message' => 'Invalid verification link'
            ]);
        }

        $result = $this->authService->verifyEmail($token);

        return $this->view('auth/verify-email', [
            'title' => 'Email Verification - ' . APP_NAME,
            'success' => $result['success'],
            'message' => $result['message']
        ]);
    }

    /**
     * Resend verification email
     */
    public function resendVerification(Request $request, Response $response)
    {
        if ($request->getMethod() !== 'POST') {
            return $response->redirect('/login');
        }

        $email = $request->get('email');
        $result = $this->authService->resendVerificationEmail($email);

        if ($request->isAjax()) {
            return $response->json($result);
        }

        return $this->view('auth/verify-email', [
            'title' => 'Email Verification - ' . APP_NAME,
            'success' => $result['success'],
            'message' => $result['message'],
            'email' => $email
        ]);
    }

    /**
     * Show forgot password form
     */
    public function showForgotPassword(Request $request, Response $response)
    {
        $data = [
            'title' => 'Forgot Password - ' . APP_NAME,
            'csrf_token' => $this->csrfMiddleware->getToken()
        ];

        return $this->view('auth/forgot-password', $data);
    }

    /**
     * Handle forgot password
     */
    public function forgotPassword(Request $request, Response $response)
    {
        if ($request->getMethod() !== 'POST') {
            return $response->redirect('/forgot-password');
        }

        $email = $request->get('email');
        $result = $this->authService->requestPasswordReset($email);

        if ($request->isAjax()) {
            return $response->json($result);
        }

        $data = [
            'title' => 'Forgot Password - ' . APP_NAME,
            'success' => $result['success'],
            'message' => $result['message'],
            'csrf_token' => $this->csrfMiddleware->getToken()
        ];

        return $this->view('auth/forgot-password', $data);
    }

    /**
     * Show reset password form
     */
    public function showResetPassword(Request $request, Response $response)
    {
        $token = $request->get('token');

        if (!$token) {
            return $response->redirect('/forgot-password');
        }

        $data = [
            'title' => 'Reset Password - ' . APP_NAME,
            'token' => $token,
            'csrf_token' => $this->csrfMiddleware->getToken()
        ];

        return $this->view('auth/reset-password', $data);
    }

    /**
     * Handle reset password
     */
    public function resetPassword(Request $request, Response $response)
    {
        if ($request->getMethod() !== 'POST') {
            return $response->redirect('/forgot-password');
        }

        $token = $request->get('token');
        $password = $request->get('password');
        $confirmPassword = $request->get('confirm_password');

        if ($password !== $confirmPassword) {
            $data = [
                'title' => 'Reset Password - ' . APP_NAME,
                'token' => $token,
                'error' => 'Passwords do not match',
                'csrf_token' => $this->csrfMiddleware->getToken()
            ];

            return $this->view('auth/reset-password', $data);
        }

        $result = $this->authService->resetPassword($token, $password);

        if ($request->isAjax()) {
            return $response->json($result);
        }

        $data = [
            'title' => 'Reset Password - ' . APP_NAME,
            'success' => $result['success'],
            'message' => $result['message'],
            'csrf_token' => $this->csrfMiddleware->getToken()
        ];

        return $this->view('auth/reset-password', $data);
    }

    /**
     * Show change password form
     */
    public function showChangePassword(Request $request, Response $response)
    {
        if (!$this->authService->isLoggedIn()) {
            return $response->redirect('/login');
        }

        $data = [
            'title' => 'Change Password - ' . APP_NAME,
            'csrf_token' => $this->csrfMiddleware->getToken()
        ];

        return $this->view('auth/change-password', $data);
    }

    /**
     * Handle change password
     */
    public function changePassword(Request $request, Response $response)
    {
        if (!$this->authService->isLoggedIn()) {
            return $response->redirect('/login');
        }

        if ($request->getMethod() !== 'POST') {
            return $response->redirect('/change-password');
        }

        $user = $this->authService->getCurrentUser();
        $currentPassword = $request->get('current_password');
        $newPassword = $request->get('new_password');

        $result = $this->authService->changePassword($user['id'], $currentPassword, $newPassword);

        if ($request->isAjax()) {
            return $response->json($result);
        }

        $data = [
            'title' => 'Change Password - ' . APP_NAME,
            'success' => $result['success'],
            'message' => $result['message'],
            'csrf_token' => $this->csrfMiddleware->getToken()
        ];

        return $this->view('auth/change-password', $data);
    }

    /**
     * Show profile
     */
    public function showProfile(Request $request, Response $response)
    {
        if (!$this->authService->isLoggedIn()) {
            return $response->redirect('/login');
        }

        $user = $this->authService->getCurrentUser();
        $profile = $this->userRepository->getProfile($user['id']);

        $data = [
            'title' => 'My Profile - ' . APP_NAME,
            'user' => $profile,
            'csrf_token' => $this->csrfMiddleware->getToken()
        ];

        return $this->view('auth/profile', $data);
    }

    /**
     * Update profile
     */
    public function updateProfile(Request $request, Response $response)
    {
        if (!$this->authService->isLoggedIn()) {
            return $response->redirect('/login');
        }

        if ($request->getMethod() !== 'POST') {
            return $response->redirect('/profile');
        }

        $user = $this->authService->getCurrentUser();
        $data = $request->all();

        // Remove sensitive fields
        unset($data['password'], $data['role'], $data['status']);

        try {
            $this->userRepository->update($user['id'], $data);

            if ($request->isAjax()) {
                return $response->json([
                    'success' => true,
                    'message' => 'Profile updated successfully'
                ]);
            }

            return $response->redirect('/profile?success=1');

        } catch (\Exception $e) {
            if ($request->isAjax()) {
                return $response->json([
                    'success' => false,
                    'message' => 'Profile update failed: ' . $e->getMessage()
                ], 400);
            }

            return $response->redirect('/profile?error=1');
        }
    }

    /**
     * Get redirect URL based on user role
     */
    private function getRedirectUrlForRole(string $role): string
    {
        switch ($role) {
            case 'admin':
                return '/admin/dashboard';
            case 'doctor':
                return '/doctor/dashboard';
            case 'staff':
                return '/staff/dashboard';
            case 'patient':
                return '/patient/dashboard';
            default:
                return '/dashboard';
        }
    }
}