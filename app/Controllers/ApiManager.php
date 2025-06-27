<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ApiKeyModel;
use App\Models\UserModel;

class ApiManager extends BaseController
{
    protected $apiKeyModel;
    protected $userModel;

    public function __construct()
    {
        $this->apiKeyModel = new ApiKeyModel();
        $this->userModel = new UserModel();
        helper(['url', 'form']);
    }

    /**
     * Check if user is logged in
     */
    private function checkAuth()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth/login');
        }
        return null;
    }

    /**
     * API Keys dashboard
     */
    public function index()
    {
        $redirect = $this->checkAuth();
        if ($redirect) return $redirect;

        $userId = session()->get('user_id');
        
        // Get user's API keys
        $apiKeys = $this->apiKeyModel->getUserApiKeys($userId);
        
        // Get API key statistics
        $stats = $this->apiKeyModel->getApiKeyStats($userId);
        
        // Get user info for rate limits
        $user = $this->userModel->find($userId);
        $userType = $user['user_type'] ?? 'free';
        
        // Get rate limit info
        $rateLimits = $this->userModel->getRateLimits($userType);
        $rateLimitDisplay = $this->userModel->getRateLimitDisplay($userType);

        $data = array_merge($this->getCommonViewData(), [
            'title' => 'API Keys',
            'apiKeys' => $apiKeys,
            'stats' => $stats,
            'rate_limits' => $rateLimits,
            'rate_limit_display' => $rateLimitDisplay,
            'user_type' => $userType
        ]);

        return view('dashboard/api_keys', $data);
    }

    /**
     * Create new API key
     */
    public function create()
    {
        $redirect = $this->checkAuth();
        if ($redirect) return $redirect;

        if ($this->request->getMethod() === 'POST') {
            $userId = session()->get('user_id');
            $keyName = $this->request->getPost('key_name');
            $enableDomainLock = $this->request->getPost('enable_domain_lock') === 'true';
            $allowedDomains = $this->request->getPost('allowed_domains');

            // Validate input
            if (empty($keyName)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'API key name is required'
                ]);
            }

            // Validate domains if domain lock is enabled
            $domainsArray = [];
            if ($enableDomainLock) {
                if (empty($allowedDomains) || !is_array($allowedDomains)) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'At least one domain is required when domain lock is enabled'
                    ]);
                }

                // Validate and sanitize domains
                foreach ($allowedDomains as $domain) {
                    $domain = trim($domain);
                    if (empty($domain)) continue;
                    
                    // Basic domain validation
                    if (!$this->isValidDomain($domain)) {
                        return $this->response->setJSON([
                            'success' => false,
                            'message' => "Invalid domain format: {$domain}"
                        ]);
                    }
                    
                    $domainsArray[] = $domain;
                }

                if (empty($domainsArray)) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'At least one valid domain is required'
                    ]);
                }
            }

            // Check if user already has too many keys (limit: 10)
            $existingKeys = $this->apiKeyModel->getUserApiKeys($userId);
            if (count($existingKeys) >= 10) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Maximum of 10 API keys allowed per user'
                ]);
            }

            try {
                // Create new API key with domain restrictions
                $newKey = $this->apiKeyModel->createApiKeyWithDomains($userId, $keyName, $domainsArray, $enableDomainLock);

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'API key created successfully',
                    'data' => [
                        'id' => $newKey['id'],
                        'key_name' => $newKey['key_name'],
                        'api_key' => $newKey['api_key'],
                        'created_at' => $newKey['created_at'],
                        'domain_lock_enabled' => $newKey['domain_lock_enabled'],
                        'allowed_domains' => $newKey['allowed_domains']
                    ]
                ]);

            } catch (\Exception $e) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to create API key: ' . $e->getMessage()
                ]);
            }
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Invalid request method'
        ]);
    }

    /**
     * Revoke/delete API key
     */
    public function revoke($keyId = null)
    {
        $redirect = $this->checkAuth();
        if ($redirect) return $redirect;

        if ($this->request->getMethod() === 'POST') {
            $userId = session()->get('user_id');

            if (!$keyId) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'API key ID is required'
                ]);
            }

            try {
                $success = $this->apiKeyModel->revokeApiKey((int)$keyId, $userId);

                if ($success) {
                    return $this->response->setJSON([
                        'success' => true,
                        'message' => 'API key revoked successfully'
                    ]);
                } else {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Failed to revoke API key or key not found'
                    ]);
                }

            } catch (\Exception $e) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Error revoking API key: ' . $e->getMessage()
                ]);
            }
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Invalid request method'
        ]);
    }

    /**
     * Get API key details
     */
    public function details($keyId = null)
    {
        $redirect = $this->checkAuth();
        if ($redirect) return $redirect;

        $userId = session()->get('user_id');

        if (!$keyId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'API key ID is required'
            ]);
        }

        // Get the API key
        $apiKey = $this->apiKeyModel->where('id', $keyId)
                                   ->where('user_id', $userId)
                                   ->first();

        if (!$apiKey) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'API key not found'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $apiKey
        ]);
    }

    /**
     * Generate API documentation view
     */
    public function docs()
    {
        $redirect = $this->checkAuth();
        if ($redirect) return $redirect;

        $userId = session()->get('user_id');
        
        // Get user information for rate limits
        $user = $this->userModel->find($userId);
        $userType = $user['user_type'] ?? 'free';
        
        // Get all rate limits for admin view
        $allRateLimits = [
            'free' => $this->userModel->getRateLimits('free'),
            'paid' => $this->userModel->getRateLimits('paid'),
            'admin' => $this->userModel->getRateLimits('admin')
        ];
        
        // Get current user's rate limits
        $currentRateLimits = $this->userModel->getRateLimits($userType);
        
        $data = array_merge($this->getCommonViewData(), [
            'title' => 'API Documentation',
            'user_id' => $userId,
            'user_type' => $userType,
            'base_url' => base_url(),
            'all_rate_limits' => $allRateLimits,
            'current_rate_limits' => $currentRateLimits
        ]);

        return view('dashboard/api_docs', $data);
    }

    /**
     * Update domain lock settings for an API key
     */
    public function updateDomainLock()
    {
        $redirect = $this->checkAuth();
        if ($redirect) return $redirect;

        if ($this->request->getMethod() === 'POST') {
            $userId = session()->get('user_id');
            $keyId = $this->request->getPost('key_id');
            $enableDomainLock = $this->request->getPost('enable_domain_lock') === 'true';
            $allowedDomains = $this->request->getPost('allowed_domains');

            // Validate input
            if (empty($keyId)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'API key ID is required'
                ]);
            }

            // Verify the API key belongs to the user
            $apiKey = $this->apiKeyModel->where('id', $keyId)
                                       ->where('user_id', $userId)
                                       ->first();

            if (!$apiKey) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'API key not found or access denied'
                ]);
            }

            // Validate domains if domain lock is enabled
            $domainsArray = [];
            if ($enableDomainLock) {
                if (empty($allowedDomains) || !is_array($allowedDomains)) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'At least one domain is required when domain lock is enabled'
                    ]);
                }

                // Validate and sanitize domains
                foreach ($allowedDomains as $domain) {
                    $domain = trim($domain);
                    if (empty($domain)) continue;
                    
                    // Basic domain validation
                    if (!$this->isValidDomain($domain)) {
                        return $this->response->setJSON([
                            'success' => false,
                            'message' => "Invalid domain format: {$domain}"
                        ]);
                    }
                    
                    $domainsArray[] = $domain;
                }

                if (empty($domainsArray)) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'At least one valid domain is required'
                    ]);
                }
            }

            try {
                // Update domain restrictions
                $success = $this->apiKeyModel->updateDomainRestrictions($keyId, $userId, $domainsArray, $enableDomainLock);

                if ($success) {
                    // Log the change for security audit
                    log_message('info', "Domain lock updated for API key ID: {$keyId}, User: {$userId}, Enabled: " . ($enableDomainLock ? 'Yes' : 'No'));

                    return $this->response->setJSON([
                        'success' => true,
                        'message' => 'Domain lock settings updated successfully',
                        'data' => [
                            'domain_lock_enabled' => $enableDomainLock,
                            'allowed_domains' => $enableDomainLock ? $domainsArray : null
                        ]
                    ]);
                } else {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Failed to update domain lock settings'
                    ]);
                }

            } catch (\Exception $e) {
                log_message('error', "Error updating domain lock for key {$keyId}: " . $e->getMessage());
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'An error occurred while updating domain lock settings'
                ]);
            }
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Invalid request method'
        ]);
    }

    /**
     * Validate domain format
     */
    private function isValidDomain(string $domain): bool
    {
        // Remove protocol if present
        $domain = preg_replace('#^https?://#i', '', $domain);
        
        // Remove port if present
        $domain = preg_replace('#:\d+$#', '', $domain);
        
        // Remove path if present
        $domain = preg_replace('#/.*$#', '', $domain);
        
        // Convert to lowercase
        $domain = strtolower(trim($domain));
        
        // Allow localhost
        if ($domain === 'localhost') {
            return true;
        }
        
        // Allow IP addresses
        if (filter_var($domain, FILTER_VALIDATE_IP)) {
            return true;
        }
        
        // Allow wildcard subdomains
        if (strpos($domain, '*.') === 0) {
            $baseDomain = substr($domain, 2);
            if (empty($baseDomain)) {
                return false;
            }
            $domain = $baseDomain;
        }
        
        // Basic domain validation
        if (empty($domain)) {
            return false;
        }
        
        // Check for valid domain format
        if (!preg_match('/^[a-z0-9]+([.-][a-z0-9]+)*\.[a-z]{2,}$/i', $domain)) {
            return false;
        }
        
        return true;
    }
}
