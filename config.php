<?php
// ---------------------------------------------------------
// Kettlewood Coffee Co. — Database Configuration
// Edit these 4 values to match your local MySQL setup
// ---------------------------------------------------------
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'kettlewood');

define('SITE_NAME', 'Kettlewood Coffee Co.');
define('CURRENCY', 'Rs. ');
define('SHIPPING_FEE', 150.00);
define('FREE_SHIPPING_THRESHOLD', 3000.00);

// Base URL — change if you deploy in a subfolder, e.g. '/kettlewood'
define('BASE_URL', '/kettlewood-coffee-store/kettlewood');

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$conn) {
    die('Database connection failed: ' . mysqli_connect_error() .
        '<br>Check your credentials in config.php and make sure you imported schema.sql');
}

mysqli_set_charset($conn, 'utf8mb4');

$users_table_exists = mysqli_query($conn, "SHOW TABLES LIKE 'users'");
if ($users_table_exists && mysqli_num_rows($users_table_exists) > 0) {
    $user_columns = [];
    $col_result = mysqli_query($conn, 'SHOW COLUMNS FROM users');
    while ($col = mysqli_fetch_assoc($col_result)) {
        $user_columns[$col['Field']] = true;
    }

    if (empty($user_columns['phone'])) {
        mysqli_query($conn, "ALTER TABLE users ADD COLUMN phone VARCHAR(30) DEFAULT NULL");
    }
    if (empty($user_columns['address'])) {
        mysqli_query($conn, "ALTER TABLE users ADD COLUMN address VARCHAR(255) DEFAULT NULL");
    }
    if (empty($user_columns['city'])) {
        mysqli_query($conn, "ALTER TABLE users ADD COLUMN city VARCHAR(100) DEFAULT NULL");
    }
    if (empty($user_columns['postal_code'])) {
        mysqli_query($conn, "ALTER TABLE users ADD COLUMN postal_code VARCHAR(20) DEFAULT NULL");
    }
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}