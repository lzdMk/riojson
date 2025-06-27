<?php
/**
 * Database Structure Scanner
 * This script scans the current database and generates the structure information
 */

// Load environment variables
if (file_exists('.env')) {
    $envContent = file_get_contents('.env');
    $lines = explode("\n", $envContent);
    foreach ($lines as $line) {
        if (trim($line) && strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
        }
    }
}

// Database configuration
$hostname = $_ENV['database.default.hostname'] ?? 'localhost';
$database = $_ENV['database.default.database'] ?? 'riojson_data';
$username = $_ENV['database.default.username'] ?? 'root';
$password = $_ENV['database.default.password'] ?? '';

try {
    // Connect to database using MySQLi
    $mysqli = new mysqli($hostname, $username, $password, $database);
    
    // Check connection
    if ($mysqli->connect_error) {
        throw new Exception("Connection failed: " . $mysqli->connect_error);
    }
    
    echo "=== DATABASE STRUCTURE SCANNER ===\n";
    echo "Database: $database\n";
    echo "Host: $hostname\n";
    echo "Scanning started at: " . date('Y-m-d H:i:s') . "\n\n";
    
    // Get all tables
    $result = $mysqli->query("SHOW TABLES");
    $tables = [];
    while ($row = $result->fetch_array()) {
        $tables[] = $row[0];
    }
    
    echo "Found " . count($tables) . " tables:\n";
    foreach ($tables as $table) {
        echo "- $table\n";
    }
    echo "\n";
    
    $sqlOutput = [];
    $sqlOutput[] = "-- RioConsoleJSON Database Setup";
    $sqlOutput[] = "-- Generated: " . date('Y-m-d H:i:s');
    $sqlOutput[] = "-- Database: $database";
    $sqlOutput[] = "";
    $sqlOutput[] = "SET FOREIGN_KEY_CHECKS = 0;";
    $sqlOutput[] = "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';";
    $sqlOutput[] = "SET time_zone = '+00:00';";
    $sqlOutput[] = "";
    
    // For each table, get structure
    foreach ($tables as $table) {
        echo "=== TABLE: $table ===\n";
        
        // Get CREATE TABLE statement
        $result = $mysqli->query("SHOW CREATE TABLE `$table`");
        $createTable = $result->fetch_assoc();
        
        echo "Structure:\n";
        echo $createTable['Create Table'] . "\n\n";
        
        // Get column information
        $result = $mysqli->query("DESCRIBE `$table`");
        $columns = [];
        while ($row = $result->fetch_assoc()) {
            $columns[] = $row;
        }
        
        echo "Columns:\n";
        foreach ($columns as $column) {
            echo sprintf("  %-20s %-20s %-10s %-10s %-10s %s\n", 
                $column['Field'], 
                $column['Type'], 
                $column['Null'], 
                $column['Key'], 
                $column['Default'] ?? 'NULL', 
                $column['Extra']
            );
        }
        
        // Get row count
        $result = $mysqli->query("SELECT COUNT(*) as count FROM `$table`");
        $count = $result->fetch_assoc()['count'];
        echo "Row count: $count\n";
        
        // Get sample data (first 3 rows)
        if ($count > 0) {
            echo "Sample data (first 3 rows):\n";
            $result = $mysqli->query("SELECT * FROM `$table` LIMIT 3");
            $samples = [];
            while ($row = $result->fetch_assoc()) {
                $samples[] = $row;
            }
            
            if (!empty($samples)) {
                // Print column headers
                $headers = array_keys($samples[0]);
                echo "  " . implode(" | ", array_map(function($h) { return str_pad($h, 15); }, $headers)) . "\n";
                echo "  " . str_repeat("-", count($headers) * 17) . "\n";
                
                // Print data rows
                foreach ($samples as $row) {
                    $values = array_map(function($v) { 
                        return str_pad(substr($v ?? 'NULL', 0, 15), 15); 
                    }, array_values($row));
                    echo "  " . implode(" | ", $values) . "\n";
                }
            }
        }
        
        echo "\n" . str_repeat("-", 80) . "\n\n";
        
        // Add to SQL output
        $sqlOutput[] = "-- Table: $table";
        $sqlOutput[] = "DROP TABLE IF EXISTS `$table`;";
        $sqlOutput[] = $createTable['Create Table'] . ";";
        $sqlOutput[] = "";
    }
    
    $sqlOutput[] = "SET FOREIGN_KEY_CHECKS = 1;";
    
    // Write SQL to file
    $sqlFile = 'database_structure_scan.sql';
    file_put_contents($sqlFile, implode("\n", $sqlOutput));
    
    echo "=== SUMMARY ===\n";
    echo "Total tables: " . count($tables) . "\n";
    echo "SQL structure saved to: $sqlFile\n";
    echo "Scan completed at: " . date('Y-m-d H:i:s') . "\n";
    
    $mysqli->close();
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
