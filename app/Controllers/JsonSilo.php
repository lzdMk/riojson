<?php

namespace App\Controllers;

use App\Models\JsonSiloModel;

class JsonSilo extends BaseController
{
    protected $jsonSiloModel;
    
    public function __construct()
    {
        $this->jsonSiloModel = new JsonSiloModel();
    }
    
    /**
     * Show dashboard with user's JSON silos
     */
    public function index()
    {
        if (!$this->isLoggedIn()) {
            return redirect()->to('/signin');
        }
        
        $userId = session()->get('user_id');
        $userEmail = session()->get('email');
        $jsonFiles = $this->jsonSiloModel->getUserFiles($userId);
        $storageUsed = $this->jsonSiloModel->getUserStorageUsed($userId);
        
        // Add formatted file sizes to each file
        foreach ($jsonFiles as &$file) {
            $file['formatted_size'] = $this->formatBytes(strlen($file['json_content']));
        }
        
        // Get user info to check admin status
        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($userId);
        $isAdmin = $user && $user['user_type'] === 'admin';
        
        $data = array_merge($this->getCommonViewData(), [
            'title' => 'JSON Silos Dashboard',
            'jsonFiles' => $jsonFiles,
            'totalFiles' => count($jsonFiles),
            'storage_used' => $storageUsed,
            'user_email' => $userEmail,
            'user' => $user
        ]);
        
        // If admin, add admin stats
        if ($isAdmin) {
            $data['admin_stats'] = $this->getAdminStats();
        }
        
        return view('dashboard/silos', $data);
    }
    
    /**
     * Show create new silo page
     */
    public function create()
    {
        if (!$this->isLoggedIn()) {
            return redirect()->to('/signin');
        }
        
        $data = array_merge($this->getCommonViewData(), [
            'title' => 'Create New JSON Silo',
            'user_email' => session()->get('email')
        ]);
        
        return view('dashboard/create_silo', $data);
    }
    
