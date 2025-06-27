-- RioConsoleJSON Database Setup
-- Updated: 2025-06-27 (Generated from live database scan)
-- Database: riojson_data

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';
SET time_zone = '+00:00';

-- Create database (if not exists)
CREATE DATABASE IF NOT EXISTS `riojson_data` 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_general_ci;

-- Use the database
USE `riojson_data`;

-- ===============================================
-- Table: accounts - User account management
-- ===============================================
DROP TABLE IF EXISTS `accounts`;
CREATE TABLE `accounts` (
  `user_id` varchar(6) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `user_type` enum('free','paid','admin') NOT NULL DEFAULT 'free',
  `max_files` int(11) NOT NULL DEFAULT 10 COMMENT 'Maximum files allowed',
  `max_storage_mb` int(11) NOT NULL DEFAULT 50 COMMENT 'Maximum storage in MB',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`),
  KEY `user_type` (`user_type`),
  KEY `is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ===============================================
-- Table: api_keys - API key management
-- ===============================================
DROP TABLE IF EXISTS `api_keys`;
CREATE TABLE `api_keys` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(6) NOT NULL,
  `key_name` varchar(255) NOT NULL,
  `api_key` varchar(64) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_used_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `allowed_domains` text DEFAULT NULL COMMENT 'JSON array of allowed domains, NULL means no restriction',
  `domain_lock_enabled` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Whether domain lock is enabled for this key',
  PRIMARY KEY (`id`),
  UNIQUE KEY `api_key` (`api_key`),
  KEY `user_id` (`user_id`),
  KEY `is_active` (`is_active`),
  KEY `idx_domain_lock` (`domain_lock_enabled`),
  CONSTRAINT `api_keys_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `accounts` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ===============================================
-- Table: api_request_logs - API request monitoring
-- ===============================================
DROP TABLE IF EXISTS `api_request_logs`;
CREATE TABLE `api_request_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(6) DEFAULT NULL,
  `user_email` varchar(255) DEFAULT NULL,
  `user_type` enum('free','paid','admin') DEFAULT NULL,
  `endpoint` varchar(255) DEFAULT NULL,
  `method` varchar(10) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `response_time` varchar(20) DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `rate_limit_hit` tinyint(1) DEFAULT 0,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_timestamp` (`timestamp`),
  KEY `idx_user_type` (`user_type`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ===============================================
-- Table: user_json_files - JSON file storage
-- ===============================================
DROP TABLE IF EXISTS `user_json_files`;
CREATE TABLE `user_json_files` (
  `id` varchar(11) NOT NULL,
  `account_id` varchar(6) NOT NULL,
  `original_filename` varchar(255) DEFAULT NULL,
  `json_content` longtext NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`account_id`),
  CONSTRAINT `user_json_files_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ===============================================
-- Table: migrations - CodeIgniter migration tracking
-- ===============================================
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- ===============================================
-- DATABASE DESCRIPTION
-- ===============================================
/*
This database schema supports:

1. USER MANAGEMENT (accounts table):
   - 6-character alphanumeric user IDs
   - User types: free, paid, admin
   - Configurable file and storage limits
   - Account status tracking

2. JSON FILE STORAGE (user_json_files table):
   - 11-character file IDs (XXX-XXX-XXX format)
   - LONGTEXT storage for JSON content
   - Original filename preservation
   - Upload timestamp tracking

3. API SECURITY (api_keys table):
   - Named API keys for organization
   - Domain lock restrictions
   - Usage tracking and key management
   - User-based key association

4. REQUEST MONITORING (api_request_logs table):
   - Real-time API request logging
   - Performance monitoring
   - Rate limiting tracking
   - User activity analysis

5. FRAMEWORK SUPPORT (migrations table):
   - CodeIgniter 4 migration tracking
   - Database versioning support
*/

-- ===============================================
-- SAMPLE DATA (Optional - for development)
-- ===============================================
-- Uncomment the section below to insert sample data

/*
-- Insert sample admin user (password: admin123)
INSERT INTO `accounts` (`user_id`, `email`, `password_hash`, `user_type`, `max_files`, `max_storage_mb`) VALUES
('ADMIN1', 'admin@example.com', '$2y$12$Sq5UR1TDGfj.v8pYx4iYSek6vBz5nF3Ht1xGdP2L0FrK9Y8aE3Mum', 'admin', 999999, 999999);

-- Insert sample free user (password: user123)
INSERT INTO `accounts` (`user_id`, `email`, `password_hash`, `user_type`) VALUES
('USER01', 'user@example.com', '$2y$12$example_hash_here', 'free');
*/
