<?php
/**
 * Database Configuration for Smart Meeting System
 * Supports both Localhost (XAMPP) and Free Cloud Hosting (InfinityFree, Alwaysdata, etc.)
 */

// Check if credentials are provided via environment variables (standard for cloud platforms)
$db_host = getenv('DB_HOST') ?: 'localhost';
$db_user = getenv('DB_USER') ?: 'root';
$db_pass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';
$db_name = getenv('DB_NAME') ?: 'smart_meeting_db';
$db_port = (int)(getenv('DB_PORT') ?: 3306);

// Check if production credentials file exists (optional override file for free hosting)
$prodConfigFile = __DIR__ . '/production_config.php';
if (file_exists($prodConfigFile)) {
    include $prodConfigFile;
}
