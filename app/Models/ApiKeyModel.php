<?php

namespace App\Models;

use CodeIgniter\Model;

class ApiKeyModel extends Model
{
    protected $table            = 'api_keys';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'key_name', 
        'api_key',
        'created_at',
        'last_used_at',
        'is_active',
        'allowed_domains',
        'domain_lock_enabled'
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'user_id'   => 'required|max_length[6]',
        'key_name'  => 'required|max_length[255]',
        'api_key'   => 'required|max_length[64]|is_unique[api_keys.api_key]'
    ];
    
    protected $validationMessages   = [
        'api_key' => [
            'is_unique' => 'This API key already exists.'
        ]
    ];
    
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Additional methods for API key management
    
    /**
     * Generate a new API key
     */
    public function generateApiKey(): string
    {
        // Generate a secure random API key
        return 'rio_' . bin2hex(random_bytes(24)); // 48 characters + prefix
    }
    
    /**
     * Get all API keys for a user
     */
    public function getUserApiKeys(string $userId): array
    {
        return $this->where('user_id', $userId)
                    ->where('is_active', 1)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }
    
    /**
     * Verify API key and get user info
     */
    public function verifyApiKey(string $apiKey): ?array
    {
        $key = $this->where('api_key', $apiKey)
                    ->where('is_active', 1)
                    ->first();
        
        if ($key) {
            // Update last used timestamp
            $this->update($key['id'], ['last_used_at' => date('Y-m-d H:i:s')]);
        }
        
        return $key;
    }
    
    /**
     * Create a new API key
     */
    public function createApiKey(string $userId, string $keyName): array
    {
        return $this->createApiKeyWithDomains($userId, $keyName, [], false);
    }
    
    /**
     * Revoke an API key
     */
    public function revokeApiKey(int $keyId, string $userId): bool
    {
        return $this->where('id', $keyId)
                    ->where('user_id', $userId)
                    ->set('is_active', 0)
                    ->update();
    }
    
    /**
     * Get API key usage stats
     */
    public function getApiKeyStats(string $userId): array
    {
        $total = $this->where('user_id', $userId)->countAllResults();
        $active = $this->where('user_id', $userId)->where('is_active', 1)->countAllResults();
        $recentlyUsed = $this->where('user_id', $userId)
                             ->where('last_used_at >=', date('Y-m-d H:i:s', strtotime('-7 days')))
                             ->countAllResults();
        
        return [
            'total' => $total,
            'active' => $active,
            'recently_used' => $recentlyUsed
        ];
    }
    
    /**
     * Validate domain against API key restrictions
     */
    public function validateDomain(string $apiKey, string $domain): bool
    {
        $key = $this->where('api_key', $apiKey)
                    ->where('is_active', 1)
                    ->first();
        
        if (!$key) {
            return false;
        }
        
        // If domain lock is not enabled, allow all domains
        if (!$key['domain_lock_enabled']) {
            return true;
        }
        
        // If no allowed domains specified, deny all
        if (empty($key['allowed_domains'])) {
            return false;
        }
        
        // Parse allowed domains from JSON
        $allowedDomains = json_decode($key['allowed_domains'], true);
        if (!is_array($allowedDomains)) {
            return false;
        }
        
        // Normalize domain (remove protocol, port, path)
        $normalizedDomain = $this->normalizeDomain($domain);
        
        // Check if domain is in allowed list
        foreach ($allowedDomains as $allowedDomain) {
            $normalizedAllowed = $this->normalizeDomain($allowedDomain);
            
            // Exact match
            if ($normalizedDomain === $normalizedAllowed) {
                return true;
            }
            
            // Wildcard subdomain support (*.example.com)
            if (strpos($normalizedAllowed, '*.') === 0) {
                $baseDomain = substr($normalizedAllowed, 2);
                if ($normalizedDomain === $baseDomain || 
                    str_ends_with($normalizedDomain, '.' . $baseDomain)) {
                    return true;
                }
            }
        }
        
        return false;
    }
    
    /**
     * Normalize domain for comparison
     */
    private function normalizeDomain(string $domain): string
    {
        // Remove protocol
        $domain = preg_replace('#^https?://#i', '', $domain);
        
        // Remove port
        $domain = preg_replace('#:\d+$#', '', $domain);
        
        // Remove path
        $domain = preg_replace('#/.*$#', '', $domain);
        
        // Convert to lowercase
        $domain = strtolower(trim($domain));
        
        return $domain;
    }
    
    /**
     * Create a new API key with domain restrictions
     */
    public function createApiKeyWithDomains(string $userId, string $keyName, array $domains = [], bool $domainLockEnabled = false): array
    {
        $apiKey = $this->generateApiKey();
        
        $data = [
            'user_id' => $userId,
            'key_name' => $keyName,
            'api_key' => $apiKey,
            'created_at' => date('Y-m-d H:i:s'),
            'is_active' => 1,
            'domain_lock_enabled' => $domainLockEnabled ? 1 : 0,
            'allowed_domains' => $domainLockEnabled && !empty($domains) ? json_encode($domains) : null
        ];
        
        $id = $this->insert($data);
        
        if ($id) {
            return $this->find($id);
        }
        
        throw new \Exception('Failed to create API key');
    }
    
    /**
     * Update domain restrictions for an API key
     */
    public function updateDomainRestrictions(int $keyId, string $userId, array $domains = [], bool $domainLockEnabled = false): bool
    {
        $updateData = [
            'domain_lock_enabled' => $domainLockEnabled ? 1 : 0,
            'allowed_domains' => $domainLockEnabled && !empty($domains) ? json_encode($domains) : null
        ];
        
        return $this->where('id', $keyId)
                    ->where('user_id', $userId)
                    ->set($updateData)
                    ->update();
    }
}
