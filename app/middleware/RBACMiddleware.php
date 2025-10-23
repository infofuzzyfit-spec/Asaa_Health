<?php

namespace App\Middleware;

use App\Services\AuthService;
use Core\Request;
use Core\Response;
use Core\Database;

class RBACMiddleware
{
    private $authService;
    private $db;

    public function __construct()
    {
        $this->authService = new AuthService();
        $this->db = Database::getInstance();
    }

    /**
     * Check if user has specific permission
     */
    public function permission(string $permission)
    {
        return function (Request $request, Response $response, callable $next) use ($permission) {
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

            if (!$this->hasPermission($permission)) {
                if ($request->isAjax()) {
                    return $response->json([
                        'success' => false,
                        'message' => 'Access denied. You do not have permission to perform this action.',
                        'redirect' => '/unauthorized'
                    ], 403);
                }

                return $response->redirect('/unauthorized');
            }

            return $next($request, $response);
        };
    }

    /**
     * Check if user has any of the specified permissions
     */
    public function anyPermission(array $permissions)
    {
        return function (Request $request, Response $response, callable $next) use ($permissions) {
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

            if (!$this->hasAnyPermission($permissions)) {
                if ($request->isAjax()) {
                    return $response->json([
                        'success' => false,
                        'message' => 'Access denied. You do not have permission to perform this action.',
                        'redirect' => '/unauthorized'
                    ], 403);
                }

                return $response->redirect('/unauthorized');
            }

            return $next($request, $response);
        };
    }

    /**
     * Check if user has all of the specified permissions
     */
    public function allPermissions(array $permissions)
    {
        return function (Request $request, Response $response, callable $next) use ($permissions) {
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

            if (!$this->hasAllPermissions($permissions)) {
                if ($request->isAjax()) {
                    return $response->json([
                        'success' => false,
                        'message' => 'Access denied. You do not have permission to perform this action.',
                        'redirect' => '/unauthorized'
                    ], 403);
                }

                return $response->redirect('/unauthorized');
            }

            return $next($request, $response);
        };
    }

    /**
     * Check if user can access specific resource
     */
    public function resource(string $resource, string $action = 'view')
    {
        return function (Request $request, Response $response, callable $next) use ($resource, $action) {
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

            $permission = $resource . '.' . $action;
            
            if (!$this->hasPermission($permission)) {
                if ($request->isAjax()) {
                    return $response->json([
                        'success' => false,
                        'message' => 'Access denied. You do not have permission to ' . $action . ' ' . $resource,
                        'redirect' => '/unauthorized'
                    ], 403);
                }

                return $response->redirect('/unauthorized');
            }

            return $next($request, $response);
        };
    }

    /**
     * Check if user can manage appointments
     */
    public function canManageAppointments(Request $request, Response $response, callable $next)
    {
        return $this->anyPermission(['appointments.create', 'appointments.update', 'appointments.delete'])($request, $response, $next);
    }

    /**
     * Check if user can manage medical records
     */
    public function canManageMedicalRecords(Request $request, Response $response, callable $next)
    {
        return $this->anyPermission(['medical_records.create', 'medical_records.update', 'medical_records.delete'])($request, $response, $next);
    }

    /**
     * Check if user can manage payments
     */
    public function canManagePayments(Request $request, Response $response, callable $next)
    {
        return $this->anyPermission(['payments.create', 'payments.update', 'payments.delete'])($request, $response, $next);
    }

    /**
     * Check if user can view reports
     */
    public function canViewReports(Request $request, Response $response, callable $next)
    {
        return $this->anyPermission(['reports.view', 'reports.export'])($request, $response, $next);
    }

    /**
     * Check if user can manage users
     */
    public function canManageUsers(Request $request, Response $response, callable $next)
    {
        return $this->anyPermission(['users.create', 'users.update', 'users.delete'])($request, $response, $next);
    }

    /**
     * Check if user has specific permission
     */
    private function hasPermission(string $permission): bool
    {
        $user = $this->authService->getCurrentUser();
        if (!$user) {
            return false;
        }

        // Admin has all permissions
        if ($user['role'] === 'admin') {
            return true;
        }

        // Get user permissions
        $permissions = $this->getUserPermissions($user['id']);
        
        return in_array($permission, $permissions);
    }

    /**
     * Check if user has any of the specified permissions
     */
    private function hasAnyPermission(array $permissions): bool
    {
        $user = $this->authService->getCurrentUser();
        if (!$user) {
            return false;
        }

        // Admin has all permissions
        if ($user['role'] === 'admin') {
            return true;
        }

        // Get user permissions
        $userPermissions = $this->getUserPermissions($user['id']);
        
        return !empty(array_intersect($permissions, $userPermissions));
    }

    /**
     * Check if user has all of the specified permissions
     */
    private function hasAllPermissions(array $permissions): bool
    {
        $user = $this->authService->getCurrentUser();
        if (!$user) {
            return false;
        }

        // Admin has all permissions
        if ($user['role'] === 'admin') {
            return true;
        }

        // Get user permissions
        $userPermissions = $this->getUserPermissions($user['id']);
        
        return empty(array_diff($permissions, $userPermissions));
    }

    /**
     * Get user permissions
     */
    private function getUserPermissions(int $userId): array
    {
        $sql = "SELECT p.name 
                FROM permissions p
                INNER JOIN role_permissions rp ON p.id = rp.permission_id
                INNER JOIN users u ON u.role = rp.role
                WHERE u.id = :user_id";

        $permissions = $this->db->fetchAll($sql, ['user_id' => $userId]);
        
        return array_column($permissions, 'name');
    }

    /**
     * Get role permissions
     */
    public function getRolePermissions(string $role): array
    {
        $sql = "SELECT p.name, p.description 
                FROM permissions p
                INNER JOIN role_permissions rp ON p.id = rp.permission_id
                WHERE rp.role = :role";

        return $this->db->fetchAll($sql, ['role' => $role]);
    }

    /**
     * Get all permissions
     */
    public function getAllPermissions(): array
    {
        $sql = "SELECT * FROM permissions ORDER BY name";
        return $this->db->fetchAll($sql);
    }

    /**
     * Assign permission to role
     */
    public function assignPermissionToRole(string $role, int $permissionId): bool
    {
        try {
            $sql = "INSERT INTO role_permissions (role, permission_id) VALUES (:role, :permission_id)";
            return $this->db->execute($sql, ['role' => $role, 'permission_id' => $permissionId]);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Remove permission from role
     */
    public function removePermissionFromRole(string $role, int $permissionId): bool
    {
        $sql = "DELETE FROM role_permissions WHERE role = :role AND permission_id = :permission_id";
        return $this->db->execute($sql, ['role' => $role, 'permission_id' => $permissionId]);
    }

    /**
     * Check if user can access their own resource
     */
    public function ownResource(string $resourceIdParam = 'id')
    {
        return function (Request $request, Response $response, callable $next) use ($resourceIdParam) {
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

            $user = $this->authService->getCurrentUser();
            $resourceId = $request->get($resourceIdParam);

            // Allow if user is admin or accessing their own resource
            if ($user['role'] === 'admin' || $user['id'] == $resourceId) {
                return $next($request, $response);
            }

            if ($request->isAjax()) {
                return $response->json([
                    'success' => false,
                    'message' => 'Access denied. You can only access your own resources.',
                    'redirect' => '/unauthorized'
                ], 403);
            }

            return $response->redirect('/unauthorized');
        };
    }
}