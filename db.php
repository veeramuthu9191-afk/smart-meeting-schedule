<?php
/**
 * Global DB connection wrapper
 */
require_once __DIR__ . '/database/connection.php';

if (!$conn) {
    die("Database Connection Failed: " . ($db_connection_error ?? mysqli_connect_error()));
}