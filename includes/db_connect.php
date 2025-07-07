<?php // /includes/db_connect.php - Database Connection File ?>
<?php

// --- Database Configuration ---
define('DB_HOST', 'localhost'); // or your database host
define('DB_NAME', 'hotel_db');    // your database name
define('DB_USER', 'root');        // your database username
define('DB_PASS', '');            // your database password

// --- Data Source Name (DSN) ---
$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";

// --- PDO Options ---
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Throw exceptions on errors
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Fetch associative arrays
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Use native prepared statements
];

// --- Create PDO Instance ---
try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    // In a real application, you would log this error and show a generic message.
    // Never expose detailed database errors to the public.
    die("خطا در اتصال به پایگاه داده: " . $e->getMessage());
}
