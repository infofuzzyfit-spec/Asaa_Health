<?php

namespace App\Middleware;

use App\Services\AuthService;
use Core\Request;
use Core\Response;

class AuthMiddleware
{
    private $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    /**
     * Check if user is authenticated
     */
    public function handle(Request $request, Response $response, callable $next)
    {
        // Check remember me token first
        if (!$this->authService->isLoggedIn()) {
            $this->authService->checkRememberMe();
        }

        if (!$this->authService->isLoggedIn()) {
            if ($request->isAjax()) {
                return $response->json([
                    'success' => false,
                    'message' => 'Authentication required',
                    'redirect' => '/login'
                ], 401);
            }

            return $response->redirect('/login');
        }

        return $next($request, $response);
    }

    /**
     * Check if user is guest (not authenticated)
     */
    public function guest(Request $request, Response $response, callable $next)
    {
        if ($this->authService->isLoggedIn()) {
            $user = $this->authService->getCurrentUser();
            $redirectUrl = $this->getRedirectUrlForRole($user['role']);
            
            if ($request->isAjax()) {
                return $response->json([
                    'success' => false,
                    'message' => 'Already logged in',
                    'redirect' => $redirectUrl
                ], 400);
            }

            return $response->redirect($redirectUrl);
        }

        return $next($request, $response);
    }

    /**
     * Check if user has specific role
     */
    public function role(string $role)
    {
        return function (Request $request, Response $response, callable $next) use ($role) {
            if (!$this->authService->isLoggedIn()) {
                if ($request->isAjax()) {
                    return $response->json([
                        'success' => false,
                        'message' => 'Authentication required',
                        'redirect' => '/login'
                    ], 401);
                }

                return $response->redirect('/login');
            }

            if (!$this->authService->hasRole($role)) {
                if ($request->isAjax()) {
                    return $response->json([
                        'success' => false,
                        'message' => 'Access denied. Insufficient permissions.',
                        'redirect' => '/unauthorized'
                    ], 403);
                }

                return $response->redirect('/unauthorized');
            }

            return $next($request, $response);
        };
    }

    /**
     * Check if user has any of the specified roles
     */
    public function anyRole(array $roles)
    {
        return function (Request $request, Response $response, callable $next) use ($roles) {
            if (!$this->authService->isLoggedIn()) {
                if ($request->isAjax()) {
                    return $response->json([
                        'success' => false,
                        'message' => 'Authentication required',
                        'redirect' => '/login'
                    ], 401);
                }

                return $response->redirect('/login');
            }

            if (!$this->authService->hasAnyRole($roles)) {
                if ($request->isAjax()) {
                    return $response->json([
                        'success' => false,
                        'message' => 'Access denied. Insufficient permissions.',
                        'redirect' => '/unauthorized'
                    ], 403);
                }

                return $response->redirect('/unauthorized');
            }

            return $next($request, $response);
        };
    }

    /**
     * Check if user is admin
     */
    public function admin(Request $request, Response $response, callable $next)
    {
        return $this->role('admin')($request, $response, $next);
    }

    /**
     * Check if user is doctor
     */
    public function doctor(Request $request, Response $response, callable $next)
    {
        return $this->role('doctor')($request, $response, $next);
    }

    /**
     * Check if user is staff
     */
    public function staff(Request $request, Response $response, callable $next)
    {
        return $this->role('staff')($request, $response, $next);
    }

    /**
     * Check if user is patient
     */
    public function patient(Request $request, Response $response, callable $next)
    {
        return $this->role('patient')($request, $response, $next);
    }

    /**
     * Check if user is admin or staff
     */
    public function adminOrStaff(Request $request, Response $response, callable $next)
    {
        return $this->anyRole(['admin', 'staff'])($request, $response, $next);
    }

    /**
     * Check if user is doctor or staff
     */
    public function doctorOrStaff(Request $request, Response $response, callable $next)
    {
        return $this->anyRole(['doctor', 'staff'])($request, $response, $next);
    }

    /**
     * Check if user is admin, doctor, or staff
     */
    public function adminDoctorOrStaff(Request $request, Response $response, callable $next)
    {
        return $this->anyRole(['admin', 'doctor', 'staff'])($request, $response, $next);
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