    /**
     * Handle file upload
     */
    public function upload()
    {
        if (!$this->isLoggedIn()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Not logged in'
            ]);
        }
        
        try {
            $userId = session()->get('user_id');
            $uploadedFile = $this->request->getFile('json_file');
            $filename = $this->request->getPost('filename');
            
            if (!$uploadedFile || !$uploadedFile->isValid()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'No valid file uploaded'
                ]);
            }
            
            // Read file content
            $jsonContent = file_get_contents($uploadedFile->getTempName());
            
            // Validate JSON
            if (!$this->jsonSiloModel->isValidJson($jsonContent)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Invalid JSON format'
                ]);
            }
            
            // Create silo
            $siloId = $this->jsonSiloModel->createSilo(
                $userId, 
                $filename ?: $uploadedFile->getName(), 
                $jsonContent
            );
            
            if ($siloId) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'JSON silo created successfully',
                    'silo_id' => $siloId
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to create JSON silo'
                ]);
            }
            
        } catch (\Exception $e) {
            log_message('error', 'JSON upload error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Handle manual JSON creation
     */
    public function save()
    {
        if (!$this->isLoggedIn()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Not logged in'
            ]);
        }
        
        try {
            $userId = session()->get('user_id');
            $filename = $this->request->getPost('filename');
            $jsonContent = $this->request->getPost('json_content');
            
            if (empty($filename) || empty($jsonContent)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Filename and JSON content are required'
                ]);
            }
            
            // Validate JSON
            if (!$this->jsonSiloModel->isValidJson($jsonContent)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Invalid JSON format'
                ]);
            }
            
            // Create silo
            $siloId = $this->jsonSiloModel->createSilo($userId, $filename, $jsonContent);
            
            if ($siloId) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'JSON silo created successfully',
                    'silo_id' => $siloId
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to create JSON silo'
                ]);
            }
            
        } catch (\Exception $e) {
            log_message('error', 'JSON save error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * View JSON silo
     */
    public function view($siloId)
    {
        if (!$this->isLoggedIn()) {
            return redirect()->to('/signin');
        }
        
        $userId = session()->get('user_id');
        $silo = $this->jsonSiloModel->getUserFile($siloId, $userId);
        
        if (!$silo) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('JSON silo not found');
        }
        
        // Get user info for badges
        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($userId);
        
        $data = [
            'title' => 'View JSON - ' . $silo['original_filename'],
            'silo' => $silo,
            'jsonFormatted' => $this->jsonSiloModel->formatJson($silo['json_content']),
            'formatted_size' => $this->formatBytes(strlen($silo['json_content'])),
            'user_email' => session()->get('email'),
            'user_type' => $user['user_type'] ?? 'free',
            'is_admin' => ($user['user_type'] ?? 'free') === 'admin'
        ];
        
        return view('dashboard/silos/view', $data);
    }
    
    /**
     * Edit JSON silo
     */
    public function edit($siloId)
    {
        if (!$this->isLoggedIn()) {
            return redirect()->to('/signin');
        }
        
        $userId = session()->get('user_id');
        $silo = $this->jsonSiloModel->getUserFile($siloId, $userId);
        
        if (!$silo) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('JSON silo not found');
        }
        
        // Get user info for badges
        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($userId);
        
        $data = [
            'title' => 'Edit JSON - ' . $silo['original_filename'],
            'silo' => $silo,
            'jsonFormatted' => $this->jsonSiloModel->formatJson($silo['json_content']),
            'formatted_size' => $this->formatBytes(strlen($silo['json_content'])),
            'user_email' => session()->get('email'),
            'user_type' => $user['user_type'] ?? 'free',
            'is_admin' => ($user['user_type'] ?? 'free') === 'admin'
        ];
        
        return view('dashboard/silos/edit', $data);
    }
    
    /**
     * Update JSON silo
     */
    public function update($siloId)
    {
        if (!$this->isLoggedIn()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Not logged in'
            ]);
        }
        
        try {
            $userId = session()->get('user_id');
            $jsonContent = $this->request->getPost('json_content');
            
            if (empty($jsonContent)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'JSON content is required'
                ]);
            }
            
            // Validate JSON
            if (!$this->jsonSiloModel->isValidJson($jsonContent)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Invalid JSON format'
                ]);
            }
            
            // Update silo
            $updated = $this->jsonSiloModel->updateSilo($siloId, $userId, $jsonContent);
            
            if ($updated) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'JSON silo updated successfully'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to update JSON silo'
                ]);
            }
            
        } catch (\Exception $e) {
            log_message('error', 'JSON update error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Delete JSON silo
     */
    public function delete($siloId)
    {
        if (!$this->isLoggedIn()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Not logged in'
            ]);
        }
        
        try {
            $userId = session()->get('user_id');
            $deleted = $this->jsonSiloModel->deleteSilo($siloId, $userId);
            
            if ($deleted) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'JSON silo deleted successfully'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to delete JSON silo'
                ]);
            }
            
        } catch (\Exception $e) {
            log_message('error', 'JSON delete error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Download JSON silo as file
     */
    public function download($siloId)
    {
        if (!$this->isLoggedIn()) {
            return redirect()->to('/signin');
        }
        
        $userId = session()->get('user_id');
        $silo = $this->jsonSiloModel->getUserFile($siloId, $userId);
        
        if (!$silo) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('JSON silo not found');
        }
        
        // Set download headers
        $filename = $silo['original_filename'] ?: 'silo-' . $siloId . '.json';
        
        return $this->response
            ->setHeader('Content-Type', 'application/json')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($silo['json_content']);
    }

    /**
     * Get admin statistics for admin users
     */
    private function getAdminStats()
    {
        $userModel = new \App\Models\UserModel();
        $apiKeyModel = new \App\Models\ApiKeyModel();
        
        $totalUsers = $userModel->countAll();
        $totalFiles = $this->jsonSiloModel->countAll();
        $totalApiKeys = $apiKeyModel->countAll();
        
        // Count by user type
        $userTypes = [
            'free' => $userModel->where('user_type', 'free')->countAllResults(),
            'paid' => $userModel->where('user_type', 'paid')->countAllResults(),
            'admin' => $userModel->where('user_type', 'admin')->countAllResults()
        ];
        
        $recentUsers = $userModel->orderBy('created_at', 'DESC')->limit(5)->findAll();
        
        return [
            'total_users' => $totalUsers,
            'total_files' => $totalFiles,
            'total_api_keys' => $totalApiKeys,
            'user_types' => $userTypes,
            'recent_users' => $recentUsers
        ];
    }
    
    /**
     * Format bytes into human readable format (starting with MB)
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $mb = $bytes / (1024 * 1024);
        
        // Format with cleaner decimal places - always show MB
        if ($mb >= 10) {
            return round($mb, 1) . ' MB';  // 10.5 MB, 15.2 MB
        } else {
            return round($mb, 2) . ' MB';  // 0.61 MB, 5.43 MB
        }
    }
}
