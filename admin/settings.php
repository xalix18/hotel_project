<?php
// admin/settings.php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index');
    exit;
}

require_once '../includes/db_connect.php';

$feedback_message = '';
$feedback_type = '';
$upload_dir = '../uploads/';

// --- Fetch all current settings first ---
try {
    $stmt = $pdo->query("SELECT * FROM settings");
    $settings_array = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
} catch (PDOException $e) {
    die("خطا در دریافت تنظیمات: " . $e->getMessage());
}


// --- Handle Form Submission ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $settings_to_update = $_POST['settings'];

    // --- Handle Hero Image Upload ---
    if (isset($_FILES['hero_image']) && $_FILES['hero_image']['error'] === UPLOAD_ERR_OK) {
        $image_file = $_FILES['hero_image'];
        
        // Create a unique filename
        $filename = 'hero_' . uniqid() . '.' . pathinfo($image_file['name'], PATHINFO_EXTENSION);
        $destination = $upload_dir . $filename;

        // Move the new file to the uploads directory
        if (move_uploaded_file($image_file['tmp_name'], $destination)) {
            // Get the old image path to delete it later
            $old_image_path = $settings_array['hero_image_url'] ?? null;

            // Set the new path to be saved in the database
            $settings_to_update['hero_image_url'] = 'uploads/' . $filename;
            
            // Delete the old hero image file if it exists and is not a placeholder
            if ($old_image_path && strpos($old_image_path, 'uploads/') === 0 && file_exists('../' . $old_image_path)) {
                unlink('../' . $old_image_path);
            }
        } else {
            $feedback_message = 'خطا در آپلود تصویر جدید.';
            $feedback_type = 'error';
        }
    }

    // --- Update all settings in the database ---
    if (empty($feedback_message)) { // Proceed only if there was no upload error
        try {
            $pdo->beginTransaction();
            $sql = "UPDATE settings SET setting_value = :value WHERE setting_key = :key";
            $stmt = $pdo->prepare($sql);

            foreach ($settings_to_update as $key => $value) {
                $stmt->execute([':value' => trim($value), ':key' => $key]);
            }

            $pdo->commit();
            $feedback_message = 'تنظیمات با موفقیت ذخیره شد.';
            $feedback_type = 'success';
            
            // Refresh settings array to show the new values immediately
            $stmt = $pdo->query("SELECT * FROM settings");
            $settings_array = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

        } catch (PDOException $e) {
            $pdo->rollBack();
            $feedback_message = 'خطا در ذخیره تنظیمات.';
            $feedback_type = 'error';
        }
    }
}

$page_title = 'تنظیمات وب‌سایت';
include 'includes/header.php';
?>

<div class="main-content">
    <h1>تنظیمات وب‌سایت</h1>
    <p>در این بخش می‌توانید اطلاعات اصلی وب‌سایت را ویرایش کنید.</p>

    <?php if (!empty($feedback_message)): ?>
        <div class="feedback-banner <?php echo $feedback_type; ?>">
            <?php echo htmlspecialchars($feedback_message); ?>
        </div>
    <?php endif; ?>

    <div class="form-container">
        <!-- Add enctype for file uploads -->
        <form action="settings" method="POST" enctype="multipart/form-data">
            <h2>اطلاعات اصلی هتل</h2>
            <div class="input-group"><label for="hotel_name">نام هتل</label><input type="text" id="hotel_name" name="settings[hotel_name]" value="<?php echo htmlspecialchars($settings_array['hotel_name'] ?? ''); ?>"></div>
            <div class="input-group"><label for="hotel_address">آدرس</label><input type="text" id="hotel_address" name="settings[hotel_address]" value="<?php echo htmlspecialchars($settings_array['hotel_address'] ?? ''); ?>"></div>
            <div class="input-group"><label for="hotel_phone">تلفن</label><input type="text" id="hotel_phone" name="settings[hotel_phone]" value="<?php echo htmlspecialchars($settings_array['hotel_phone'] ?? ''); ?>"></div>
            <div class="input-group"><label for="hotel_email">ایمیل</label><input type="email" id="hotel_email" name="settings[hotel_email]" value="<?php echo htmlspecialchars($settings_array['hotel_email'] ?? ''); ?>"></div>
            
            <hr class="form-divider">

            <h2>محتوای صفحه اصلی</h2>
            <div class="input-group"><label for="hotel_welcome_title">عنوان خوش‌آمدگویی اصلی</label><input type="text" id="hotel_welcome_title" name="settings[hotel_welcome_title]" value="<?php echo htmlspecialchars($settings_array['hotel_welcome_title'] ?? ''); ?>"></div>
            <div class="input-group"><label for="hotel_welcome_subtitle">زیرعنوان خوش‌آمدگویی</label><textarea id="hotel_welcome_subtitle" name="settings[hotel_welcome_subtitle]" rows="3"><?php echo htmlspecialchars($settings_array['hotel_welcome_subtitle'] ?? ''); ?></textarea></div>
            
            <div class="input-group full-width">
                <label>تصویر پس‌زمینه فعلی</label>
                <img src="../<?php echo htmlspecialchars($settings_array['hero_image_url'] ?? ''); ?>" alt="Current hero image" style="max-width: 300px; border-radius: 8px; margin-top: 10px;">
            </div>
            <div class="input-group full-width">
                <label for="hero_image">آپلود تصویر پس‌زمینه جدید (Hero Image)</label>
                <p style="font-size: 0.9rem; color: #777; margin-top: -5px; margin-bottom: 10px;">با آپلود تصویر جدید، تصویر فعلی جایگزین خواهد شد.</p>
                <input type="file" id="hero_image" name="hero_image">
                <!-- Keep hidden input to pass old value if no new file is uploaded -->
                <input type="hidden" name="settings[hero_image_url]" value="<?php echo htmlspecialchars($settings_array['hero_image_url'] ?? ''); ?>">
            </div>

            <button type="submit" class="btn-submit">ذخیره تنظیمات</button>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
