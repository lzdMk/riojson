<?php

namespace App\Models;

use CodeIgniter\Model;

class BackupModel extends Model
{
    protected $db;
    
    // Expected table structure for validation
    protected $expectedTables = [
        'accounts' => ['user_id', 'email', 'password_hash', 'user_type', 'max_files', 'max_storage_mb', 'last_login_at', 'is_active', 'created_at'],
        'api_keys' => ['id', 'user_id', 'api_key', 'domain_lock', 'is_active', 'created_at'],
        'user_json_files' => ['id', 'account_id', 'file_id', 'original_filename', 'json_content', 'uploaded_at'],
        'api_request_logs' => ['id', 'user_id', 'user_email', 'user_type', 'endpoint', 'method', 'status', 'response_time', 'ip', 'rate_limit_hit', 'timestamp']
    ];

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * Generate complete SQL backup of the database
     */
    public function generateSqlBackup()
    {
        $sql = '';
        $sql .= "-- RioConsoleJSON Database Backup\n";
        $sql .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
        $sql .= "-- Database: " . $this->db->getDatabase() . "\n\n";
        
        $sql .= "SET FOREIGN_KEY_CHECKS = 0;\n";
        $sql .= "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n";
        $sql .= "SET time_zone = '+00:00';\n\n";
        
        // Get all tables
        $tables = $this->getAllTables();
        
        foreach ($tables as $tableName) {
            $sql .= $this->generateTableBackup($tableName);
            $sql .= "\n";
        }
        
        $sql .= "SET FOREIGN_KEY_CHECKS = 1;\n";
        
        return $sql;
    }

    /**
     * Generate backup for a specific table
     */
    private function generateTableBackup($tableName)
    {
        $sql = "-- Table: {$tableName}\n";
        $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
        
        // Get CREATE TABLE statement
        $createQuery = $this->db->query("SHOW CREATE TABLE `{$tableName}`");
        $createResult = $createQuery->getRowArray();
        $sql .= $createResult['Create Table'] . ";\n\n";
        
        // Get table data
        $sql .= "-- Data for table `{$tableName}`\n";
        $dataQuery = $this->db->query("SELECT * FROM `{$tableName}`");
        $rows = $dataQuery->getResultArray();
        
        if (!empty($rows)) {
            $columns = array_keys($rows[0]);
            $columnList = '`' . implode('`, `', $columns) . '`';
            
            $sql .= "INSERT INTO `{$tableName}` ({$columnList}) VALUES\n";
            
            $valueStrings = [];
            foreach ($rows as $row) {
                $values = [];
                foreach ($row as $value) {
                    if ($value === null) {
                        $values[] = 'NULL';
                    } else {
                        // Properly escape the value
                        $escaped = str_replace(['\\', "'", "\n", "\r"], ['\\\\', "\\'", "\\n", "\\r"], $value);
                        $values[] = "'{$escaped}'";
                    }
                }
                $valueStrings[] = '(' . implode(', ', $values) . ')';
            }
            
            $sql .= implode(",\n", $valueStrings) . ";\n\n";
        } else {
            $sql .= "-- No data in table `{$tableName}`\n\n";
        }
        
        return $sql;
    }

    /**
     * Get all table names from database
     */
    public function getAllTables()
    {
        $query = $this->db->query("SHOW TABLES");
        $tables = [];
        
        foreach ($query->getResultArray() as $row) {
            $tables[] = array_values($row)[0];
        }
        
        return $tables;
    }

