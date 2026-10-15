<?php
/**
 * Smart Meeting System - Database Connection
 * Production & Localhost Compatible
 */

require_once __DIR__ . '/config.php';

mysqli_report(MYSQLI_REPORT_OFF);

// Try primary configured connection
$conn = @mysqli_connect($db_host, $db_user, $db_pass, $db_name, $db_port);

// If running locally and primary failed, attempt local fallback ports/hosts
if (!$conn && ($db_host === 'localhost' || $db_host === '127.0.0.1')) {
    // Try 127.0.0.1
    $conn = @mysqli_connect('127.0.0.1', $db_user, $db_pass, $db_name, $db_port);
    
    // Try port 3308 (if XAMPP MariaDB was started on 3308)
    if (!$conn) {
        $conn = @mysqli_connect('127.0.0.1', 'root', '', $db_name, 3308);
    }
}

if (!$conn) {
    $db_connection_error = mysqli_connect_error();
    // Do not terminate if used via require, but log error
    error_log("Database connection failed: " . $db_connection_error);
} else {
    // Set charset to utf8mb4 for full emoji & multilingual support
    mysqli_set_charset($conn, "utf8mb4");
}
