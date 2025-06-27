<?php

namespace App\Models;

use CodeIgniter\Model;

class JsonSiloModel extends Model
{
    protected $table = 'user_json_files';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'id',
        'account_id', // Updated column name
        'original_filename',
        'json_content',
        'uploaded_at'
    ];

    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    protected $createdField = 'uploaded_at';
    protected $updatedField = '';
    protected $deletedField = '';

    protected $validationRules = [
        'account_id' => 'required|max_length[6]', // Updated column name
        'original_filename' => 'permit_empty|max_length[255]',
        'json_content' => 'required'
    ];

    protected $validationMessages = [
        'account_id' => [
            'required' => 'Account ID is required',
            'max_length' => 'Account ID must not exceed 6 characters'
        ],
        'json_content' => [
            'required' => 'JSON content is required'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Before insert: generate unique ID and validate JSON
     */
    protected $beforeInsert = ['generateUniqueId', 'validateJson'];
    protected $beforeUpdate = ['validateJson'];

    protected function generateUniqueId(array $data)
    {
        if (!isset($data['data']['id'])) {
            $data['data']['id'] = $this->createUniqueId();
        }
        return $data;
    }

    protected function validateJson(array $data)
    {
        if (isset($data['data']['json_content'])) {
            json_decode($data['data']['json_content'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \RuntimeException('Invalid JSON: ' . json_last_error_msg());
            }
        }
        return $data;
    }

    private function createUniqueId()
    {
        do {
            $id = sprintf('%03d-%03d-%03d', rand(100, 999), rand(100, 999), rand(100, 999));
            $exists = $this->find($id);
        } while ($exists);
        return $id;
    }

    public function getUserFiles($userId)
    {
        return $this->where('account_id', $userId) // Updated column name
                    ->orderBy('uploaded_at', 'DESC')
                    ->findAll();
    }

    public function getUserFile($fileId, $userId)
    {
        return $this->where('id', $fileId)
                    ->where('account_id', $userId) // Updated column name
                    ->first();
    }

    public function createSilo($userId, $filename, $jsonContent)
    {
        $data = [
            'account_id' => $userId, // Updated column name
            'original_filename' => $filename,
            'json_content' => $jsonContent,
            'uploaded_at' => date('Y-m-d H:i:s')
        ];
        if ($this->insert($data)) {
            return $this->getInsertID();
        }
        return false;
    }

    public function updateSilo($fileId, $userId, $jsonContent)
    {
        return $this->where('id', $fileId)
                    ->where('account_id', $userId) // Updated column name
                    ->set('json_content', $jsonContent)
                    ->update();
    }

    public function deleteSilo($fileId, $userId)
    {
        return $this->where('id', $fileId)
                    ->where('account_id', $userId) // Updated column name
                    ->delete();
    }

    public function isValidJson($jsonString)
    {
        json_decode($jsonString);
        return json_last_error() === JSON_ERROR_NONE;
    }

    public function formatJson($jsonString)
    {
        $decoded = json_decode($jsonString, true);
        return (json_last_error() === JSON_ERROR_NONE)
            ? json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
            : $jsonString;
    }

    /**
     * Calculate total storage used by user's JSON files
     */
    public function getUserStorageUsed($userId)
    {
        $files = $this->getUserFiles($userId);
        $totalBytes = 0;
        
        foreach ($files as $file) {
            if (isset($file['json_content'])) {
                $totalBytes += strlen($file['json_content']);
            }
        }
        
        return $this->formatBytes($totalBytes);
    }

    /**
     * Format bytes into MB with clean decimal places
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $mb = $bytes / (1024 * 1024);
        
        // Format with cleaner decimal places
        if ($mb >= 10) {
            return round($mb, 1) . ' MB';
        } else {
            return round($mb, 2) . ' MB';
        }
    }
}
