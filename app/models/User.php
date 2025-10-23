<?php

namespace App\Models;

use Core\Model;

class User extends Model
{
    protected $table = 'users';

    /**
     * Get user by email
     */
    public function findByEmail(string $email): ?array
    {
        return $this->findBy('email', $email);
    }

    /**
     * Get user by NIC
     */
    public function findByNIC(string $nic): ?array
    {
        return $this->findBy('nic', $nic);
    }

    /**
     * Get user by verification token
     */
    public function findByVerificationToken(string $token): ?array
    {
        return $this->findBy('email_verification_token', $token);
    }

    /**
     * Get user by password reset token
     */
    public function findByPasswordResetToken(string $token): ?array
    {
        return $this->findBy('password_reset_token', $token);
    }

    /**
     * Get user by remember token
     */
    public function findByRememberToken(string $token): ?array
    {
        return $this->findBy('remember_token', $token);
    }

    /**
     * Check if email exists
     */
    public function emailExists(string $email, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE email = :email";
        $params = ['email' => $email];

        if ($excludeId) {
            $sql .= " AND id != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }

        $result = $this->db->fetch($sql, $params);
        return $result['count'] > 0;
    }

    /**
     * Check if NIC exists
     */
    public function nicExists(string $nic, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE nic = :nic";
        $params = ['nic' => $nic];

        if ($excludeId) {
            $sql .= " AND id != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }

        $result = $this->db->fetch($sql, $params);
        return $result['count'] > 0;
    }

    /**
     * Get users by role
     */
    public function getByRole(string $role): array
    {
        return $this->findAllBy('role', $role);
    }

    /**
     * Get active users
     */
    public function getActiveUsers(): array
    {
        return $this->findAllBy('status', 'active');
    }

    /**
     * Get user profile with doctor details
     */
    public function getProfile(int $id): ?array
    {
        $sql = "SELECT u.*, d.specialization_id, d.qualification, d.experience, d.consultation_fee, d.bio,
                       s.name as specialization_name
                FROM {$this->table} u
                LEFT JOIN doctor_details d ON u.id = d.user_id
                LEFT JOIN specializations s ON d.specialization_id = s.id
                WHERE u.id = :id";
        
        return $this->db->fetch($sql, ['id' => $id]);
    }

    /**
     * Search users
     */
    public function search(string $query, int $limit = 10): array
    {
        $sql = "SELECT id, first_name, last_name, email, role, phone 
                FROM {$this->table} 
                WHERE (first_name LIKE :query OR last_name LIKE :query OR email LIKE :query OR phone LIKE :query)
                AND status = 'active'
                ORDER BY first_name, last_name
                LIMIT :limit";
        
        return $this->db->fetchAll($sql, [
            'query' => '%' . $query . '%',
            'limit' => $limit
        ]);
    }

    /**
     * Get user statistics
     */
    public function getStatistics(): array
    {
        $sql = "SELECT 
                    COUNT(*) as total_users,
                    SUM(CASE WHEN role = 'patient' THEN 1 ELSE 0 END) as patients,
                    SUM(CASE WHEN role = 'doctor' THEN 1 ELSE 0 END) as doctors,
                    SUM(CASE WHEN role = 'staff' THEN 1 ELSE 0 END) as staff,
                    SUM(CASE WHEN role = 'admin' THEN 1 ELSE 0 END) as admins,
                    SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_users,
                    SUM(CASE WHEN email_verified = 1 THEN 1 ELSE 0 END) as verified_users
                FROM {$this->table}";

        return $this->db->fetch($sql);
    }

    /**
     * Update last login
     */
    public function updateLastLogin(int $id): bool
    {
        return $this->update($id, ['last_login' => date('Y-m-d H:i:s')]);
    }

    /**
     * Get recent users
     */
    public function getRecentUsers(int $limit = 5): array
    {
        $sql = "SELECT id, first_name, last_name, email, role, created_at 
                FROM {$this->table} 
                ORDER BY created_at DESC 
                LIMIT :limit";
        
        return $this->db->fetchAll($sql, ['limit' => $limit]);
    }

    /**
     * Get users with pagination and filters
     */
    public function getPaginated(int $page = 1, int $limit = 10, array $filters = []): array
    {
        $offset = ($page - 1) * $limit;
        $where = [];
        $params = [];

        // Apply filters
        if (!empty($filters['role'])) {
            $where[] = "role = :role";
            $params['role'] = $filters['role'];
        }

        if (!empty($filters['status'])) {
            $where[] = "status = :status";
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['search'])) {
            $where[] = "(first_name LIKE :search OR last_name LIKE :search OR email LIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        // Get total count
        $countSql = "SELECT COUNT(*) as total FROM {$this->table} {$whereClause}";
        $totalResult = $this->db->fetch($countSql, $params);
        $total = $totalResult['total'];

        // Get users
        $sql = "SELECT id, first_name, last_name, email, role, status, email_verified, created_at, last_login 
                FROM {$this->table} {$whereClause} 
                ORDER BY created_at DESC 
                LIMIT :limit OFFSET :offset";

        $params['limit'] = $limit;
        $params['offset'] = $offset;

        $users = $this->db->fetchAll($sql, $params);

        return [
            'users' => $users,
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'pages' => ceil($total / $limit)
        ];
    }
}