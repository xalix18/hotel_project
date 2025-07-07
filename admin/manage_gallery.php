<?php
// admin/manage_gallery.php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index');
    exit;
}

require_once '../includes/db_connect.php';

$feedback_message = '';
$feedback_type = '';
$upload_dir = '../uploads/';

// --- Handle Image Upload ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['gallery_image'])) {
    $image_file = $_FILES['gallery_image'];
    $alt_text = trim($_POST['alt_text']) ?: 'Hotel gallery image';

    // Basic validation
    if ($image_file['error'] === UPLOAD_ERR_OK) {
        $file_type = mime_content_type($image_file['tmp_name']);
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

        if (in_array($file_type, $allowed_types)) {
            // Create a unique filename to prevent overwriting
            $filename = uniqid('gallery_', true) . '.' . pathinfo($image_file['name'], PATHINFO_EXTENSION);
            $destination = $upload_dir . $filename;

            if (move_uploaded_file($image_file['tmp_name'], $destination)) {
                // Save to database
                try {
                    $sql = "INSERT INTO gallery (image_filename, alt_text) VALUES (:filename, :alt_text)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([':filename' => $filename, ':alt_text' => $alt_text]);
                    $feedback_message = 'تصویر با موفقیت آپلود و به گالری اضافه شد.';
                    $feedback_type = 'success';
                } catch (PDOException $e) {
                    $feedback_message = 'خطا در ذخیره اطلاعات تصویر در پایگاه داده.';
                    $feedback_type = 'error';
                    unlink($destination); // Delete uploaded file if DB insert fails
                }
            } else {
                $feedback_message = 'خطا در انتقال فایل آپلود شده.';
                $feedback_type = 'error';
            }
        } else {
            $feedback_message = 'نوع فایل مجاز نیست. لطفاً از فرمت‌های JPG, PNG, GIF یا WEBP استفاده کنید.';
            $feedback_type = 'error';
        }
    } else {
        $feedback_message = 'خطا در آپلود فایل. لطفاً دوباره تلاش کنید.';
        $feedback_type = 'error';
    }
}

// --- Handle Image Deletion ---
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    try {
        // First, get the filename to delete the file from the server
        $stmt = $pdo->prepare("SELECT image_filename FROM gallery WHERE id = :id");
        $stmt->execute([':id' => $delete_id]);
        $filename_to_delete = $stmt->fetchColumn();

        // Then, delete the record from the database
        $delete_stmt = $pdo->prepare("DELETE FROM gallery WHERE id = :id");
        $delete_stmt->execute([':id' => $delete_id]);

        // If DB deletion is successful, delete the file
        if ($filename_to_delete && file_exists($upload_dir . $filename_to_delete)) {
            unlink($upload_dir . $filename_to_delete);
        }
        
        $feedback_message = 'تصویر با موفقیت حذف شد.';
        $feedback_type = 'success';
    } catch (PDOException $e) {
        $feedback_message = 'خطا در حذف تصویر.';
        $feedback_type = 'error';
    }
}

// --- Fetch all gallery images ---
$gallery_images = $pdo->query("SELECT * FROM gallery ORDER BY uploaded_at DESC")->fetchAll();

$page_title = 'مدیریت گالری';
include 'includes/header.php';
?>

<div class="main-content">
    <h1>مدیریت گالری تصاویر</h1>

    <?php if (!empty($feedback_message)): ?>
        <div class="feedback-banner <?php echo $feedback_type; ?>">
            <?php echo htmlspecialchars($feedback_message); ?>
        </div>
    <?php endif; ?>

    <!-- Upload New Image Form -->
    <div class="form-container">
        <h2>آپلود تصویر جدید</h2>
        <form action="manage_gallery" method="POST" enctype="multipart/form-data">
            <div class="form-grid">
                <div class="input-group">
                    <label for="gallery_image">فایل تصویر *</label>
                    <input type="file" id="gallery_image" name="gallery_image" required>
                </div>
                <div class="input-group">
                    <label for="alt_text">متن جایگزین (Alt Text)</label>
                    <input type="text" id="alt_text" name="alt_text" placeholder="مثال: لابی هتل در شب">
                </div>
            </div>
            <button type="submit" class="btn-submit">آپلود</button>
        </form>
    </div>

    <!-- List of Existing Images -->
    <div class="table-container">
        <h2>تصاویر موجود در گالری</h2>
        <div class="image-gallery-management">
            <?php if (empty($gallery_images)): ?>
                <p>هیچ تصویری در گالری وجود ندارد.</p>
            <?php else: ?>
                <?php foreach ($gallery_images as $image): ?>
                <div class="image-thumb">
                    <img src="<?php echo $upload_dir . htmlspecialchars($image['image_filename']); ?>" alt="<?php echo htmlspecialchars($image['alt_text']); ?>">
                    <a href="manage_gallery?delete_id=<?php echo $image['id']; ?>" class="delete-image-btn" onclick="return confirm('آیا از حذف این تصویر اطمینان دارید؟');" title="حذف تصویر">&times;</a>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
