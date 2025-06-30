<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\Controller;
use App\Models\ApiKeyModel;
use App\Models\JsonSiloModel;
use App\Models\UserModel;

class ApiController extends BaseController
{
    protected $format = 'json';
    
    protected $apiKeyModel;
    protected $userModel;
    protected $startTime;
    
    // Rate limiting storage (in production, use Redis or database)
    private static $requestCounts = [];
    
    
    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);
        
        // Initialize properties
        $this->startTime = microtime(true);
        
        // Initialize models
        $this->apiKeyModel = new ApiKeyModel();
        $this->userModel = new UserModel();
        
        // Load helpers
        helper(['url', 'form']);
    }
    
    /**
     * Common API setup - headers, CORS, and initial validation
     */
    private function setupApiResponse(): void
    {
        // Set all security and CORS headers
        $this->setSecurityHeaders();
        
        // Reset start time for this specific request
        $this->startTime = microtime(true);
        
        // Add server timing header
        $this->response->setHeader('Server-Timing', 'app;desc="API Processing"');
    }
    
    /**
     * Set comprehensive security headers and CORS policy
     */
    private function setSecurityHeaders(): void
    {
        // Ensure response object is available
        if (!$this->response) {
            $this->response = service('response');
        }
        
        // Essential Security Headers
        $this->response->setHeader('X-Content-Type-Options', 'nosniff');
        $this->response->setHeader('X-Frame-Options', 'DENY');
        $this->response->setHeader('X-XSS-Protection', '1; mode=block');
        $this->response->setHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $this->response->setHeader('X-Permitted-Cross-Domain-Policies', 'none');
        $this->response->setHeader('X-Download-Options', 'noopen');
        
        // Enhanced Security Headers
        if ($this->request->isSecure() || ENVIRONMENT === 'production') {
            $this->response->setHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }
        
        // API-specific Content Security Policy
        $this->response->setHeader('Content-Security-Policy', 
            "default-src 'none'; " .
            "script-src 'none'; " .
            "object-src 'none'; " .
            "base-uri 'none'; " .
            "form-action 'none'; " .
            "frame-ancestors 'none';"
        );
        
        // CORS headers with improved configuration
        $this->setCorsHeaders();
        
        // Cache control for API responses
        $this->response->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, private, max-age=0');
        $this->response->setHeader('Pragma', 'no-cache');
        $this->response->setHeader('Expires', '0');
        
        // API Information Headers
        $this->response->setHeader('API-Version', '1.0.0');
        $this->response->setHeader('Server', 'RioJSON-API');
        $this->response->setHeader('X-API-RateLimit-Policy', 'user-based');
        
        // Content-Type for API responses
        $this->response->setHeader('Content-Type', 'application/json; charset=UTF-8');
        $this->response->setHeader('Vary', 'Accept, Authorization, Origin');
    }
    
    /**
     * Set CORS headers with improved handling
     */
    private function setCorsHeaders(): void
    {
        $origin = $this->request->getHeaderLine('Origin');
        $method = $this->request->getMethod();
        
        // Handle preflight requests
        if ($method === 'OPTIONS') {
            $this->response->setHeader('Access-Control-Allow-Origin', '*');
            $this->response->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS, HEAD');
            $this->response->setHeader('Access-Control-Allow-Headers', 
                'Authorization, Content-Type, Accept, Origin, X-Requested-With, ' .
                'Access-Control-Request-Method, Access-Control-Request-Headers, ' .
                'X-API-Key, Cache-Control'
            );
            $this->response->setHeader('Access-Control-Max-Age', '86400'); // 24 hours
        } else {
            // For actual requests, be more restrictive if needed
            $this->response->setHeader('Access-Control-Allow-Origin', '*');
        }
        
        // Headers to expose to the client
        $this->response->setHeader('Access-Control-Expose-Headers', 
            'X-RateLimit-Remaining-Hourly, X-RateLimit-Limit-Hourly, ' .
            'X-RateLimit-Remaining-Burst, X-RateLimit-Limit-Burst, ' .
            'X-RateLimit-Reset-Hourly, X-RateLimit-Reset-Burst, ' .
            'API-Version, Content-Length, X-Response-Time'
        );
        
        // Credentials policy
        $this->response->setHeader('Access-Control-Allow-Credentials', 'false');
        
        // Vary header for proper caching
        $this->response->setHeader('Vary', 'Accept, Authorization, Origin');
    }
    
    /**
     * Rate limiting check with user-based limits
     */
    private function checkRateLimit(string $identifier, string $userType = 'free'): bool
    {
        // Get user-specific rate limits
        $rateLimits = $this->userModel->getRateLimits($userType);
        
        // Admin users have unlimited access
        if ($userType === 'admin') {
            return true;
        }
        
        $now = time();
        $hourWindow = floor($now / 3600); // 1-hour window
        $minuteWindow = floor($now / 60);  // 1-minute window
        
        $hourKey = "hour_" . $hourWindow . "_" . $identifier;
        $minuteKey = "minute_" . $minuteWindow . "_" . $identifier;
        
        // Initialize if not exists
        if (!isset(self::$requestCounts[$hourKey])) {
            self::$requestCounts[$hourKey] = 0;
        }
        if (!isset(self::$requestCounts[$minuteKey])) {
            self::$requestCounts[$minuteKey] = 0;
        }
        
        // Check limits based on user type
        if (self::$requestCounts[$hourKey] >= $rateLimits['requests_per_hour']) {
            return false;
        }
        if (self::$requestCounts[$minuteKey] >= $rateLimits['burst_limit']) {
            return false;
        }
        
        // Increment counters
        self::$requestCounts[$hourKey]++;
        self::$requestCounts[$minuteKey]++;
        
        // Clean old entries (simple cleanup)
        if (count(self::$requestCounts) > 1000) {
            $cutoff = $now - 7200; // Keep last 2 hours
            foreach (self::$requestCounts as $key => $value) {
                if (strpos($key, 'hour_') === 0) {
                    $timestamp = (int)explode('_', $key)[1] * 3600;
                    if ($timestamp < $cutoff) {
                        unset(self::$requestCounts[$key]);
                    }
                }
                if (strpos($key, 'minute_') === 0) {
                    $timestamp = (int)explode('_', $key)[1] * 60;
                    if ($timestamp < $cutoff) {
                        unset(self::$requestCounts[$key]);
                    }
                }
            }
        }
        
        return true;
    }
    
    /**
     * Authenticate API request with enhanced security
     */
    private function authenticateRequest(): ?array
    {
        // Security check: Ensure HTTPS in production (optional)
        $isHTTPS = $this->request->isSecure();
        if (!$isHTTPS && ENVIRONMENT === 'production') {
            $this->response->setStatusCode(426); // Upgrade Required
            return null;
        }
        
        // Get client IP for rate limiting and logging
        $clientIP = $this->request->getIPAddress();
        
        // Validate IP address format
        if (!filter_var($clientIP, FILTER_VALIDATE_IP)) {
            $this->response->setStatusCode(400);
            return null;
        }
        
        // Get API key from Authorization header first to determine user type
        $authHeader = $this->request->getHeaderLine('Authorization');
        
        if (!$authHeader) {
            return null;
        }
        
        // Sanitize authorization header
        $authValue = trim($authHeader);
        
        // Check for suspicious characters in auth header
        if (preg_match('/[<>"\'\x00-\x1f\x7f-\xff]/', $authValue)) {
            return null;
        }
        
        // Support both "Bearer <key>" and "API-Key <key>" formats
        $apiKey = null;
        if (preg_match('/^Bearer\s+(.+)$/i', $authValue, $matches)) {
            $apiKey = trim($matches[1]);
        } elseif (preg_match('/^API-Key\s+(.+)$/i', $authValue, $matches)) {
            $apiKey = trim($matches[1]);
        } elseif (preg_match('/^rio_[a-f0-9]{40,48}$/', $authValue)) {
            // Direct API key without prefix
            $apiKey = $authValue;
        }
        
        if (!$apiKey) {
            return null;
        }
        
        // Validate API key format (support both 40 and 48 character keys)
        if (!preg_match('/^rio_[a-f0-9]{40,48}$/', $apiKey)) {
            return null;
        }
        
        // Additional length check for extra security
        $keyLength = strlen($apiKey);
        if ($keyLength < 44 || $keyLength > 52) {
            return null;
        }
        
        // Verify API key
        $keyData = $this->apiKeyModel->verifyApiKey($apiKey);
        
        if (!$keyData) {
            return null;
        }
        
        // Get user type for rate limiting
        $user = $this->userModel->find($keyData['user_id']);
        $userType = $user['user_type'] ?? 'free';
        
        // Check rate limiting with user-specific limits
        if (!$this->checkRateLimit($clientIP, $userType)) {
            $this->response->setStatusCode(429);
            return null;
        }
        
        if ($keyData) {
            // Check domain restrictions
            $origin = $this->request->getHeaderLine('Origin');
            $referer = $this->request->getHeaderLine('Referer');
            $host = $this->request->getHeaderLine('Host');
            
            // Get the domain from various sources
            $requestDomain = $this->extractDomainFromRequest($origin, $referer, $host);
            
            // Validate domain against API key restrictions
            if (!$this->apiKeyModel->validateDomain($apiKey, $requestDomain)) {
                log_message('warning', "Domain restriction violated for API key: {$apiKey}, Domain: {$requestDomain}");
                $this->response->setStatusCode(403); // Forbidden
                return null;
            }
            // Additional rate limiting per API key
            if (!$this->checkRateLimit('key_' . $apiKey, $userType)) {
                $this->response->setStatusCode(429);
                return null;
            }
            
            // Log successful authentication (optional)
            log_message('info', "API access granted for user: {$keyData['user_id']}, IP: {$clientIP}");
        }
        
        return $keyData;
    }
    
    /**
     * Validate and sanitize user ID format
     */
    private function validateUserId(string $userId): bool
    {
        // Sanitize input
        $userId = trim($userId);
        
        // Check basic format (6 alphanumeric characters)
        if (!preg_match('/^[a-zA-Z0-9]{6}$/', $userId)) {
            return false;
        }
        
        // Additional security: prevent SQL injection patterns
        if (preg_match('/[\'";\\\\%_]/', $userId)) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Validate and sanitize file ID format  
     */
    private function validateFileId(string $fileId): bool
    {
        // Sanitize input
        $fileId = trim($fileId);
        
        // Check basic format (11 alphanumeric with hyphens)
        if (!preg_match('/^[a-zA-Z0-9\-]{11}$/', $fileId)) {
            return false;
        }
        
        // Additional security: prevent path traversal
        if (strpos($fileId, '..') !== false || strpos($fileId, '/') !== false || strpos($fileId, '\\') !== false) {
            return false;
        }
        
        // Prevent SQL injection patterns
        if (preg_match('/[\'";\\\\%_]/', $fileId)) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Return error response with headers
     */
    private function errorResponse(string $message, int $code = 400): \CodeIgniter\HTTP\ResponseInterface
    {
        // Add response time header if available
        if (isset($this->startTime)) {
            $responseTime = round((microtime(true) - $this->startTime) * 1000, 2);
            $this->response->setHeader('X-Response-Time', $responseTime . 'ms');
        }
        
        return $this->response->setStatusCode($code)->setJSON([
            'error' => true,
            'message' => $message,
            'code' => $code,
            'timestamp' => date('c')
        ]);
    }
    
    /**
     * Return success response with headers
     */
    private function successResponse($data, string $message = 'Success'): \CodeIgniter\HTTP\ResponseInterface
    {
        // Add response time header if available
        if (isset($this->startTime)) {
            $responseTime = round((microtime(true) - $this->startTime) * 1000, 2);
            $this->response->setHeader('X-Response-Time', $responseTime . 'ms');
        }
        
        return $this->response->setJSON([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'timestamp' => date('c')
        ]);
    }
    
    /**
     * Detect and prevent potential security threats
     */
    private function validateRequestSecurity(): bool
    {
        // Check for common attack patterns in headers
        $suspiciousHeaders = ['x-forwarded-for', 'x-real-ip', 'x-cluster-client-ip'];
        foreach ($suspiciousHeaders as $header) {
            $value = $this->request->getHeaderLine($header);
            if ($value && preg_match('/[<>"\'\x00-\x1f\x7f-\xff]/', $value)) {
                return false;
            }
        }
        
        // Check User-Agent for suspicious patterns
        $userAgent = $this->request->getHeaderLine('User-Agent');
        if ($userAgent) {
            // Block common attack tools
            $blockedAgents = ['sqlmap', 'nmap', 'nikto', 'gobuster', 'dirbuster'];
            foreach ($blockedAgents as $blocked) {
                if (stripos($userAgent, $blocked) !== false) {
                    return false;
                }
            }
        }
        
        // Check for suspicious query parameters
        $queryParams = $this->request->getGet();
        if ($queryParams) {
            foreach ($queryParams as $key => $value) {
                if (is_string($value) && preg_match('/[<>"\'\x00-\x1f\x7f-\xff]/', $value)) {
                    return false;
                }
            }
        }
        
        // Check request size (prevent DoS)
        $contentLength = $this->request->getHeaderLine('Content-Length');
        if ($contentLength && intval($contentLength) > 1048576) { // 1MB limit
            return false;
        }
        
        return true;
    }
    
    /**
     * Extract domain from request headers
     */
    private function extractDomainFromRequest(?string $origin, ?string $referer, ?string $host): string
    {
        // Priority: Origin > Referer > Host
        if (!empty($origin)) {
            return $this->normalizeDomain($origin);
        }
        
        if (!empty($referer)) {
            return $this->normalizeDomain($referer);
        }
        
        if (!empty($host)) {
            return $this->normalizeDomain($host);
        }
        
        // Fallback to localhost for direct API calls
        return 'localhost';
    }
    
    /**
     * Normalize domain for comparison (same logic as in ApiKeyModel)
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
     * Set rate limit headers in response
     */
    private function setRateLimitHeaders(string $userType, string $identifier): void
    {
        $rateLimits = $this->userModel->getRateLimits($userType);
        
        // Skip for admin users
        if ($userType === 'admin') {
            $this->response->setHeader('X-RateLimit-Limit', 'unlimited');
            $this->response->setHeader('X-RateLimit-Remaining', 'unlimited');
            $this->response->setHeader('X-RateLimit-Reset', 'n/a');
            return;
        }
        
        $now = time();
        $hourWindow = floor($now / 3600);
        $minuteWindow = floor($now / 60);
        
        $hourKey = "hour_" . $hourWindow . "_" . $identifier;
        $minuteKey = "minute_" . $minuteWindow . "_" . $identifier;
        
        $hourlyUsed = self::$requestCounts[$hourKey] ?? 0;
        $burstUsed = self::$requestCounts[$minuteKey] ?? 0;
        
        $hourlyRemaining = max(0, $rateLimits['requests_per_hour'] - $hourlyUsed);
        $burstRemaining = max(0, $rateLimits['burst_limit'] - $burstUsed);
        
        // Set headers
        $this->response->setHeader('X-RateLimit-Limit-Hourly', (string)$rateLimits['requests_per_hour']);
        $this->response->setHeader('X-RateLimit-Remaining-Hourly', (string)$hourlyRemaining);
        $this->response->setHeader('X-RateLimit-Limit-Burst', (string)$rateLimits['burst_limit']);
        $this->response->setHeader('X-RateLimit-Remaining-Burst', (string)$burstRemaining);
        $this->response->setHeader('X-RateLimit-Reset-Hourly', (string)(($hourWindow + 1) * 3600));
        $this->response->setHeader('X-RateLimit-Reset-Burst', (string)(($minuteWindow + 1) * 60));
    }

    /**
     * Log API request for live monitoring
     */
    private function logApiRequest($userType, $userEmail, $endpoint, $method, $status, $responseTime, $rateLimitHit = false): void
    {
        try {
            $db = \Config\Database::connect();
            
            $logData = [
                'user_id' => $this->request->user_id ?? null,
                'user_email' => $userEmail,
                'user_type' => $userType,
                'endpoint' => $endpoint,
                'method' => $method,
                'status' => $status,
                'response_time' => $responseTime . 'ms',
                'ip' => $this->request->getIPAddress(),
                'rate_limit_hit' => $rateLimitHit ? 1 : 0,
                'timestamp' => date('Y-m-d H:i:s')
            ];
            
            $db->table('api_request_logs')->insert($logData);
        } catch (\Exception $e) {
            // Log error but don't break API functionality
            log_message('error', 'Failed to log API request: ' . $e->getMessage());
        }
    }

    /**
     * Get JSON file by user_id and file_id
     * GET /api/v1/{user_id}/{file_id}
     */
    public function getJsonFile($userId = null, $fileId = null)
    {
        // Setup API response with headers and timing
        $this->setupApiResponse();
        $startTime = microtime(true);
        
        // Validate request security
        if (!$this->validateRequestSecurity()) {
            $responseTime = round((microtime(true) - $startTime) * 1000);
            $this->logApiRequest('unknown', 'unknown', '/api/v1/' . $userId . '/' . $fileId, 'GET', 400, $responseTime);
            return $this->errorResponse('Security validation failed', 400);
        }
        
        // Authenticate request
        $apiKey = $this->authenticateRequest();
        if (!$apiKey) {
            $responseTime = round((microtime(true) - $startTime) * 1000);
            $this->logApiRequest('unknown', 'unknown', '/api/v1/' . $userId . '/' . $fileId, 'GET', 401, $responseTime);
            return $this->errorResponse('Invalid or missing API key', 401);
        }
        
        // Get user info for logging
        $user = $this->userModel->find($apiKey['user_id']);
        $userType = $user['user_type'] ?? 'free';
        $userEmail = $user['email'] ?? 'unknown';
        
        // Check rate limits
        $rateLimited = $this->checkRateLimit($userType, $this->request->getIPAddress());
        
        // Set rate limit headers
        $this->setRateLimitHeaders($userType, $this->request->getIPAddress());
        
        // Validate parameters
        if (!$userId || !$fileId) {
            $responseTime = round((microtime(true) - $startTime) * 1000);
            $this->logApiRequest($userType, $userEmail, '/api/v1/' . $userId . '/' . $fileId, 'GET', 400, $responseTime);
            return $this->errorResponse('Missing user_id or file_id', 400);
        }
        
        // Validate parameter formats
        if (!$this->validateUserId($userId)) {
            $responseTime = round((microtime(true) - $startTime) * 1000);
            $this->logApiRequest($userType, $userEmail, '/api/v1/' . $userId . '/' . $fileId, 'GET', 400, $responseTime);
            return $this->errorResponse('Invalid user_id format', 400);
        }
        
        if (!$this->validateFileId($fileId)) {
            $responseTime = round((microtime(true) - $startTime) * 1000);
            $this->logApiRequest($userType, $userEmail, '/api/v1/' . $userId . '/' . $fileId, 'GET', 400, $responseTime);
            return $this->errorResponse('Invalid file_id format', 400);
        }
        
        // Check if API key belongs to the requested user
        if ($apiKey['user_id'] !== $userId) {
            $responseTime = round((microtime(true) - $startTime) * 1000);
            $this->logApiRequest($userType, $userEmail, '/api/v1/' . $userId . '/' . $fileId, 'GET', 403, $responseTime);
            return $this->errorResponse('API key does not have access to this user\'s data', 403);
        }
        
        try {
            // Get the JSON file
            $jsonSiloModel = new JsonSiloModel();
            $file = $jsonSiloModel->where('account_id', $userId)
                                  ->where('id', $fileId)
                                  ->first();
            
            if (!$file) {
                $responseTime = round((microtime(true) - $startTime) * 1000);
                $this->logApiRequest($userType, $userEmail, '/api/v1/' . $userId . '/' . $fileId, 'GET', 404, $responseTime);
                return $this->errorResponse('JSON file not found', 404);
            }
            
            // Parse JSON content
            $jsonContent = json_decode($file['json_content'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $responseTime = round((microtime(true) - $startTime) * 1000);
                $this->logApiRequest($userType, $userEmail, '/api/v1/' . $userId . '/' . $fileId, 'GET', 500, $responseTime);
                return $this->errorResponse('Invalid JSON content in file', 500);
            }
            
            // Success - log the request
            $responseTime = round((microtime(true) - $startTime) * 1000);
            $this->logApiRequest($userType, $userEmail, '/api/v1/' . $userId . '/' . $fileId, 'GET', 200, $responseTime, $rateLimited);
            
            // Return the JSON data
            return $this->successResponse([
                'file_id' => $file['id'],
                'filename' => $file['original_filename'],
                'uploaded_at' => $file['uploaded_at'],
                'size' => strlen($file['json_content']),
                'content' => $jsonContent
            ], 'JSON file retrieved successfully');
            
        } catch (\Exception $e) {
            $responseTime = round((microtime(true) - $startTime) * 1000);
            $this->logApiRequest($userType, $userEmail, '/api/v1/' . $userId . '/' . $fileId, 'GET', 500, $responseTime);
            return $this->errorResponse('Internal server error', 500);
        }
    }
    
    /**
     * Get all JSON files for a user
     * GET /api/v1/{user_id}
     */
    public function getUserFiles($userId = null)
    {
        // Setup API response with headers and timing
        $this->setupApiResponse();
        $startTime = microtime(true);
        
        // Validate request security
        if (!$this->validateRequestSecurity()) {
            $responseTime = round((microtime(true) - $startTime) * 1000);
            $this->logApiRequest('unknown', 'unknown', '/api/v1/' . $userId, 'GET', 400, $responseTime);
            return $this->errorResponse('Security validation failed', 400);
        }
        
        // Authenticate request
        $apiKey = $this->authenticateRequest();
        if (!$apiKey) {
            $responseTime = round((microtime(true) - $startTime) * 1000);
            $this->logApiRequest('unknown', 'unknown', '/api/v1/' . $userId, 'GET', 401, $responseTime);
            return $this->errorResponse('Invalid or missing API key', 401);
        }
        
        // Get user info for logging
        $user = $this->userModel->find($apiKey['user_id']);
        $userType = $user['user_type'] ?? 'free';
        $userEmail = $user['email'] ?? 'unknown';
        
        // Check rate limits
        $rateLimited = $this->checkRateLimit($userType, $this->request->getIPAddress());
        
        // Set rate limit headers
        $this->setRateLimitHeaders($userType, $this->request->getIPAddress());
        
        // Validate parameters
        if (!$userId) {
            $responseTime = round((microtime(true) - $startTime) * 1000);
            $this->logApiRequest($userType, $userEmail, '/api/v1/' . $userId, 'GET', 400, $responseTime);
            return $this->errorResponse('Missing user_id', 400);
        }
        
        // Validate parameter format
        if (!$this->validateUserId($userId)) {
            $responseTime = round((microtime(true) - $startTime) * 1000);
            $this->logApiRequest($userType, $userEmail, '/api/v1/' . $userId, 'GET', 400, $responseTime);
            return $this->errorResponse('Invalid user_id format', 400);
        }
        
        // Check if API key belongs to the requested user
        if ($apiKey['user_id'] !== $userId) {
            $responseTime = round((microtime(true) - $startTime) * 1000);
            $this->logApiRequest($userType, $userEmail, '/api/v1/' . $userId, 'GET', 403, $responseTime);
            return $this->errorResponse('API key does not have access to this user\'s data', 403);
        }
        
        try {
            // Get all JSON files for the user
            $jsonSiloModel = new JsonSiloModel();
            $files = $jsonSiloModel->where('account_id', $userId)
                                   ->orderBy('uploaded_at', 'DESC')
                                   ->findAll();
            
            // Format response data
            $fileList = array_map(function($file) {
                return [
                    'file_id' => $file['id'],
                    'filename' => $file['original_filename'],
                    'uploaded_at' => $file['uploaded_at'],
                    'size' => strlen($file['json_content'])
                ];
            }, $files);
            
            // Success - log the request
            $responseTime = round((microtime(true) - $startTime) * 1000);
            $this->logApiRequest($userType, $userEmail, '/api/v1/' . $userId, 'GET', 200, $responseTime, $rateLimited);
            
            return $this->successResponse([
                'user_id' => $userId,
                'total_files' => count($fileList),
                'files' => $fileList
            ], 'User files retrieved successfully');
            
        } catch (\Exception $e) {
            $responseTime = round((microtime(true) - $startTime) * 1000);
            $this->logApiRequest($userType, $userEmail, '/api/v1/' . $userId, 'GET', 500, $responseTime);
            return $this->errorResponse('Internal server error', 500);
        }
    }
    
    /**
     * Get raw JSON content
     * GET /api/v1/{user_id}/{file_id}/raw
     */
    public function getRawJson($userId = null, $fileId = null)
    {
        // Setup API response with headers and timing
        $this->setupApiResponse();
        
        // Validate request security
        if (!$this->validateRequestSecurity()) {
            return $this->errorResponse('Security validation failed', 400);
        }
        
        // Authenticate request
        $apiKey = $this->authenticateRequest();
        if (!$apiKey) {
            return $this->errorResponse('Invalid or missing API key', 401);
        }
        
        // Get user type for rate limit headers
        $user = $this->userModel->find($apiKey['user_id']);
        $userType = $user['user_type'] ?? 'free';
        
        // Set rate limit headers
        $this->setRateLimitHeaders($userType, $this->request->getIPAddress());
        
        // Validate parameters
        if (!$userId || !$fileId) {
            return $this->errorResponse('Missing user_id or file_id', 400);
        }
        
        // Validate parameter formats
        if (!$this->validateUserId($userId)) {
            return $this->errorResponse('Invalid user_id format', 400);
        }
        
        if (!$this->validateFileId($fileId)) {
            return $this->errorResponse('Invalid file_id format', 400);
        }
        
        // Check if API key belongs to the requested user
        if ($apiKey['user_id'] !== $userId) {
            return $this->errorResponse('API key does not have access to this user\'s data', 403);
        }
        
        try {
            // Get the JSON file
            $jsonSiloModel = new JsonSiloModel();
            $file = $jsonSiloModel->where('account_id', $userId)
                                  ->where('id', $fileId)
                                  ->first();
            
            if (!$file) {
                return $this->errorResponse('JSON file not found', 404);
            }
            
            // Validate JSON
            json_decode($file['json_content']);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return $this->errorResponse('Invalid JSON content in file', 500);
            }
            
            // Return raw JSON with proper content type and headers
            $responseTime = round((microtime(true) - $this->startTime) * 1000, 2);
            $this->response->setHeader('X-Response-Time', $responseTime . 'ms');
            $this->response->setHeader('Content-Disposition', 'inline; filename="' . $file['original_filename'] . '"');
            
            return $this->response
                        ->setContentType('application/json; charset=UTF-8')
                        ->setBody($file['json_content']);
                        
        } catch (\Exception $e) {
            return $this->errorResponse('Internal server error', 500);
        }
    }
    
    /**
     * API documentation/info endpoint
     * GET /api/v1/info
     */
    public function info()
    {
        // Setup API response with headers and timing
        $this->setupApiResponse();
        
        return $this->successResponse([
            'name' => 'RioConsoleJSON API',
            'version' => '1.0.0',
            'description' => 'REST API for accessing JSON silo data',
            'endpoints' => [
                'GET /api/v1/{user_id}' => 'Get all files for a user',
                'GET /api/v1/{user_id}/{file_id}' => 'Get specific JSON file with metadata',
                'GET /api/v1/{user_id}/{file_id}/raw' => 'Get raw JSON content'
            ],
            'authentication' => [
                'type' => 'API Key',
                'header' => 'Authorization: Bearer <api_key>',
                'alternative' => 'Authorization: API-Key <api_key>'
            ],
            'rate_limits' => [
                'free_users' => [
                    'requests_per_hour' => 100,
                    'daily_limit' => 1000,
                    'burst_limit' => 20
                ],
                'paid_users' => [
                    'requests_per_hour' => 500,
                    'daily_limit' => 10000,
                    'burst_limit' => 100
                ],
                'admin_users' => [
                    'requests_per_hour' => 'unlimited',
                    'daily_limit' => 'unlimited',
                    'burst_limit' => 'unlimited'
                ],
                'note' => 'Rate limits vary by user type. Admin users have unlimited access.'
            ]
        ], 'API information');
    }
    
    /**
     * Health check endpoint
     * GET /api/v1/health
     */
    public function health()
    {
        // Setup API response with headers and timing
        $this->setupApiResponse();
        
        return $this->successResponse([
            'status' => 'healthy',
            'server_time' => date('c'),
            'version' => '1.0.0'
        ], 'API is healthy');
    }
    
    /**
     * Handle CORS preflight requests (OPTIONS)
     */
    public function options()
    {
        // Setup API response with headers and timing
        $this->setupApiResponse();
        
        // Log preflight request for monitoring
        $origin = $this->request->getHeaderLine('Origin');
        $method = $this->request->getHeaderLine('Access-Control-Request-Method');
        $headers = $this->request->getHeaderLine('Access-Control-Request-Headers');
        
        log_message('info', "CORS Preflight: Origin={$origin}, Method={$method}, Headers={$headers}");
        
        // Return successful preflight response
        return $this->response
            ->setStatusCode(200)
            ->setBody(''); // Empty body for preflight
    }
}
