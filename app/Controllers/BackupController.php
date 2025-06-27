<?php

namespace App\Controllers;

use App\Models\BackupModel;

class BackupController extends BaseController
{
    protected $backupModel;

    public function __construct()
    {
        $this->backupModel = new BackupModel();
    }

    /**
     * Display backup management interface
     */
    public function index()
    {
        // Check if user is admin
        if (!$this->isAdmin()) {
            return redirect()->to('/dashboard')->with('error', 'Access denied. Admin privileges required.');
        }

        // Get database statistics
        $stats = $this->backupModel->getDatabaseStats();

        $data = [
            'title' => 'System Backup & Import',
            'stats' => $stats
        ];

        return view('backup/index', $data);
    }

    /**
     * Download complete database backup
     */
    public function download()
    {
        // Check if user is admin
        if (!$this->isAdmin()) {
            return redirect()->to('/dashboard')->with('error', 'Access denied.');
        }

        try {
            // Generate SQL backup
            $sqlContent = $this->backupModel->generateSqlBackup();
            
            // Create filename with timestamp
            $timestamp = date('Y-m-d_H-i-s');
            $filename = "riojson_backup_{$timestamp}.sql";
            
            // Force download
            return $this->response
                ->setHeader('Content-Type', 'application/octet-stream')
                ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
                ->setHeader('Content-Length', strlen($sqlContent))
                ->setBody($sqlContent);
                
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to generate backup: ' . $e->getMessage());
        }
    }

    /**
     * Import database from uploaded SQL file
     */
    public function import()
    {
        // Check if user is admin
        if (!$this->isAdmin()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $file = $this->request->getFile('sql_file');
        
        if (!$file || !$file->isValid()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No valid file uploaded'
            ]);
        }

        // Validate file
        if ($file->getExtension() !== 'sql') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Only .sql files are allowed'
            ]);
        }

        // Check file size (max 100MB)
        $fileSizeMB = $file->getSize() / (1024 * 1024);
        if ($fileSizeMB > 100) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'File too large. Maximum size is 100MB'
            ]);
        }

        try {
            // Read SQL content
            $sqlContent = file_get_contents($file->getTempName());
            
            // Validate SQL structure first
            $validation = $this->backupModel->validateSqlStructure($sqlContent);
            
            if (!$validation['valid']) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'SQL validation failed: ' . $validation['error']
                ]);
            }

            // Import the database
            $result = $this->backupModel->importSqlBackup($sqlContent);
            
            return $this->response->setJSON($result);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Import failed: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get current database statistics (AJAX)
     */
    public function stats()
    {
        // Check if user is admin
        if (!$this->isAdmin()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        try {
            $stats = $this->backupModel->getDatabaseStats();
            
            return $this->response->setJSON([
                'success' => true,
                'stats' => $stats
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to get stats: ' . $e->getMessage()
            ]);
        }
    }
}
