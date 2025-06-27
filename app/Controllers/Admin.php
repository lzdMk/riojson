<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\JsonSiloModel;
use App\Models\ApiKeyModel;

class Admin extends BaseController
{
    protected $userModel;
    protected $jsonSiloModel;
    protected $apiKeyModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->jsonSiloModel = new JsonSiloModel();
        $this->apiKeyModel = new ApiKeyModel();
    }

    public function index()
    {
        // Check if user is admin
        if (!$this->isAdmin()) {
            return redirect()->to('/dashboard')->with('error', 'Access denied. Admin privileges required.');
        }

        $data = [
            'title' => 'Admin Dashboard',
            'stats' => $this->getSystemStats(),
            'recent_users' => $this->getRecentUsers(),
            'recent_files' => $this->getRecentFiles(),
            'live_stats' => $this->getLiveRequestStats()
        ];

        return view('admin/dashboard', $data);
    }

    public function users()
    {
        if (!$this->isAdmin()) {
            return redirect()->to('/dashboard')->with('error', 'Access denied.');
        }

        $users = $this->userModel->getAllUsersWithStats();
        
        $data = [
            'title' => 'User Management',
            'users' => $users
        ];

        return view('admin/users', $data);
    }

    public function userDetails($userId)
    {
        if (!$this->isAdmin()) {
            return redirect()->to('/dashboard')->with('error', 'Access denied.');
        }

        $user = $this->userModel->find($userId);
        if (!$user) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('User not found');
        }

        $files = $this->jsonSiloModel->where('account_id', $userId)->findAll();
        $apiKeys = $this->apiKeyModel->where('user_id', $userId)->findAll();
        $stats = $this->getUserStats($userId);

        $data = [
            'title' => 'User Details - ' . $user['email'],
            'user' => $user,
            'files' => $files,
            'apiKeys' => $apiKeys,
            'stats' => $stats
        ];

        return view('admin/user_details', $data);
    }

    public function updateUserType()
    {
        if (!$this->isAdmin()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $userId = $this->request->getPost('user_id');
        $newType = $this->request->getPost('user_type');

        $limits = $this->getUserLimits($newType);
        
        $updated = $this->userModel->update($userId, [
            'user_type' => $newType,
            'max_files' => $limits['max_files'],
            'max_storage_mb' => $limits['max_storage_mb']
        ]);

        if ($updated) {
            return $this->response->setJSON([
                'success' => true, 
                'message' => "User type updated to {$newType}"
            ]);
        }

        return $this->response->setJSON([
            'success' => false, 
            'message' => 'Failed to update user type'
        ]);
    }

    public function deleteUser()
    {
        if (!$this->isAdmin()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $userId = $this->request->getPost('user_id');
        
        // Don't allow deleting admin accounts
        $user = $this->userModel->find($userId);
        if ($user['user_type'] === 'admin') {
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Cannot delete admin accounts'
            ]);
        }

        $deleted = $this->userModel->delete($userId);

        if ($deleted) {
            return $this->response->setJSON([
                'success' => true, 
                'message' => 'User deleted successfully'
            ]);
        }

        return $this->response->setJSON([
            'success' => false, 
            'message' => 'Failed to delete user'
        ]);
    }

    public function deleteUserFile()
    {
        if (!$this->isAdmin()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $fileId = $this->request->getPost('file_id');
        $deleted = $this->jsonSiloModel->delete($fileId);

        if ($deleted) {
            return $this->response->setJSON([
                'success' => true, 
                'message' => 'File deleted successfully'
            ]);
        }

        return $this->response->setJSON([
            'success' => false, 
            'message' => 'Failed to delete file'
        ]);
    }

    public function editUserFile()
    {
        if (!$this->isAdmin()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $fileId = $this->request->getPost('file_id');
        $filename = $this->request->getPost('filename');
        $content = $this->request->getPost('content');

        // Validate JSON
        json_decode($content);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Invalid JSON format'
            ]);
        }

        $updated = $this->jsonSiloModel->update($fileId, [
            'original_filename' => $filename,
            'json_content' => $content
        ]);

        if ($updated) {
            return $this->response->setJSON([
                'success' => true, 
                'message' => 'File updated successfully'
            ]);
        }

        return $this->response->setJSON([
            'success' => false, 
            'message' => 'Failed to update file'
        ]);
    }

    public function updateUserLimits()
    {
        if (!$this->isAdmin()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $userType = $this->request->getPost('user_type');
        $maxFiles = (int) $this->request->getPost('max_files');
        $maxStorageMb = (int) $this->request->getPost('max_storage_mb');

        // Validate inputs
        if (!in_array($userType, ['free', 'paid'])) {
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Invalid user type'
            ]);
        }

        if ($maxFiles < 1 || $maxStorageMb < 1) {
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Limits must be greater than 0'
            ]);
        }

        // Update all users of this type
        $updated = $this->userModel->where('user_type', $userType)->set([
            'max_files' => $maxFiles,
            'max_storage_mb' => $maxStorageMb
        ])->update();

        if ($updated !== false) {
            // Get count of affected users
            $affectedUsers = $this->userModel->where('user_type', $userType)->countAllResults();
            
            return $this->response->setJSON([
                'success' => true, 
                'message' => "Updated limits for {$affectedUsers} {$userType} users"
            ]);
        }

        return $this->response->setJSON([
            'success' => false, 
            'message' => 'Failed to update user limits'
        ]);
    }

    public function liveRequests()
    {
        if (!$this->isAdmin()) {
            return redirect()->to('/dashboard')->with('error', 'Access denied. Admin privileges required.');
        }

        $data = [
            'title' => 'Live Request Monitor',
            'current_user' => $this->getCurrentUser()
        ];

        return view('admin/live_requests', $data);
    }

    public function getLiveRequestData()
    {
        if (!$this->isAdmin()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        try {
            $db = \Config\Database::connect();
            
            // Get recent API requests (last 24 hours)
            $query = $db->table('api_request_logs')
                       ->select('*')
                       ->where('timestamp >', date('Y-m-d H:i:s', strtotime('-24 hours')))
                       ->orderBy('timestamp', 'DESC')
                       ->limit(100);
            
            $requests = $query->get()->getResultArray();
            
            return $this->response->setJSON([
                'success' => true,
                'requests' => $requests,
                'total_count' => count($requests)
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to fetch live request data',
                'error' => $e->getMessage()
            ]);
        }
    }

    protected function getSystemStats()
    {
        $totalUsers = $this->userModel->countAll();
        $totalFiles = $this->jsonSiloModel->countAll();
        $totalApiKeys = $this->apiKeyModel->countAll();
        
        // Count by user type
        $userTypes = [
            'free' => $this->userModel->where('user_type', 'free')->countAllResults(),
            'paid' => $this->userModel->where('user_type', 'paid')->countAllResults(),
            'admin' => $this->userModel->where('user_type', 'admin')->countAllResults()
        ];

        // Get current limits for each user type
        $freeLimits = $this->userModel->select('max_files, max_storage_mb')
                                     ->where('user_type', 'free')
                                     ->first();
        $paidLimits = $this->userModel->select('max_files, max_storage_mb')
                                     ->where('user_type', 'paid')
                                     ->first();

        return [
            'total_users' => $totalUsers,
            'total_files' => $totalFiles,
            'total_api_keys' => $totalApiKeys,
            'user_types' => $userTypes,
            'user_limits' => [
                'free' => $freeLimits ?: ['max_files' => 10, 'max_storage_mb' => 10],
                'paid' => $paidLimits ?: ['max_files' => 15, 'max_storage_mb' => 100]
            ]
        ];
    }

    protected function getRecentUsers($limit = 5)
    {
        return $this->userModel->orderBy('created_at', 'DESC')->limit($limit)->findAll();
    }

    protected function getRecentFiles($limit = 10)
    {
        return $this->jsonSiloModel
            ->select('user_json_files.*, accounts.email')
            ->join('accounts', 'accounts.user_id = user_json_files.account_id')
            ->orderBy('user_json_files.uploaded_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    protected function getUserStats($userId)
    {
        $fileCount = $this->jsonSiloModel->where('account_id', $userId)->countAllResults();
        $apiKeyCount = $this->apiKeyModel->where('user_id', $userId)->countAllResults();
        
        // Calculate storage usage
        $files = $this->jsonSiloModel->where('account_id', $userId)->findAll();
        $totalSize = 0;
        foreach ($files as $file) {
            $totalSize += strlen($file['json_content']);
        }
        $storageMB = round($totalSize / (1024 * 1024), 2);

        return [
            'file_count' => $fileCount,
            'api_key_count' => $apiKeyCount,
            'storage_mb' => $storageMB
        ];
    }

    protected function getUserLimits($userType)
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

    protected function getCurrentUser()
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return null;
        }
        return $this->userModel->find($userId);
    }

    protected function getLiveRequestStats()
    {
        try {
            $db = \Config\Database::connect();
            
            // Get stats for the last 24 hours
            $last24Hours = date('Y-m-d H:i:s', strtotime('-24 hours'));
            
            // Total requests in last 24 hours
            $totalRequests = $db->table('api_request_logs')
                               ->where('timestamp >', $last24Hours)
                               ->countAllResults();
            
            // Successful requests (HTTP 200)
            $successfulRequests = $db->table('api_request_logs')
                                    ->where('timestamp >', $last24Hours)
                                    ->where('status', 200)
                                    ->countAllResults();
            
            // Failed requests (non-200 status)
            $failedRequests = $totalRequests - $successfulRequests;
            
            // Top endpoints
            $topEndpoints = $db->table('api_request_logs')
                              ->select('endpoint, COUNT(*) as count')
                              ->where('timestamp >', $last24Hours)
                              ->groupBy('endpoint')
                              ->orderBy('count', 'DESC')
                              ->limit(5)
                              ->get()
                              ->getResultArray();
            
            return [
                'total_requests_24h' => $totalRequests,
                'successful_requests' => $successfulRequests,
                'failed_requests' => $failedRequests,
                'success_rate' => $totalRequests > 0 ? round(($successfulRequests / $totalRequests) * 100, 1) : 0,
                'top_endpoints' => $topEndpoints
            ];
            
        } catch (\Exception $e) {
            log_message('error', 'Failed to get live request stats: ' . $e->getMessage());
            return [
                'total_requests_24h' => 0,
                'successful_requests' => 0,
                'failed_requests' => 0,
                'success_rate' => 0,
                'top_endpoints' => []
            ];
        }
    }
}