    /**
     * Validate SQL structure before import
     */
    public function validateSqlStructure($sqlContent)
    {
        try {
            // Check if it contains our expected tables
            $foundTables = [];
            
            foreach ($this->expectedTables as $tableName => $expectedColumns) {
                if (strpos($sqlContent, "CREATE TABLE `{$tableName}`") !== false || 
                    strpos($sqlContent, "CREATE TABLE {$tableName}") !== false) {
                    $foundTables[] = $tableName;
                }
            }
            
            // Check if we found at least the core tables
            $coreTablesNeeded = ['accounts', 'user_json_files'];
            $hasCoreTables = true;
            
            foreach ($coreTablesNeeded as $coreTable) {
                if (!in_array($coreTable, $foundTables)) {
                    $hasCoreTables = false;
                    break;
                }
            }
            
            if (!$hasCoreTables) {
                return [
                    'valid' => false,
                    'error' => 'SQL file does not contain required core tables: ' . implode(', ', $coreTablesNeeded)
                ];
            }
            
            // Additional validation: check for dangerous commands
            $dangerousCommands = ['DROP DATABASE', 'DROP SCHEMA', 'TRUNCATE'];
            foreach ($dangerousCommands as $command) {
                if (stripos($sqlContent, $command) !== false) {
                    return [
                        'valid' => false,
                        'error' => "SQL contains dangerous command: {$command}"
                    ];
                }
            }
            
            return [
                'valid' => true,
                'tables_found' => $foundTables
            ];
            
        } catch (\Exception $e) {
            return [
                'valid' => false,
                'error' => 'Validation error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Import SQL backup to database
     */
    public function importSqlBackup($sqlContent)
    {
        try {
            // Split SQL into individual statements
            $statements = $this->splitSqlStatements($sqlContent);
            
            $executed = 0;
            $errors = [];
            
            // Execute each statement
            foreach ($statements as $statement) {
                $statement = trim($statement);
                
                // Skip empty statements and comments
                if (empty($statement) || strpos($statement, '--') === 0) {
                    continue;
                }
                
                try {
                    $this->db->query($statement);
                    $executed++;
                } catch (\Exception $e) {
                    $errors[] = [
                        'statement' => substr($statement, 0, 100) . '...',
                        'error' => $e->getMessage()
                    ];
                }
            }
            
            return [
                'success' => true,
                'message' => "Import completed successfully",
                'executed_statements' => $executed,
                'total_statements' => count($statements),
                'errors' => $errors
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Import failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Split SQL content into individual statements
     */
    private function splitSqlStatements($sql)
    {
        // Remove comments
        $sql = preg_replace('/--.*$/m', '', $sql);
        
        // Split by semicolon
        $statements = explode(';', $sql);
        
        // Filter out empty statements
        return array_filter($statements, function($stmt) {
            return !empty(trim($stmt));
        });
    }

    /**
     * Get database statistics
     */
    public function getDatabaseStats()
    {
        try {
            $tables = $this->getAllTables();
            $stats = [
                'database_name' => $this->db->getDatabase(),
                'total_tables' => count($tables),
                'total_rows' => 0,
                'total_size_mb' => 0,
                'tables' => []
            ];
            
            foreach ($tables as $tableName) {
                // Get row count
                $rowQuery = $this->db->query("SELECT COUNT(*) as count FROM `{$tableName}`");
                $rowCount = $rowQuery->getRow()->count;
                
                // Get table size
                $sizeQuery = $this->db->query("
                    SELECT ROUND(((data_length + index_length) / 1024 / 1024), 2) AS size_mb 
                    FROM information_schema.TABLES 
                    WHERE table_schema = ? AND table_name = ?
                ", [$this->db->getDatabase(), $tableName]);
                
                $sizeResult = $sizeQuery->getRow();
                $sizeMB = $sizeResult ? (float)$sizeResult->size_mb : 0;
                
                $stats['tables'][] = [
                    'name' => $tableName,
                    'rows' => (int)$rowCount,
                    'size_mb' => $sizeMB
                ];
                
                $stats['total_rows'] += $rowCount;
                $stats['total_size_mb'] += $sizeMB;
            }
            
            $stats['total_size_mb'] = round($stats['total_size_mb'], 2);
            
            return $stats;
            
        } catch (\Exception $e) {
            return [
                'database_name' => 'unknown',
                'total_tables' => 0,
                'total_rows' => 0,
                'total_size_mb' => 0,
                'tables' => [],
                'error' => $e->getMessage()
            ];
        }
    }
}
