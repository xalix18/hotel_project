<?php
// db.php - Database Connection and Settings Loader

// --- Database Configuration ---
// Replace with your actual database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root'); // Your database username
define('DB_PASS', '');     // Your database password
define('DB_NAME', 'hotel_db'); // The database name you created

// --- Establish Connection ---
try {
    // Create a new PDO instance
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
    
    // Set PDO to throw exceptions on error
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Set default fetch mode to associative array
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // If connection fails, stop the script and show an error message.
    // In a real production environment, you would log this error instead of showing it to the user.
    die("خطا در اتصال به پایگاه داده: " . $e->getMessage());
}

// --- Load Site Settings ---
// This function fetches all settings from the 'settings' table and returns them as an associative array.
// This is efficient as it queries the database only once per page load to get all settings.
function get_site_settings($pdo) {
    try {
        $stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
        $settings_raw = $stmt->fetchAll();
        
        $settings = [];
        foreach ($settings_raw as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        return $settings;
    } catch (PDOException $e) {
        // Handle potential errors during the query
        die("خطا در بارگذاری تنظیمات سایت: " . $e->getMessage());
    }
}

// Load settings into a global variable for easy access throughout the site
$settings = get_site_settings($pdo);

?>
