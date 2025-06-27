<?php
/**
 * CodeIgniter 4 Cleanup Script
 * Run this script to clean up cache, logs, and temporary files
 * 
 * Usage: php cleanup.php
 */

echo "Starting CodeIgniter 4 cleanup...\n";

// Define paths
$basePath = __DIR__;
$writablePath = $basePath . '/writable';

// 1. Clean debugbar files
$debugbarPath = $writablePath . '/debugbar';
if (is_dir($debugbarPath)) {
    $files = glob($debugbarPath . '/debugbar_*.json');
    $count = 0;
    foreach ($files as $file) {
        if (unlink($file)) {
            $count++;
        }
    }
    echo "Removed {$count} debugbar files\n";
}

// 2. Clean old session files (older than 24 hours)
$sessionPath = $writablePath . '/session';
if (is_dir($sessionPath)) {
    $files = glob($sessionPath . '/ci_session*');
    $count = 0;
    $cutoff = time() - (24 * 60 * 60); // 24 hours ago
    
    foreach ($files as $file) {
        if (filemtime($file) < $cutoff && unlink($file)) {
            $count++;
        }
    }
    echo "Removed {$count} old session files\n";
}

// 3. Clean old log files (keep last 7 days)
$logsPath = $writablePath . '/logs';
if (is_dir($logsPath)) {
    $files = glob($logsPath . '/log-*.log');
    $count = 0;
    $cutoff = time() - (7 * 24 * 60 * 60); // 7 days ago
    
    foreach ($files as $file) {
        if (filemtime($file) < $cutoff && unlink($file)) {
            $count++;
        }
    }
    echo "Removed {$count} old log files\n";
}

// 4. Clean cache directory (if it has files)
$cachePath = $writablePath . '/cache';
if (is_dir($cachePath)) {
    $files = glob($cachePath . '/*');
    $count = 0;
    foreach ($files as $file) {
        if (is_file($file) && basename($file) !== 'index.html' && unlink($file)) {
            $count++;
        }
    }
    if ($count > 0) {
        echo "Removed {$count} cache files\n";
    }
}

// 5. Calculate space saved
function formatBytes($size, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
        $size /= 1024;
    }
    return round($size, $precision) . ' ' . $units[$i];
}

// Get current writable directory size
function getDirSize($dir) {
    $size = 0;
    $files = glob($dir . '/{,.}*', GLOB_BRACE);
    foreach ($files as $file) {
        if (is_file($file)) {
            $size += filesize($file);
        } elseif (is_dir($file) && !in_array(basename($file), ['.', '..'])) {
            $size += getDirSize($file);
        }
    }
    return $size;
}

$writableSize = getDirSize($writablePath);
echo "Current writable directory size: " . formatBytes($writableSize) . "\n";

echo "Cleanup completed!\n";

// Optional: Show directory breakdown
echo "\nDirectory breakdown:\n";
$directories = ['cache', 'debugbar', 'logs', 'session', 'uploads'];
foreach ($directories as $dir) {
    $dirPath = $writablePath . '/' . $dir;
    if (is_dir($dirPath)) {
        $size = getDirSize($dirPath);
        echo "- {$dir}: " . formatBytes($size) . "\n";
    }
}
?>
