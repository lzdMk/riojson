<?php

namespace App\Controllers;

use App\Models\UserModel;

class Settings extends BaseController
{
    protected $userModel;
    
    public function __construct()
    {
        $this->userModel = new UserModel();
    }
    
    /**
     * Show account settings page
     */
    public function index()
    {
        if (!$this->isLoggedIn()) {
            return redirect()->to('/signin');
        }
        
        $userId = session()->get('user_id');
        $user = $this->userModel->find($userId);
        
        if (!$user) {
            session()->destroy();
            return redirect()->to('/signin')->with('error', 'User not found');
        }
        
        $data = array_merge($this->getCommonViewData(), [
            'title' => 'Account Settings',
            'user_email' => $user['email'],
            'user' => $user
        ]);
        
        return view('dashboard/settings', $data);
    }
    
    /**
     * Change user password
     */
    public function changePassword()
    {
        if (!$this->isLoggedIn()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'User not logged in'
            ]);
        }
        
        $userId = session()->get('user_id');
        $currentPassword = $this->request->getPost('current_password');
        $newPassword = $this->request->getPost('new_password');
        $confirmPassword = $this->request->getPost('confirm_password');
        
        // Validate inputs
        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'All password fields are required'
            ]);
        }
        
        if (strlen($newPassword) < 8) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'New password must be at least 8 characters long'
            ]);
        }
        
        if ($newPassword !== $confirmPassword) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'New passwords do not match'
            ]);
        }
        
        // Get user and verify current password
        $user = $this->userModel->find($userId);
        if (!$user || !password_verify($currentPassword, $user['password_hash'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Current password is incorrect'
            ]);
        }
        
        // Update password
        $success = $this->userModel->update($userId, [
            'password_hash' => password_hash($newPassword, PASSWORD_DEFAULT)
        ]);
        
        if ($success) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Password changed successfully!'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update password. Please try again.'
            ]);
        }
    }
    
    /**
     * Change user email address
     */
    public function changeEmail()
    {
        if (!$this->isLoggedIn()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'User not logged in'
            ]);
        }
        
        $userId = session()->get('user_id');
        $newEmail = $this->request->getPost('new_email');
        $confirmEmail = $this->request->getPost('confirm_email');
        $password = $this->request->getPost('password');
        
        // Validate inputs
        if (empty($newEmail) || empty($confirmEmail) || empty($password)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'All fields are required'
            ]);
        }
        
        if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please enter a valid email address'
            ]);
        }
        
        if ($newEmail !== $confirmEmail) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Email addresses do not match'
            ]);
        }
        
        // Get user and verify password
        $user = $this->userModel->find($userId);
        if (!$user || !password_verify($password, $user['password_hash'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Password is incorrect'
            ]);
        }
        
        // Check if new email is already in use
        if ($this->userModel->emailExists($newEmail)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'This email address is already in use'
            ]);
        }
        
        // Update email
        $success = $this->userModel->update($userId, [
            'email' => $newEmail
        ]);
        
        if ($success) {
            // Update session email
            session()->set('email', $newEmail);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Email address changed successfully!'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update email address. Please try again.'
            ]);
        }
    }
    
    /**
     * Delete user account (requires double confirmation)
     */
    public function deleteAccount()
    {
        if (!$this->isLoggedIn()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'User not logged in'
            ]);
        }
        
        $userId = session()->get('user_id');
        $password = $this->request->getPost('password');
        $confirmText = $this->request->getPost('confirm_text');
        
        // Validate inputs
        if (empty($password) || empty($confirmText)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'All fields are required'
            ]);
        }
        
        // Check confirmation text
        if (strtolower(trim($confirmText)) !== 'delete my account forever') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please type exactly: "delete my account forever"'
            ]);
        }
        
        // Get user and verify password
        $user = $this->userModel->find($userId);
        if (!$user || !password_verify($password, $user['password_hash'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Password is incorrect'
            ]);
        }
        
        try {
            // Delete user account (this will also delete JSON files due to foreign key cascade)
            $success = $this->userModel->delete($userId);
            
            if ($success) {
                // Destroy session
                session()->destroy();
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Account deleted successfully. You will be redirected to the home page.',
                    'redirect' => base_url('/')
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to delete account. Please try again.'
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred while deleting your account. Please try again.'
            ]);
        }
    }
    
    /**
     * Get account statistics
     */
    public function getStats()
    {
        if (!$this->isLoggedIn()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'User not logged in'
            ]);
        }
        
        $userId = session()->get('user_id');
        
        // Load JsonSiloModel for statistics
        $jsonSiloModel = new \App\Models\JsonSiloModel();
        
        $stats = [
            'total_files' => $jsonSiloModel->where('account_id', $userId)->countAllResults(),
            'storage_used' => $this->getStorageUsed($userId),
            'account_created' => $this->userModel->find($userId)['created_at']
        ];
        
        return $this->response->setJSON([
            'success' => true,
            'stats' => $stats
        ]);
    }
    
    /**
     * Check if user is logged in
     */
    public function isLoggedIn()
    {
        return session()->get('logged_in') === true;
    }
    
    /**
     * Format bytes to MB, GB, TB format with clean decimal places
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $mb = $bytes / (1024 * 1024);
        
        if ($mb >= 1024) {
            $gb = $mb / 1024;
            if ($gb >= 1024) {
                $tb = $gb / 1024;
                return round($tb, $precision) . ' TB';
            }
            return round($gb, $precision) . ' GB';
        }
        
        // Format with cleaner decimal places for MB
        if ($mb >= 10) {
            return round($mb, 1) . ' MB';
        } else {
            return round($mb, 2) . ' MB';
        }
    }
    
    /**
     * Get storage used by user in formatted string
     */
    private function getStorageUsed($userId)
    {
        $jsonSiloModel = new \App\Models\JsonSiloModel();
        $files = $jsonSiloModel->where('account_id', $userId)->findAll();
        
        $totalSize = 0;
        foreach ($files as $file) {
            $totalSize += strlen($file['json_content']);
        }
        
        return $this->formatBytes($totalSize);
    }
}
