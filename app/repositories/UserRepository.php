<?php

namespace App\Repositories;

use App\Models\User;
use Core\Database;

class UserRepository
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Create a new user
     */
    public function create(array $data): ?int
    {
        $sql = "INSERT INTO users (first_name, last_name, email, password, nic, phone, gender, role, status, email_verified, email_verification_token, email_verification_expires, created_at) 
                VALUES (:first_name, :last_name, :email, :password, :nic, :phone, :gender, :role, :status, :email_verified, :email_verification_token, :email_verification_expires, :created_at)";

        $params = [
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'nic' => $data['nic'],
            'phone' => $data['phone'],
            'gender' => $data['gender'],
            'role' => $data['role'] ?? 'patient',
            'status' => $data['status'] ?? 'active',
            'email_verified' => $data['email_verified'] ?? 0,
            'email_verification_token' => $data['email_verification_token'] ?? null,
            'email_verification_expires' => $data['email_verification_expires'] ?? null,
            'created_at' => $data['created_at'] ?? date('Y-m-d H:i:s')
        ];

        return $this->db->insert($sql, $params);
    }

    /**
     * Find user by ID
     */
    public function find(int $id): ?array
    {
        $sql = "SELECT * FROM users WHERE id = :id";
        return $this->db->fetch($sql, ['id' => $id]);
    }

    /**
     * Find user by email
     */
    public function findByEmail(string $email): ?array
    {
        $sql = "SELECT * FROM users WHERE email = :email";
        return $this->db->fetch($sql, ['email' => $email]);
    }

    /**
     * Find user by NIC
     */
    public function findByNIC(string $nic): ?array
    {
        $sql = "SELECT * FROM users WHERE nic = :nic";
        return $this->db->fetch($sql, ['nic' => $nic]);
    }

    /**
     * Find user by verification token
     */
    public function findByVerificationToken(string $token): ?array
    {
        $sql = "SELECT * FROM users WHERE email_verification_token = :token";
        return $this->db->fetch($sql, ['token' => $token]);
    }

    /**
     * Find user by password reset token
     */
    public function findByPasswordResetToken(string $token): ?array
    {
        $sql = "SELECT * FROM users WHERE password_reset_token = :token";
        return $this->db->fetch($sql, ['token' => $token]);
    }

    /**
     * Find user by remember token
     */
    public function findByRememberToken(string $token): ?array
    {
        $sql = "SELECT * FROM users WHERE remember_token = :token";
        return $this->db->fetch($sql, ['token' => $token]);
    }

    /**
     * Update user
     */
    public function update(int $id, array $data): bool
    {
        $fields = [];
        $params = ['id' => $id];

        foreach ($data as $key => $value) {
            $fields[] = "{$key} = :{$key}";
            $params[$key] = $value;
        }

        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = :id";
        return $this->db->execute($sql, $params);
    }

    /**
     * Delete user
     */
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM users WHERE id = :id";
        return $this->db->execute($sql, ['id' => $id]);
    }

    /**
     * Check if email exists
     */
    public function emailExists(string $email, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) as count FROM users WHERE email = :email";
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
        $sql = "SELECT COUNT(*) as count FROM users WHERE nic = :nic";
        $params = ['nic' => $nic];

        if ($excludeId) {
            $sql .= " AND id != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }

        $result = $this->db->fetch($sql, $params);
        return $result['count'] > 0;
    }

    /**
     * Get all users with pagination
     */
    public function getAll(int $page = 1, int $limit = 10, array $filters = []): array
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
        $countSql = "SELECT COUNT(*) as total FROM users {$whereClause}";
        $totalResult = $this->db->fetch($countSql, $params);
        $total = $totalResult['total'];

        // Get users
        $sql = "SELECT id, first_name, last_name, email, role, status, email_verified, created_at, last_login 
                FROM users {$whereClause} 
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

    /**
     * Get users by role
     */
    public function getByRole(string $role): array
    {
        $sql = "SELECT id, first_name, last_name, email, phone, status, created_at 
                FROM users 
                WHERE role = :role 
                ORDER BY first_name, last_name";
        
        return $this->db->fetchAll($sql, ['role' => $role]);
    }

    /**
     * Get active users
     */
    public function getActiveUsers(): array
    {
        $sql = "SELECT id, first_name, last_name, email, role, last_login 
                FROM users 
                WHERE status = 'active' 
                ORDER BY last_login DESC";
        
        return $this->db->fetchAll($sql);
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
                FROM users";

        return $this->db->fetch($sql);
    }

    /**
     * Get recent users
     */
    public function getRecentUsers(int $limit = 5): array
    {
        $sql = "SELECT id, first_name, last_name, email, role, created_at 
                FROM users 
                ORDER BY created_at DESC 
                LIMIT :limit";
        
        return $this->db->fetchAll($sql, ['limit' => $limit]);
    }

    /**
     * Update last login
     */
    public function updateLastLogin(int $id): bool
    {
        return $this->update($id, ['last_login' => date('Y-m-d H:i:s')]);
    }

    /**
     * Get user profile with additional details
     */
    public function getProfile(int $id): ?array
    {
        $sql = "SELECT u.*, d.specialization_id, d.qualification, d.experience, d.consultation_fee, d.bio,
                       s.name as specialization_name
                FROM users u
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
                FROM users 
                WHERE (first_name LIKE :query OR last_name LIKE :query OR email LIKE :query OR phone LIKE :query)
                AND status = 'active'
                ORDER BY first_name, last_name
                LIMIT :limit";
        
        return $this->db->fetchAll($sql, [
            'query' => '%' . $query . '%',
            'limit' => $limit
        ]);
    }
}