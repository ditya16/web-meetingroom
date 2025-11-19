<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'reservasi');

// Application Configuration
define('APP_NAME', 'Room Booking System');
define('APP_URL', 'http://localhost/room');

// Timezone
date_default_timezone_set('Asia/Jakarta');

// Error Reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>