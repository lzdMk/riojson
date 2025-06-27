<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'accounts';
    protected $primaryKey = 'user_id';
    protected $useAutoIncrement = false; // Changed to false since we're using custom IDs
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'user_id', // Updated to use new column name
        'email',
        'password_hash',
        'user_type',
        'max_files',
        'max_storage_mb',
        'last_login_at',
        'is_active',
        'created_at'
    ];
    
    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    
    protected $validationRules = [
        'user_id' => 'required|min_length[6]|max_length[6]|is_unique[accounts.user_id]',
        'email' => 'required|valid_email|is_unique[accounts.email]',
        'password_hash' => 'required',
        'user_type' => 'in_list[free,paid,admin]'
    ];
    
    protected $validationMessages = [
        'user_id' => [
            'required' => 'User ID is required',
            'min_length' => 'User ID must be exactly 6 characters',
            'max_length' => 'User ID must be exactly 6 characters',
            'is_unique' => 'This user ID already exists'
        ],
        'email' => [
            'required' => 'Email is required',
            'valid_email' => 'Please enter a valid email address',
            'is_unique' => 'This email is already registered'
        ]
    ];
    
    /**
     * Generate a unique 6-digit alphanumeric ID (case sensitive)
     */
    public function generateUniqueId()
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $maxAttempts = 100; // Prevent infinite loop
        $attempts = 0;
        
        do {
            $id = '';
            for ($i = 0; $i < 6; $i++) {
                $id .= $characters[random_int(0, strlen($characters) - 1)];
            }
            $attempts++;
        } while ($this->find($id) !== null && $attempts < $maxAttempts);
        
        if ($attempts >= $maxAttempts) {
            throw new \Exception('Unable to generate unique ID after maximum attempts');
        }
        
        return $id;
    }
    
    /**
     * Create a new user account
     */
    public function createUser($email, $password, $userType = 'free')
    {
        $limits = $this->getUserLimits($userType);
        
        $data = [
            'user_id' => $this->generateUniqueId(),
            'email' => $email,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'user_type' => $userType,
            'max_files' => $limits['max_files'],
            'max_storage_mb' => $limits['max_storage_mb'],
            'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        if ($this->insert($data)) {
            return $data['user_id']; // Return the generated ID instead of boolean
        }
        
        return false;
    }
    
    /**
     * Find user by email
     */
    public function findByEmail($email)
    {
        return $this->where('email', $email)->first();
    }
    
    /**
     * Verify user login credentials
     */
    public function verifyLogin($email, $password)
    {
        $user = $this->findByEmail($email);
        
        if ($user && password_verify($password, $user['password_hash']) && $user['is_active']) {
            // Update last login
            $this->update($user['user_id'], ['last_login_at' => date('Y-m-d H:i:s')]);
            return $user;
        }
        
        return false;
    }
    
    /**
     * Check if email exists
     */
    public function emailExists($email)
    {
        return $this->where('email', $email)->countAllResults() > 0;
    }
    
    /**
     * Get all users with their stats (for admin)
     */
    public function getAllUsersWithStats()
    {
        $users = $this->findAll();
        $jsonSiloModel = new \App\Models\JsonSiloModel();
        $apiKeyModel = new \App\Models\ApiKeyModel();
        
        foreach ($users as &$user) {
            $fileCount = $jsonSiloModel->where('account_id', $user['user_id'])->countAllResults();
            $apiKeyCount = $apiKeyModel->where('user_id', $user['user_id'])->countAllResults();
            
            // Calculate storage usage
            $files = $jsonSiloModel->where('account_id', $user['user_id'])->findAll();
            $totalSize = 0;
            foreach ($files as $file) {
                $totalSize += strlen($file['json_content']);
            }
            $storageMB = round($totalSize / (1024 * 1024), 2);
            
            $user['file_count'] = $fileCount;
            $user['api_key_count'] = $apiKeyCount;
            $user['storage_mb'] = $storageMB;
            $user['storage_percent'] = $user['max_storage_mb'] >= 999999 ? 0 : ($user['max_storage_mb'] > 0 ? round(($storageMB / $user['max_storage_mb']) * 100, 2) : 0);
            $user['files_percent'] = $user['max_files'] >= 999999 ? 0 : ($user['max_files'] > 0 ? round(($fileCount / $user['max_files']) * 100, 2) : 0);
        }
        
        return $users;
    }
    
    /**
     * Get user limits based on type
     */
    private function getUserLimits($userType)
    {
        switch ($userType) {
            case 'free':
                return ['max_files' => 10, 'max_storage_mb' => 10];
            case 'paid':
                return ['max_files' => 15, 'max_storage_mb' => 100];
            case 'admin':
                return ['max_files' => 999999, 'max_storage_mb' => 999999];
            default:
                return ['max_files' => 10, 'max_storage_mb' => 10];
        }
    }
    
    /**
     * Get rate limits based on user type
     */
    public function getRateLimits($userType)
    {
        $limits = [
            'free' => [
                'requests_per_hour' => 200,    // Fair for free users
                'burst_limit' => 30,           // Reasonable burst per minute
                'daily_limit' => 2000          // Daily cap
            ],
            'paid' => [
                'requests_per_hour' => 1000,   // 5x more than free users
                'burst_limit' => 150,          // 5x more burst capability
                'daily_limit' => 20000         // 10x more daily cap
            ],
            'admin' => [
                'requests_per_hour' => 999999, // Unlimited
                'burst_limit' => 999999,       // Unlimited
                'daily_limit' => 999999        // Unlimited
            ]
        ];
        
        return $limits[$userType] ?? $limits['free'];
    }
    
    /**
     * Get formatted rate limit display for user
     */
    public function getRateLimitDisplay($userType)
    {
        $limits = $this->getRateLimits($userType);
        
        if ($userType === 'admin') {
            return 'Unlimited';
        }
        
        return $limits['requests_per_hour'] . '/h';
    }
    
    /**
     * Check if user has unlimited access (admin)
     */
    public function hasUnlimitedAccess($userType)
    {
        return $userType === 'admin';
    }
}
